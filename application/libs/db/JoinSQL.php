<?php

namespace db;

use \PDO as PDO;
use \Database as Database;
use \DataObject as DataObject;
use \Localized as Localized;
use \Logger as Logger;
use \Model as Model;
use \SQL as SQL;

class JoinSQL extends SQL
{
	public $initialPrefix;
	public $joinMeta;
	public $order;
	public $limit = 50;

    public function __construct(Model $model, array $columns = null, Qualifier $qualifier = null)
    {
    	parent::__construct($model, $columns, $qualifier);

		$this->joinMeta = array_setValueForKeypath("count", 1);
    	if ( is_null($columns) || count($columns) == 0 ) {
    		$columns = $model->allColumnNames();
    	}

		array_setValueForKeypath("join/".$model->tableName()."/prefix", SQL::PrefixAlias(0), $this->joinMeta);
		array_setValueForKeypath("join/".$model->tableName()."/model", $model, $this->joinMeta);
		array_setValueForKeypath("join/".$model->tableName()."/columns", $columns, $this->joinMeta);
		array_setValueForKeypath("join/".$model->tableName()."/qualifier", $qualifier, $this->joinMeta);

    	return $this;
    }

	public function orderby(Model $model = null, $column, $ascDesc = 'asc' )
	{
		if ( isset($model) == false ) {
			throw new \Exception( " model is required" );
		}

		$sourcePrefix = array_valueForKeypath("join/".$model->tableName()."/prefix", $this->joinMeta );
		if ( is_null( $sourcePrefix) ) {
			throw new \Exception( "source join model must already be specified" );
		}
		if ( isset( $this->order )) {
			$this->order[] = $sourcePrefix . "." . $column . " " . $ascDesc;
		}
		else {
			$this->order = array($sourcePrefix . "." . $column . " " . $ascDesc);
		}
		return $this;
	}

	public function limit( $limit = 50 )
	{
		$this->limit = $limit;
		return $this;
	}

	public function joinOn( Model $source, Model $destination, array $dest_columns = null, Qualifier $dest_qualifier = null )
	{
		if ( isset($source, $destination) == false ) {
			throw new \Exception( "both join models are required" );
		}

		$sourcePrefix = array_valueForKeypath("join/".$source->tableName()."/prefix", $this->joinMeta );
		if ( is_null( $sourcePrefix) ) {
			throw new \Exception( "source join model must already be specified" );
		}

		$lastCount = array_valueForKeypath("count", $this->joinMeta);
		array_setValueForKeypath("count", ($lastCount+1), $this->joinMeta);
		$nextPrefix = SQL::PrefixAlias($lastCount);

		array_setValueForKeypath("join/".$destination->tableName()."/prefix", $nextPrefix, $this->joinMeta);
		array_setValueForKeypath("join/".$destination->tableName()."/model", $destination, $this->joinMeta);
		array_setValueForKeypath("join/".$destination->tableName()."/columns", $dest_columns, $this->joinMeta);
		array_setValueForKeypath("join/".$destination->tableName()."/qualifier", $dest_qualifier, $this->joinMeta);

		list($src, $dest) = $source->joinAttributes( $destination );
    	if ( is_null($src) == false && is_null($dest) == false) {
			array_setValueForKeypath("join/".$destination->tableName()."/joinOn",
				Qualifier::JoinQualifier( $sourcePrefix, $src, $nextPrefix, $dest), $this->joinMeta);
    	}
    	return $this;
	}

    public function objectBasedSelect()
    {
    	$primaryModel = false;
		foreach( $this->allJoinTables() as $tableName ) {
			$columns = array_valueForKeypath("join/".$tableName."/columns", $this->joinMeta);
			if ( is_array($columns) ) {
				if ( $primaryModel == false ) {
					$model = array_valueForKeypath("join/".$tableName."/model", $this->joinMeta);
					$modelColumns = $model->allColumnNames();
					$diff = array_diff($columns, $modelColumns);
					if ( count($columns) == count($modelColumns) && count($diff) == 0 ) {
						$primaryModel = $model;
					}
					else {
						// not all columns selected
						$primaryModel = false;
						break;
					}
				}
				else {
					// columns from other tables exists
					$primaryModel = false;
					break;
				}
			}
		}

		return $primaryModel;
    }

    public function allJoinTables()
    {
    	$joins = array_valueForKeypath("join", $this->joinMeta);
    	return array_keys($joins);
    }

	public function sqlParameters()
	{
		$parameters = array();
		foreach( $this->allJoinTables() as $tableName ) {
			$qual = array_valueForKeypath("join/".$tableName."/qualifier", $this->joinMeta);
			if ( is_null($qual) == false ) {
				$qual->tablePrefix = array_valueForKeypath("join/".$tableName."/prefix", $this->joinMeta);
				$q_params = $qual->sqlParameters();
				if ( is_array($q_params)) {
					$parameters = array_merge($parameters, $q_params);
				}
			}
		}
		return (count($parameters) > 0 ? $parameters : null);
	}

	public function sqlStatement()
	{
		$selectCols = array();
		$from = array();
		$where = array();

		$tableNames = $this->allJoinTables();
		foreach( $tableNames as $tableName ) {
			$join = array_valueForKeypath("join/".$tableName, $this->joinMeta);
			$prefix = $join['prefix'];

			if ( isset($join['columns'])) {
				foreach( $join['columns'] as $col ) {
					$selectCols[] = $prefix . "." . $col;
				}
			}

			$aJoin = $tableName . " " . SQL::SQL_AS . " " . $prefix;
			if ( isset($join['joinOn'])) {
				$aJoin .= " " . SQL::SQL_ON . " " . $join['joinOn']->sqlStatement();
			}
			$from[] = $aJoin;

			if ( isset($join['qualifier'])) {
				$join['qualifier']->tablePrefix = $prefix;
				$where[] = $join['qualifier']->sqlStatement();
			}
		}

		$components = array(
			SQL::CMD_SELECT,
			SQL::SQL_DISTINCT,
			implode(",", $selectCols),
			SQL::SQL_FROM,
			implode("," . PHP_EOL . "	", $from)
		);

		if ( count($where) > 0 ) {
			$components[] = PHP_EOL;
			$components[] = SQL::SQL_WHERE;
			$components[] = implode(" AND ", $where);
		}

		$components[] = PHP_EOL;

		if ( isset($this->order) ) {
			$components[] = PHP_EOL;
			$components[] = SQL::SQL_ORDER;
			$components[] = implode(",", $this->order);
		}

		if ( isset($this->limit) && intval($this->limit) > 0 ) {
			$components[] = PHP_EOL;
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
				$primaryModel = $this->objectBasedSelect();
				if ( $primaryModel instanceof Model ) {
					$dboClassName = $primaryModel->dboName();
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
				$primaryModel = $this->objectBasedSelect();
				if ( $primaryModel instanceof Model ) {
					$dboClassName = $primaryModel->dboName();
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
