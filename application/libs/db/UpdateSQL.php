<?php

namespace db;

use \Database as Database;
use \Localized as Localized;
use \Logger as Logger;
use \Model as Model;
use \SQL as SQL;

class UpdateSQL extends SQL
{
	public $model;
	public $data;
	public $allowFullTableUpdate = false;

    public function __construct(Model $model, Qualifier $qualifier = null, array $changes = null)
    {
    	parent::__construct();
    	if ( is_null($model) ) {
    		throw new \Exception( "Must specfify the model to insert into .. " );
    	}
    	if ( is_null($changes) ) {
    		throw new \Exception( "Must specfify the data changes for update .. " );
    	}

    	$this->model = $model;
    	$this->data = $changes;
    	if ( isset($qualifier) && is_null($qualifier) == false) {
        	$this->where($qualifier);
        }

    	return $this;
    }

	private function rowValues( $idx = 0, array $row)
	{
		$idx = (is_int($idx) ? intval($idx) : 0);
		$prefix = SQL::PrefixAlias($idx);
		$map = array();
		foreach( $this->columns as $columnName ) {
			if ( isset( $row[$columnName] ) ) {
				$placeholder = ":" . $prefix . "_" . $columnName;
				$map[$placeholder] = $row[$columnName];
			}
		}
		return $map;
	}

	private function rowPlaceholders( $idx = 0, array $row )
	{
		$idx = (is_int($idx) ? intval($idx) : 0);
		$prefix = SQL::PrefixAlias($idx);
		$map = array();
		foreach( $this->columns as $columnName ) {
			if ( isset( $row[$columnName] ) ) {
				$map[] = ":" . $prefix . "_" . $columnName;
			}
			else {
				$map[] = 'null';
			}
		}
		return $map;
	}

	public function sqlParameters()
	{
		$params = array();
		foreach ($this->data as $key => $value) {
			$pKey = ':' . sanitize($key, true, true);
			$params[$pKey] = $value;
		}

		if (isset($this->qualifier) ) {
			$params = array_merge($params, $this->qualifier->sqlParameters() );
		}

		return $params;
	}

	public function sqlStatement()
	{
		if ( is_null($this->data) || count($this->data) == 0 ) {
    		throw new \Exception( "Must specfify the data to be updated into " . $this->model );
    	}

		$components = array(
			SQL::CMD_UPDATE,
			$this->model->tableName(),
			SQL::SQL_SET
		);

		$placeholders = array();
		foreach ($this->data as $key => $value) {
			$placeholders[] = $key . ' = :' . sanitize($key, true, true);
		}
		$components[] = implode(',', $placeholders);

		if ( isset($this->qualifier) ) {
			$components[] = SQL::SQL_WHERE;
			$components[] = $this->qualifier->sqlStatement();
		}

		return implode(" ", $components );
	}

	public function commitTransaction()
	{
		if ( isset($this->qualifier) == false && $this->allowFullTableUpdate != true ) {
			throw new \Exception("No qualifier set on update statement '" .$this->sqlStatement(). "'");
		}

		$sql = $this->sqlStatement();
		$params = $this->sqlParameters();

		$db = Database::instance();
		$statement = $db->prepare($sql);
		if ($statement ) {
			$affectedRows = $statement->execute($params);
			if ( $affectedRows > 0 ) {
				return true;
			}
		}

		$caller = callerClassAndMethod('commitTransaction');
		$errPoint = ($statement ? $statement : Database::instance());
		$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
		$this->reportSQLError($caller['class'], $caller['function'], $errPoint->errorCode(), $pdoError, $sql, $params);

		return false;
	}
}
