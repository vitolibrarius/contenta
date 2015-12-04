<?php

namespace utilities;

use \PDO as PDO;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Config as Config;

use \SQL as SQL;
use db\SelectSQL as SelectSQL;
use exceptions\ThrottleOverloadException as ThrottleOverloadException;

class EndpointThrottle
{
	const DefaultExtension = '.sqlite';
	const NoCodeThrottle = 'NoCodeThrottle';
	const MAX_USER_PAUSE = 20;

	function __construct($code = NoCodeThrottle, $limit = -1, $seconds = -1)
	{
		$dbPath = appendPath(Config::GetProcessing(), "EndpointThrottles" );
		makeRequiredDirectory($dbPath, 'endpoint throttle subdirectory for ' . get_class($this));

		$this->fullpath = appendPath( $dbPath, $code . EndpointThrottle::DefaultExtension );
		$this->code = $code;
		$this->limit = max( -1, intval($limit));
		$this->seconds = max( -1, intval($seconds));
		$this->initializeSchema();
	}

	public function throttle()
	{
		$limit = $this->limit();
		$sec = $this->seconds();

		// if we are disabled, skip everything
		if ( $limit <= 0 || $sec <= 0 ) {
			return;
		}

		list($count, $mindate) = $this->count();
		$ratio = $count / $limit;
		$max_ratio = $ratio;
		$pause = CEIL($sec / $limit);
		$wait_time = MAX( $pause, ($sec - (time() - $mindate)) );
		if ( defined('Contenta_Daemon') ) {
			// daemon ratio only half total limit
			while ( $ratio > 0.95 ) {
				sleep( $wait_time / 2 );
				list($count, $mindate) = $this->count();
				$ratio = $count / $limit;
				$max_ratio = max( $max_ratio, $ratio );
				$wait_time = MAX( $pause, ($sec - (time() - $mindate)) );
			}
		}
		else if ( $ratio > 0.95 ) {
			sleep( $pause );
		}
		else if ( $ratio >= 0.5 ) {
			sleep( $pause );
		}

		$rowId = $this->mark();
	}

	private function limit()
	{
		// if we are running as a daemon, cut our limit in half.  hopefully the UI can still be responsive
		if ( $this->limit > 0 && defined( 'Contenta_Daemon' )) {
			return $this->limit / 2;
		}
		return $this->limit;
	}

	private function seconds()
	{
		return $this->seconds;
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
		$purgeMarker = time() - $this->seconds();
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

	private function count()
	{
		$this->purge();
		$sql = "select COUNT(*) as COUNT, MIN(date_val) as MINDATE from PRIMITIVE";
		$params = null;

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

	private function mark()
	{
		$sql = "insert into PRIMITIVE ( pid, date_val) values (:p, :d)";
		$params = array(":d" => time(), ":p" => getmypid());
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
