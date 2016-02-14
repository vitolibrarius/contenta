<?php

namespace utilities;

use \PDO as PDO;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Metadata as Metadata;

use \SQL as SQL;
use db\SelectSQL as SelectSQL;

class Metadata_sqlite extends Metadata implements \MetadataInterface
{
	const DefaultFilename = 'metadata.sqlite';

	function __construct($fullpath)
	{
		parent::__construct($fullpath);

		$this->initializeSchema();
	}

	public function database()
	{
		$database = new PDO("sqlite:" . $this->fullpath());
    	$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$database->exec( 'PRAGMA foreign_keys = ON;' );
		$database->exec( 'PRAGMA busy_timeout = 10000;' );
		return $database;
	}

	public static function hasMetadataFile($path, $filename)
	{
		if ( is_dir($path) ) {
			$file = ((isset($filename) && strlen($filename) > 0) ? $filename : Metadata_sqlite::DefaultFilename);
			return file_exists(appendPath($path, $file));
		}
		return false;
	}

	public function metaCount($key = null)
	{
		$sql = "select COUNT(*) as COUNT from PRIMITIVE";
		$params = null;

		$normal = normalizePath( $key, null, null, true, true);
		if ( is_null($normal) == false && strlen($normal) > 0) {
			$sql .= " where keypath like :k";
			$params = array( ":k" => $normal . "%" );
		}

		$database = $this->database();
		$statement = $database->prepare($sql);
		if ($statement == false || $statement->execute($params) == false) {
			$errPoint = ($statement ? $statement : $database);
			$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
			Logger::logSQLError($sql, 'sqlite_execute', $errPoint->errorCode(), $pdoError, $sql, $params);
			$database = null;
			throw new \Exception("Error executing change to " . $sql);
		}

		$value = $statement->fetch();
		$database = null;
		if ( $value != null ) {
			return $value['COUNT'];
		}
		return 0;
	}

	/**
	 * sets a specific value to a specific key of the session
	 * @param mixed $key
	 * @param mixed $value
	 */
	public function setMeta($key = '', $value = null)
	{
		$success = false;
		$stopwatch = Stopwatch::start( uuid() );

		if ( isset($key) && strlen($key) > 0) {
			$success = $this->sqlite_deleteValueForKeypath($key);
			if ( $success ) {
				if ( is_array($value) || is_object($value) ) {
					foreach( $value as $k => $v ) {
						$this->setMeta( appendPath( $key, $k ), $v );
					}
				}
				else if ( is_int($value) || is_bool($value) || is_double($value) || is_string($value) ) {
					$success = $this->sqlite_setValueForKeypath($key, $value);
				}
			}
		}

		$elapsed = Stopwatch::end( $stopwatch );
		if ( $elapsed > 1.0 ) {
			$vmsg = "";
			if ( is_array($value) || is_object($value) ) {
				$vmsg = gettype($value) . " count(" . count($value) .")";
			}
			else {
				$vmsg = var_export($value, true);
			}
		}

		return $success;
	}

	/**
	 * gets/returns the value of a specific key of the metadata
	 * @param mixed $key Usually a string, path may be separated using '/', so 'source/subkey/itemkey'
	 * @return mixed
	 */
	public function getMeta($key = null, $default = null)
	{
		$value = null;

		$stopwatch = Stopwatch::start( uuid() );

		if ( isset($key) && strlen($key) > 0) {
			$value = $this->sqlite_valueForKeypath( $key );
		}

		$elapsed = Stopwatch::end( $stopwatch );
		if ( $elapsed > 1.0 ) {
			$vmsg = "";
			if ( is_array($value) || is_object($value) ) {
				$vmsg = gettype($value) . " count(" . count($value) .")";
			}
			else {
				$vmsg = var_export($value, true);
			}
		}

		return (is_null($value) ? $default : $value);
	}

	private function sqlite_execute( $sql = null, $params = null )
	{
		if ( is_null($sql) ) {
			throw new \Exception("Unable to execute SQL for -null- statement");
		}
		else {
			$database = $this->database();
			$statement = $database->prepare($sql);
			if ($statement == false || $statement->execute($params) == false) {
				$errPoint = ($statement ? $statement : $database);
				$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
				Logger::logSQLError($sql, 'sqlite_execute', $errPoint->errorCode(), $pdoError, $sql, $params);
				$database = null;
				throw new \Exception("Error executing change to " . $sql);
			}
			$database = null;
		}
	}

	private function sqlite_record_value( $record )
	{
		if ( is_null( $record['int_val'] ) == false ) {
			return $record['int_val'];
		}
		else if ( is_null( $record['real_val'] ) == false ) {
			return $record['real_val'];
		}
		else if ( is_null( $record['str_val'] ) == false ) {
			return $record['str_val'];
		}
		else if ( is_null( $record['bool_val'] ) == false ) {
			return boolval($record['bool_val']);
		}
		else if ( is_null( $record['date_val'] ) == false ) {
			return $record['date_val'];
		}
		return null;
	}

