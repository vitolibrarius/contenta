<?php

require_once SYSTEM_PATH .'application/libs/db/Qualifier.php';

use \Database as Database;
use \Localized as Localized;
use \Logger as Logger;
use \DataObject as DataObject;
use \Model as Model;

/**
 */
abstract class SQL
{
	const CMD_SELECT	= 'SELECT';
	const CMD_INSERT	= 'INSERT INTO';
	const CMD_UPDATE	= 'UPDATE';
	const CMD_DELETE	= 'DELETE';

	const SQL_FROM		= 'FROM';
	const SQL_WHERE		= 'WHERE';
	const SQL_VALUES	= 'VALUES';
	const SQL_SET		= 'SET';
	const SQL_AS		= 'AS';
	const SQL_ON		= 'ON';
	const SQL_JOIN		= 'JOIN';
	const SQL_JOIN_LEFT	= 'LEFT JOIN';

	const SQL_LIKE_BEFORE =	'%%%s';
	const SQL_LIKE_AFTER =	'%s%%';
	const SQL_LIKE_BOTH =	'%%%s%%';

	const SQL_HAVING	= 'HAVING';
	const SQL_GROUP		= 'GROUP BY';
	const SQL_ORDER		= 'ORDER BY';
	const SQL_ORDER_ASC		= 'ASC';
	const SQL_ORDER_DESC	= 'DESC';

	public $qualifier;

	public static function PrefixAlias( $idx = 0 )
	{
		$prefix = '';
		for ( ; $idx >= 0; $idx = intval($idx / 26) - 1) {
			$prefix = chr($idx % 26 + 0x61) . $prefix;
		}
		return $prefix;
	}

	public static function Sum( Model $model, $target, array $columns = null, db\Qualifier $qualifier = null, array $order = null )
	{
		return \SQL::Aggregate( "sum", $model, $target, $columns, $qualifier, $order);
	}

	public static function Count( Model $model, array $columns = null, db\Qualifier $qualifier = null, array $order = null )
	{
		return \SQL::Aggregate( "count", $model, $model->tablePK(), $columns, $qualifier, $order);
	}

	public static function Maximum( Model $model, $target, array $columns = null, db\Qualifier $qualifier = null, array $order = null )
	{
		return \SQL::Aggregate( "max", $model, $target, $columns, $qualifier, $order);
	}

	public static function Minimum( Model $model, $target, array $columns = null, db\Qualifier $qualifier = null, array $order = null )
	{
		return \SQL::Aggregate( "min", $model, $target, $columns, $qualifier, $order);
	}

	public static function Aggregate( $aggregate, Model $model, $target = '*', array $columns = null, db\Qualifier $qualifier = null, array $order = null )
	{
		$groupBy = false;
		$newColumns = array( $aggregate . "(" . $target . ") as " . $aggregate );
		if ( is_array($columns) && count($columns) > 0 ) {
			$newColumns = array_merge($newColumns, $columns);
			$groupBy = true;
		}

		$select = new db\SelectSQL($model, $newColumns, $qualifier);
		if ( $groupBy == true ) {
			$select->groupBy( $columns );
		}
		$select->orderBy( $order );
		return $select;
	}

	public static function Select( Model $model, array $columns = null, db\Qualifier $qualifier = null )
	{
		return new db\SelectSQL($model, $columns, $qualifier);
	}

	public static function SelectObject( Model $model, DataObject $data )
	{
		if ( is_null($data) ) {
			throw new \Exception( "You must specify the data to be selected" );
		}
		return new db\SelectSQL($model, null, db\Qualifier::PK($data));
	}

	public static function SelectJoin( Model $model, array $columns = null, db\Qualifier $qualifier = null )
	{
		return new db\JoinSQL($model, $columns, $qualifier);
	}

	public static function Insert( Model $model, array $columns = array() )
	{
		return new db\InsertSQL($model, $columns);
	}

	public static function InsertRecord( Model $model, array $columns = array(), array $record = null )
	{
		$sql = new db\InsertSQL($model, $columns);
		$sql->addRecord( $record );
		return $sql;
	}

	public static function Delete( Model $model, db\Qualifier $qualifier = null )
	{
		return new db\DeleteSQL($model, $qualifier);
	}

	public static function DeleteObject( DataObject $data = null )
	{
		if ( is_null($data) ) {
			throw new \Exception( "You must specify the data to be deleted" );
		}

		return new db\DeleteSQL($data->model(), db\Qualifier::PK($data));
	}

	public static function Update( Model $model, db\Qualifier $qualifier = null, array $changes = null)
	{
		return new db\UpdateSQL($model, $qualifier, $changes);
	}

	public static function UpdateObject( DataObject $data = null, array $changes = null )
	{
		if ( is_null($data) ) {
			throw new \Exception( "You must specify the data to be updated" );
		}

		return new db\UpdateSQL($data->model(), db\Qualifier::PK($data), $changes);
	}


    public function __construct()
    {
    }

	public function reportSQLError( $clazz = 'Model', $method = 'unknown', $pdocode, $pdoError, $sql, $params = null)
	{
		printMemory( __method__, __line__, 'PDO Error(' . $pdocode . ') ' . $pdoError);
		printBacktrace($sql);
		$msg = 'PDO Error(' . $pdocode . ') ' . $pdoError . ' for [' . $sql . '] ' . (isset($params) ? var_export($params, true) : 'No Parameters');
		Logger::logError($msg, $clazz, $method);
	}


	abstract public function sqlParameters();
	abstract public function sqlStatement();

	public function __toString()
	{
		$str = null;
		try {
			$str = $this->sqlStatement() . ';  ' . var_export($this->sqlParameters(), true);
		}
		catch ( \Exception $e ) {
			$str = get_class($this) . '__toString() : ' . $e;
		}
		return $str;
	}

	public function where( db\Qualifier $qualifier )
	{
		if ( isset( $this->qualifier, $qualifier) ) {
			$this->qualifier = db\Qualifier::AndQualifier( $this->qualifier, $qualifier );
		}
		else {
			$this->qualifier = $qualifier;
		}
		return $this;
	}

	public function whereEqual( $key = null, $value = null )
	{
		return $this->where( db\Qualifier::Equals( $key, $value ) );
	}

	public function whereRelation( $key = null, $value = null )
	{
		return $this->where( db\Qualifier::FK( $key, $value ));
	}

	/** Misc SQL functions */
	public static function raw( $sql = null, array $params = null, $comment = null )
	{
		if ( is_null($sql) ) {
			throw new \Exception("Unable to execute SQL for -null- statement");
		}

		$statement = Database::instance()->prepare($sql);
		if ($statement == false || $statement->execute($params) == false) {
			$errPoint = ($statement ? $statement : Database::instance());
			$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
			Logger::logSQLError("raw", 'sqlite_execute', $errPoint->errorCode(), $pdoError, $sql, $params);
			throw new \Exception("Error executing raw sql");
		}

		if ( is_null($comment) == false) {
			Logger::logInfo( $comment, get_class(), "SQL::raw");
		}

		return $statement->fetchAll();
	}

	public static function pragma_TableInfo($table)
	{
		try {
			$sql = "PRAGMA table_info(" . $table . ")";
			$statement = Database::instance()->prepare($sql);
			if ($statement && $statement->execute()) {
				$table_pragma = $statement->fetchAll();
				if ($table_pragma != false) {
					$table_fields = array();
					foreach($table_pragma as $key => $value) {
						$table_fields[ $value->name ] = $value;
					}
					return $table_fields;
				}
			}
		}
		catch( \Exception $e ) {
		}

		return false;
	}
}
