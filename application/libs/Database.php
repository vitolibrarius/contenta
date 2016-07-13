<?php

use utilities\Stopwatch as Stopwatch;

/**
 * Class Database
 * Creates a PDO database connection. This connection will be passed into the models (so we use
 * the same connection for all models and prevent to open multiple connections at once)
 */
class Database extends PDO
{
	const CONTENTA_DB_VERSION = 1;

    private static $instances = array();

    protected function __clone() {}

	final public static function instance()
	{
        $cls = get_called_class();
        if (isset(self::$instances[$cls]) === false) {
            self::$instances[$cls] = new static;
        }
        return self::$instances[$cls];
	}

	final public static function ResetConnection()
	{
        $cls = get_called_class();
        if (isset(self::$instances[$cls]) === true) {
            unset(self::$instances[$cls]);
        }
	}

	/*
	 * Verification is 2 quick tests.  First, that the database has a meta version  number == CONTENTA_DB_VERSION, and
	 * second that the version and patch tables are up to date
	 */
	public static function VerifyDatabase() {
		$dbversion = static::DBVersion();
		if ( $dbversion == Database::CONTENTA_DB_VERSION ) {
			$versionNum = currentVersionNumber();
			$maxPatchApplied = static::DBPatchLevel();
			return (version_compare( $versionNum, $maxPatchApplied ) == 0);
		}
		return false;
	}

	public static function DBVersion($newVersion = null)
	{
		$type = Config::Get("Database/type", "sqlite");
		$dbConnection = new static;
		switch ( $type ) {
			case 'mysql':
				$dbversion = -1;
				break;
			case 'sqlite':
				if ( is_null($newVersion) == false && is_integer($newVersion) ) {
					$dbConnection->execute_sql( 'PRAGMA user_version=' . $newVersion );
				}
				$rows = $dbConnection->execute_sql( 'PRAGMA user_version' );
				$key = key($rows[0]);
				$dbversion = $rows[0]->{$key};
				break;
			default:
				die('Unable to verify database connection for ' . $type);
				break;
		}
		unset($dbConnection);
		return $dbversion;
	}

	public static function DBPatchLevel()
	{
		$dbConnection = new static;
		try {
			$rows = $dbConnection->execute_sql( "select max(code) as MAX_CODE from version");
			$key = key($rows[0]);
			$dbpatch = $rows[0]->{$key};
		}
		catch ( \Exception $e ) {
			$dbpatch = "0.0.0";
		}
		finally {
			unset($dbConnection);
		}
		return $dbpatch;
	}

	public function __construct()
	{
		$type = Config::Get("Database/type", "sqlite");
		if ( $type === 'mysql')
		{
			parent::__construct($type . ':host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8', DB_USER, DB_PASS, $options);
		}
		else if ( $type === 'sqlite')
		{
			$db_path = Config::GetPath("Database/path", null);
			if ( strlen($db_path) == 0 ) {
				throw new \Exception('No path set in configuration for sqlite database');
			}
			makeRequiredDirectory($db_path, 'Database directory');

			parent::__construct($type . ':' . appendPath($db_path, "contenta.sqlite" ));

			$this->exec( 'PRAGMA foreign_keys = ON;' );
			$this->exec( 'PRAGMA busy_timeout = 10000;' );
			$this->exec( 'PRAGMA journal_mode=WAL;' );
		}
		else
		{
			die('Failed to create database connection for ' . $type);
		}

		if ( (is_null($this->errorCode()) == false) && ($this->errorCode() != PDO::ERR_NONE)){
			echo 'PDO error code ' . $this->errorCode() . PHP_EOL;
			echo 'PDO error info ' . var_export( $this->errorInfo(), true) . PHP_EOL;
			die('Failed to create database connection for ' . var_export(Config::Get("Database", ""), true));
		}

		$this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
		$this->setAttribute(PDO::ATTR_TIMEOUT, 10000);
		$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//         $this->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('TraceStatement', array($this)));
	}