	private function sqlite_valueForKeypath( $keypath )
	{
		$normal = normalizePath( $keypath, null, null, true, true);
		if ( is_null($normal) == false && strlen($normal) > 0) {
			$sql = "select keypath, int_val, real_val, str_val, bool_val, date_val from PRIMITIVE where keypath = :k";
			$params = array( ":k" => $normal );

			$database = $this->database();
			$statement = $database->prepare($sql);
			if ($statement == false || $statement->execute($params) == false) {
				$errPoint = ($statement ? $statement : $database);
				$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
				Logger::logSQLError($sql, 'sqlite_execute', $errPoint->errorCode(), $pdoError, $sql, $params);
				$database = null;
				throw new \Exception("Error executing change to " . $sql);
			}

			$value = $statement->fetch();
			if ( $value != null ) {
				$database = null;
				return $this->sqlite_record_value( $value );
			}
			else {
				$sql = "select keypath, int_val, real_val, str_val, bool_val, date_val from PRIMITIVE where keypath like :k";
				$params = array( ":k" => $normal . "%" );

				$statement = $database->prepare($sql);
				if ($statement == false || $statement->execute($params) == false) {
					$errPoint = ($statement ? $statement : $database);
					$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
					Logger::logSQLError($sql, 'sqlite_execute', $errPoint->errorCode(), $pdoError, $sql, $params);
					$database = null;
					throw new \Exception("Error executing change to " . $sql);
				}

				$value = $statement->fetchAll();
				if ( $value != null ) {
					$results = array();
					foreach( $value as $record ) {
						$k = $record['keypath'];
						if (substr($k, 0, strlen($normal)) == $normal) {
							$k = substr($k, strlen($normal));
						}
						$v = $this->sqlite_record_value( $record );
						$results = array_setValueForKeypath($k, $v, $results);
					}
					$database = null;
					return $results;
				}
			}
			$database = null;
		}
		return null;
	}

	private function sqlite_setValueForKeypath( $keypath, $value )
	{
		$normal = normalizePath( $keypath, null, null, true, true);
		if ( is_null($normal) == false && strlen($normal) > 0) {
			$sql = "insert or replace into PRIMITIVE (keypath, int_val, real_val, str_val, bool_val, date_val) "
				. "values (:path, :int_val, :real_val, :str_val, :bool_val, :date_val)";
			$params = array(
				":path" => $normal,
				":int_val" => null,
				":real_val" => null,
				":str_val" => null,
				":bool_val" => null,
				":date_val" => null
			);

			switch( gettype( $value ) ) {
				case "boolean":
					$params[":bool_val"] = (($value)? 1 : 0);
					break;

				case "integer":
					$params[":int_val"] = $value;
					break;

				case "double":
					$params[":real_val"] = $value;
					break;

				case "string":
					$params[":str_val"] = $value;
					break;

				default:
					throw new \Exception( "cannot setMeta($key, ?) for " . var_export($value, true));
			}

			$database = $this->database();
			$statement = $database->prepare($sql);
			if ($statement == false || $statement->execute($params) == false) {
				$errPoint = ($statement ? $statement : $database);
				$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
				Logger::logSQLError($sql, 'sqlite_execute', $errPoint->errorCode(), $pdoError, $sql, $params);
				$database = null;
				throw new \Exception("Error executing change to " . $sql);
			}
			$database = null;
			return true;
		}
		return false;
	}

	private function sqlite_deleteValueForKeypath( $keypath )
	{
		$normal = normalizePath( $keypath, null, null, true, true);
		if ( is_null($normal) == false && strlen($normal) > 0) {
			$sql = "delete from PRIMITIVE where keypath like :k";
			$params = array( ":k" => $normal . "%" );

			$database = $this->database();
			$statement = $database->prepare($sql);
			if ($statement == false || $statement->execute($params) == false) {
				$errPoint = ($statement ? $statement : $database);
				$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
				Logger::logSQLError($sql, 'sqlite_execute', $errPoint->errorCode(), $pdoError, $sql, $params);
				$database = null;
				throw new \Exception("Error executing change to " . $sql);
			}

			$database = null;
			return true;
		}
		return false;
	}

	public function initializeSchema()
	{
		$sql = "CREATE TABLE IF NOT EXISTS PRIMITIVE ( "
			. "keypath TEXT PRIMARY KEY, "
			. "int_val INTEGER, "
			. "real_val REAL, "
			. "str_val TEXT, "
			. "bool_val INTEGER, "
			. "date_val INTEGER "
			. ")";
		$this->sqlite_execute( $sql );
	}
}

?>
