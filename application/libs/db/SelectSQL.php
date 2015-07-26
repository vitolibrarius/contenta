<?php

namespace db;

use \PDO as PDO;
use \Database as Database;
use \DataObject as DataObject;
use \Localized as Localized;
use \Logger as Logger;
use \Model as Model;
use \SQL as SQL;

class SelectSQL extends SQL
{
	public $model;
	public $columns;
	public $order;
	public $group;
	public $limit = 50;

    public function __construct(Model $model, array $columns = null, Qualifier $qualifier = null)
    {
    	parent::__construct();
    	if ( is_null($model) ) {
    		throw new \Exception( "Must specfify the model to selct from .. " );
    	}

    	if ( is_null($columns) || count($columns) == 0 ) {
    		$columns = $model->allColumnNames();
    	}

    	$this->model = $model;
    	$this->columns = $columns;

    	if ( isset($qualifier) && is_null($qualifier) == false) {
        	$this->where($qualifier);
        }
    	return $this;
    }

    public function objectBasedSelect()
    {
		if ( isset($this->columns) ) {
			$modelColumns = $this->model->allColumnNames();
			if ( count($this->columns) == count($modelColumns) ) {
				$diff = array_diff($this->columns, $modelColumns);
				return (count($diff) == 0);
			}
		}
		return false;
    }

	public function orderby(array $order = null)
	{
		$this->order = $order;
		return $this;
	}

	public function groupby(array $group = null)
	{
		$this->group = $group;
		return $this;
	}

	public function limit( $limit = 50 )
	{
		$this->limit = $limit;
		return $this;
	}

	public function sqlParameters()
	{
		return (isset($this->qualifier) ? $this->qualifier->sqlParameters() : null);
	}

	public function sqlStatement()
	{
		$components = array(
			SQL::CMD_SELECT,
			implode(",", $this->columns),
			SQL::SQL_FROM,
			$this->model->tableName(),
		);

		if ( isset($this->qualifier) ) {
			$components[] = SQL::SQL_WHERE;
			$components[] = $this->qualifier->sqlStatement();
		}

		if ( isset($this->group) ) {
			$components[] = SQL::SQL_GROUP;
			$components[] = implode(",", $this->group);
		}

		if ( isset($this->order) ) {
			$components[] = SQL::SQL_ORDER;
			$orderClauses = array();
			foreach( $this->order as $clause ) {
				if ( is_array($clause) ) {
					if ( count($clause) != 1 ) {
						throw new \Exception( "Unable to parse order clause " . var_export($clause, true) );
					}
					foreach( $clause as $direction => $col ) {
						if ( strtoupper($direction) === SQL::SQL_ORDER_DESC) {
							$orderClauses[] = $col . ' ' . SQL::SQL_ORDER_DESC;
						}
						else {
							$orderClauses[] = $col;
						}
					}
				}
				else if ( is_string($clause) ) {
					$orderClauses[] = $clause;
				}
				else {
					throw new \Exception( "Unable to order '$clause'" );
				}
			}
			$components[] = implode(",", $orderClauses);
		}

		if ( isset($this->limit) && intval($this->limit) > 0 ) {
			$components[] = "LIMIT " . $this->limit;
		}

		return implode(" ", $components );
	}

	public function fetch()
	{
		$sql = $this->sqlStatement();
		$params = $this->sqlParameters();

		$statement = Database::instance()->prepare($sql);
		if ($statement && $statement->execute($params)) {
			try {
				if ( isset($this->model) && $this->objectBasedSelect() ) {
					$dboClassName = DataObject::NameForModel($this->model);
					if (class_exists($dboClassName)) {
						return $statement->fetchObject($dboClassName);
					}
				}
				else {
					return $statement->fetch();
				}
			}
			catch ( \ClassNotFoundException $e ) {
				Logger::logException( $e );
				return $statement->fetch();
			}
		}

		$caller = callerClassAndMethod('fetch');
		$errPoint = ($statement ? $statement : Database::instance());
		$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
		$this->reportSQLError($caller['class'], $caller['function'], $errPoint->errorCode(), $pdoError, $sql, $params);

		return false;
	}

	public function fetchAll()
	{
		$sql = $this->sqlStatement();
		$params = $this->sqlParameters();

		$statement = Database::instance()->prepare($sql);
		if ($statement && $statement->execute($params)) {
			try {
				if ( isset($this->model) && $this->objectBasedSelect() ) {
					$dboClassName = DataObject::NameForModel($this->model);
					if (class_exists($dboClassName)) {
						return $statement->fetchAll(PDO::FETCH_CLASS, $dboClassName);
					}
					else {
						return $statement->fetchAll();
					}
				}
				else {
					return $statement->fetchAll();
				}
			}
			catch ( \ClassNotFoundException $e ) {
				Logger::logException( $e );
				return $statement->fetchAll();
			}
		}

		$caller = callerClassAndMethod('fetchAll');
		$errPoint = ($statement ? $statement : Database::instance());
		$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
		$this->reportSQLError($caller['class'], $caller['function'], $errPoint->errorCode(), $pdoError, $sql, $params);

		return false;
	}
}
