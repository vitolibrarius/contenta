<?php

namespace db;

use \Database as Database;
use \Localized as Localized;
use \Logger as Logger;
use \Model as Model;
use \SQL as SQL;

class InsertSQL extends SQL
{
	public $model;
	public $columns;
	public $dataArray;

    public function __construct(Model $model = null, array $columns = array())
    {
    	parent::__construct();
    	if ( is_null($model) ) {
    		throw new \Exception( "Must specfify the model to insert into .. " );
    	}

    	if ( count($columns) == 0 ) {
    		$columns = $model->allColumnNames();
    		$columns = array_diff($columns, array($model->tablePK()));
    	}

    	$this->model = $model;
    	$this->columns = $columns;
    	return $this;
    }

	public function addRecord( array $record = null )
	{
		$this->dataArray[] = $record;
		return $this;
	}

	public function hasData()
	{
		return ( is_null($this->dataArray) == false && count($this->dataArray) > 0 );
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
		foreach ($this->dataArray as $idx => $row ) {
			$params = array_merge($params, $this->rowValues($idx, $row));
		}

		return $params;
	}

	public function sqlStatement()
	{
		if ( is_null($this->dataArray) || count($this->dataArray) == 0 ) {
    		throw new \Exception( "Must specfify the data to be inserted into " . $this->model );
    	}

		$components = array(
			SQL::CMD_INSERT,
			$this->model->tableName(),
		);

		$components[] = "(" . implode(",", $this->columns ) . ")";
		$components[] = SQL::SQL_VALUES;

		$rowPlaceholders = array();
		foreach ($this->dataArray as $idx => $row ) {
			$rowPlaceholders[] = "(" . implode(",", $this->rowPlaceholders($idx, $row)) . ")";
		}
		$components[] = implode(", ", $rowPlaceholders );

		return implode(" ", $components );
	}

	public function commitTransaction($successOnly = false)
	{
		$sql = $this->sqlStatement();
		$params = $this->sqlParameters();

		$db = Database::instance();
		$statement = $db->prepare($sql);
		if ($statement ) {
			$affectedRows = $statement->execute($params);
			if ( $affectedRows > 0 ) {
				if ( $successOnly == false && count( $this->dataArray ) == 1 ) {
					$tablePk = $this->model->tablePK();
					if (in_array($tablePk, $this->columns)) {
						$rowId = $this->dataArray[0]{$tablePk};
					}
					else {
						$rowId = $db->lastInsertId();
					}

					if ( isset($this->model) ) {
						$select = new SelectSQL($this->model);
						$select->where( Qualifier::Equals($this->model->tablePK(), $rowId) );
						return $select->fetch();
					}
					else {
						return $rowId;
					}
				}
				else {
					return true;
				}
			}
		}

		$caller = callerClassAndMethod('commitTransaction');
		$errPoint = ($statement ? $statement : Database::instance());
		$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
		$this->reportSQLError($caller['class'], $caller['function'], $errPoint->errorCode(), $pdoError, $sql, $params);

		return false;
	}
}