	public function execute_sql( $sql = null, $params = array() )
	{
		if ( empty($sql) ) {
			throw new Exception("Unable to execute SQL for -null- statement");
		}

		$statement = $this->prepare($sql);
		try {
			if ($statement == false || $statement->execute($params) == false) {
				$errPoint = ($statement ? $statement : $this);
				throw new Exception( 'PDO Error(' . $errPoint->errorCode() . ') ' . $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2]
					. ' for [' . $sql . '] ' . (isset($params) ? var_export($params, true) : 'No Parameters')
				);
			}
		}
		catch ( \PDOException $pdoe ) {
			$errPoint = ($statement ? $statement : $this);
			throw new Exception( 'PDO Error(' . $errPoint->errorCode() . ') ' . $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2]
				. ' for [' . $sql . '] ' . (isset($params) ? var_export($params, true) : 'No Parameters')
			);
		}

		return $statement->fetchAll();
	}

	public function dbOptimize()
	{
		$type = Config::Get("Database/type", "sqlite");
		$tableNames = array();
		switch ( $type ) {
			case 'mysql':
				$sql = null;
				break;
			case 'sqlite':
				$sql = "vacuum";
				break;
			default:
				die('Unable to query tables from database connection for ' . $type);
				break;
		}

		try {
			$result = $this->execute_sql( $sql );
		}
		catch ( \Exception $e ) {
			Logger::logToFile( $e->__toString() );
		}
		return true;
	}

	public function dbTableNames()
	{
		$type = Config::Get("Database/type", "sqlite");
		$tableNames = array();
		switch ( $type ) {
			case 'mysql':
				$sql = null;
				break;
			case 'sqlite':
				$sql = "SELECT name FROM sqlite_master WHERE type='table'";
				break;
			default:
				die('Unable to query tables from database connection for ' . $type);
				break;
		}

		try {
			$result = $this->execute_sql( $sql );
			if ( is_array( $result ) ) {
				foreach( $result as $row ) {
					$tableNames[] = (isset($row->name) ? $row->name : 'error');
				}
				return $tableNames;
			}
		}
		catch ( \Exception $e ) {
			Logger::logToFile( $e->__toString() );
		}
		return false;
	}

	public function dbTableInfo($tablename)
	{
		$type = Config::Get("Database/type", "sqlite");
		switch ( $type ) {
			case 'mysql':
				$sql = "";
				break;
			case 'sqlite':
				$sql = "PRAGMA table_info(" . $tablename . ")";
				break;
			default:
				die('Unable to query tables from database connection for ' . $type);
				break;
		}

		try {
			$tableDetails = $this->execute_sql($sql);
			if ($tableDetails != false) {
				$table_fields = array();
				foreach($tableDetails as $key => $value) {
					$table_fields[ $value->name ] = $value;
				}
				return $table_fields;
			}
		}
		catch( \Exception $e ) {
			Logger::logToFile( $e->__toString() );
		}

		return false;
	}

	public function dbPKForTable($tablename)
	{
		$rows = $this->dbTableInfo($tablename);
		if ( is_array($rows) ) {
			$results = array();
			foreach( $rows as $row ) {
				if ( isset($row->pk) && $row->pk != 0 ) {
					$results[] = (isset($row->name) ? $row->name : 'error');
				}
			}
			return $results;
		}
		return false;
	}

	public function dbTableRename( $oldName = null, $newName = null)
	{
		$type = Config::Get("Database/type", "sqlite");
		$tableNames = array();
		switch ( $type ) {
			case 'mysql':
				$sql[] = "RENAME TABLE $oldName TO $newName";
				break;
			case 'sqlite':
				$sql[] = 'PRAGMA foreign_keys = OFF;';
				$sql[] = "ALTER TABLE $oldName RENAME TO $newName";
				break;
			default:
				die('Unable to query tables from database connection for ' . $type);
				break;
		}

		try {
			foreach( $sql as $s ) {
				$result = $this->execute_sql( $s );
			}
			return true;
		}
		catch( \Exception $e ) {
			Logger::logToFile( $e->__toString() );
		}
		return false;
	}

	public function dbFetchRawCountForSQL($sql, $params = null)
	{
		$rows = $this->execute_sql($sql, $params);
		if ( is_array($rows) && count($rows) === 1 ) {
			$key = key($rows[0]);
			return intval($rows[0]->{$key});
		}
		return false;
	}

	public function dbFetchRawCount($table, $restrictKey = null, $restrictOp = "=", $restrictValue = null )
	{
		$sql = "select count(*) from " . $table;
		$params = array();
		if ( is_null( $restrictKey ) == false ) {
			$sql .= " where " . $restrictKey . " " . $restrictOp . " :" . $restrictKey;
			$params[":".$restrictKey] = $restrictValue;
		}
		return $this->dbFetchRawCountForSQL($sql, $params);
	}

	public function dbFetchRawBatch($table, $page = 0, $page_size = 500 )
	{
		$pk = $this->dbPKForTable( $table );
		$sql = "select * from " . $table
			. " order by " . implode(",", $pk)
			. " limit " . $page_size
			. " offset " . ($page * $page_size);
		return $this->execute_sql($sql);
	}
}

class TraceStatement extends PDOStatement {
    protected $pdo;

    protected function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function execute($input_parameters = null)
    {
		Stopwatch::start( $this->queryString );
		$count = 0;
		$keepTrying = true;
		$success = false;

		while ($keepTrying && $count < 5) {
			try {
				$success = parent::execute( $input_parameters );
				$keepTrying = false;
			}
			catch ( \Exception $exception ) {
				$count++;
				list($message, $file, $line) = Logger::exceptionMessage( $exception );
				Logger::logToFile( "Try $count : " . $message, $file, $line );
				usleep(500);
			}
		}

		if ( $count >= 5 && false == $success) {
			Logger::logToFile( "Failed after $count tries" );
		}
		else {
			$elapsed = Stopwatch::end( $this->queryString );
			if ( $elapsed > 0.5 ) {
				$msg = $this->queryString . ' ' . (isset($input_parameters) ? var_export($input_parameters, true) : 'No Parameters');
				Logger::logToFile( $msg, "Slow SQL", $elapsed . " seconds" );
			}
		}

		return $success;
	}
}
