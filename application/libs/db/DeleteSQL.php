<?php

namespace db;

use \Database as Database;
use \Localized as Localized;
use \Logger as Logger;
use \Model as Model;
use \SQL as SQL;

class DeleteSQL extends SQL
{
	public $model;
	public $allowFullTableDelete = false;

    public function __construct(Model $model, Qualifier $qualifier = null)
    {
    	parent::__construct();
    	if ( is_null($model) ) {
    		throw new \Exception( "Must specfify the model to selct from .. " );
    	}

    	$this->model = $model;
    	if ( isset($qualifier) && is_null($qualifier) == false) {
        	$this->where($qualifier);
        }
    	return $this;
    }

	public function sqlParameters()
	{
		return (isset($this->qualifier) ? $this->qualifier->sqlParameters() : null);
	}

	public function sqlStatement()
	{
		$components = array(
			SQL::CMD_DELETE,
			SQL::SQL_FROM,
			$this->model->tableName(),
		);

		if ( isset($this->qualifier) ) {
			$components[] = SQL::SQL_WHERE;
			$components[] = $this->qualifier->sqlStatement();
		}

		return implode(" ", $components );
	}

	public function commitTransaction()
	{
		if ( isset($this->qualifier) == false && $this->allowFullTableDelete != true ) {
			throw new \Exception("No qualifier set on delete statement '" .$this->sqlStatement(). "'");
		}

		$sql = $this->sqlStatement();
		$params = $this->sqlParameters();

		$db = Database::instance();
		$statement = $db->prepare($sql);
		if ($statement ) {
			$success = $statement->execute($params);
			if ( $success > 0 ) {
				return $statement->rowCount();
			}
		}

		$caller = callerClassAndMethod('commitTransaction');
		$errPoint = ($statement ? $statement : Database::instance());
		$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
		$this->reportSQLError($caller['class'], $caller['function'], $errPoint->errorCode(), $pdoError, $sql, $params);

		return false;
	}
}
