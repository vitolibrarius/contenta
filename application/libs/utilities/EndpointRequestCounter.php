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
	const MAX_USER_PAUSE = 20;

	private $pdoDatabase;

	function __construct($endpointName = NoEndpoint, $dailyMax = -1)
	{
		$dbPath = appendPath(Config::GetProcessing(), "EndpointRequestCounters" );
		makeRequiredDirectory($dbPath, 'endpoint throttle subdirectory for ' . get_class($this));

		$this->fullpath = appendPath( $dbPath, $endpointName . EndpointRequestCounter::DefaultExtension );
		$this->endpointName = $endpointName;
		$this->dailyMax = max( -1, intval($dailyMax));
		$this->initializeSchema();
	}

	public function overMaximum()
	{
		// if we are disabled, skip everything
		if ( $this->dailyMax <= 0 ) {
			return false;
		}

		list($count, $mindate) = $this->count();
		if ( $count > $this->dailyMax ) {
			return true;
		}
		$this->mark();

		return false;
	}

	private function database()
	{
		if ( isset($this->pdoDatabase) == false ) {
			try {
				$this->pdoDatabase = new PDO("sqlite:" . $this->fullpath);
				$this->pdoDatabase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$this->pdoDatabase->exec( 'PRAGMA foreign_keys = ON;' );
				$this->pdoDatabase->exec( 'PRAGMA busy_timeout = 10000;' );
			}
			catch(PDOException $e) {
				Logger::logException($e);
			}
		}
		return $this->pdoDatabase;
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
