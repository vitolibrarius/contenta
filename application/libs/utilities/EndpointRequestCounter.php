<?php

namespace utilities;

use \PDO as PDO;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Config as Config;

use \SQL as SQL;
use \db\SelectSQL as SelectSQL;
use \exceptions\RequestOverloadException as RequestOverloadException;

class EndpointRequestCounter
{
	const DefaultExtension = '.sqlite';
	const NoEndpoint = 'NoEndpoint';

	function __construct( $endpoint = null )
	{
		$dbPath = appendPath(Config::GetProcessing(), "EndpointRequestCounters" );
		makeRequiredDirectory($dbPath, 'endpoint throttle subdirectory for ' . get_class($this));

		$fname = EndpointRequestCounter::NoEndpoint;
		$this->endpointName = EndpointRequestCounter::NoEndpoint;
		$this->daily_max = -1;
		$this->daily_dnld_max = -1;

		if ( is_null($endpoint) == false ) {
			$this->endpointName = $endpoint->displayName();
			$this->daily_max = max( -1, intval($endpoint->daily_max));
			$this->daily_dnld_max = max( -1, intval($endpoint->daily_dnld_max));
			$parse = parse_url($endpoint->base_url);
			if ( isset($parse['host']) ) {
				$fname = $parse['host'];
			}
		}
		$this->fullpath = appendPath( $dbPath, sanitize_filename($fname) . EndpointRequestCounter::DefaultExtension );
		$this->initializeSchema();
	}

	public function isOverMaximum($type = 'daily_max')
	{
		// if we are disabled, skip everything
		$max = -1;
		switch ( $type ) {
			case 'daily_dnld_max':
				$max = $this->daily_dnld_max;
				break;
			case 'daily_max':
			default:
				$max = $this->daily_max;
				break;
		}

		if ( $max <= 0 ) {
			return false;
		}

		list($count, $mindate) = $this->count($type);
		return ( $count > $max );
	}

	public function markOverMaximum($type = 'daily_max')
	{
		if ( $this->isOverMaximum($type) ) {
			return true;
		}

		$this->mark($type);
		return false;
	}

	private function database()
	{
		$database = new PDO("sqlite:" . $this->fullpath);
    	$database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$database->exec( 'PRAGMA foreign_keys = ON;' );
		$database->exec( 'PRAGMA busy_timeout = 10000;' );
		return $database;
	}

	private function purge()
	{
		// 24 hours
		$purgeMarker = time() - 60 * 60 * 24;
		$sql = "delete from PRIMITIVE where date_val < :k";
		$params = array( ":k" => $purgeMarker );

		$database = $this->database();
		$statement = $database->prepare($sql);
		if ($statement == false || $statement->execute($params) == false) {
			$errPoint = ($statement ? $statement : $database);
			$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
			Logger::logSQLError($sql, 'purge', $errPoint->errorCode(), $pdoError, $sql, $params);
			$database = null;
			throw new \Exception("Error executing change to " . $sql);
		}

		$database = null;
	}

	public function count($type = 'daily_max')
	{
		$this->purge();
		$sql = "select COUNT(*) as COUNT, MIN(date_val) as MINDATE from PRIMITIVE where type = :t";
		$params = array( ":t" => $type );

		$database = $this->database();
		$statement = $database->prepare($sql);
		if ($statement == false || $statement->execute($params) == false) {
			$errPoint = ($statement ? $statement : $database);
			$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
			Logger::logSQLError($sql, 'count', $errPoint->errorCode(), $pdoError, $sql, $params);
			$database = null;
			throw new \Exception("Error executing change to " . $sql);
		}

		$value = $statement->fetch();
		$database = null;
		if ( $value != null ) {
			return array( $value['COUNT'], $value['MINDATE'] );
		}
		Logger::logWarning("No count returned", $this->code, getmypid());
		return array( 0, 0 );
	}

	private function mark($type = 'daily_max')
	{
		$sql = "insert into PRIMITIVE ( pid, date_val, type ) values (:p, :d, :t)";
		$params = array(":d" => time(), ":p" => getmypid(), ":t" => $type );
		$database = $this->database();
		$statement = $database->prepare($sql);
		if ($statement == false || $statement->execute($params) == false) {
			$errPoint = ($statement ? $statement : $database);
			$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
			Logger::logSQLError($sql, 'sqlite_execute', $errPoint->errorCode(), $pdoError, $sql, $params);
			$database = null;
			throw new \Exception("Error executing change to " . $sql);
		}
		$rowId = $database->lastInsertId();
		$database = null;
		return $rowId;
	}

	public function initializeSchema()
	{
		$sql = "CREATE TABLE IF NOT EXISTS PRIMITIVE ( "
			. "id INTEGER PRIMARY KEY, "
			. "pid INTEGER, "
			. "type TEXT, "
			. "date_val INTEGER "
			. ")";
		$database = $this->database();
		$statement = $database->prepare($sql);
		if ($statement == false || $statement->execute() == false) {
			$errPoint = ($statement ? $statement : $database);
			$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
			Logger::logSQLError($sql, 'sqlite_execute', $errPoint->errorCode(), $pdoError, $sql, $params);
			$database = null;
			throw new \Exception("Error executing change to " . $sql);
		}
		$database = null;
	}
}

?>
