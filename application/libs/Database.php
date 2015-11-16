<?php

use utilities\Stopwatch as Stopwatch;

/**
 * Class Database
 * Creates a PDO database connection. This connection will be passed into the models (so we use
 * the same connection for all models and prevent to open multiple connections at once)
 */
class Database extends PDO
{
	final public static function instance()
	{
		static $instance = null;
		if ( null == $instance ) {
			$instance = new Database();
		}
	   return $instance;
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
        $this->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('TraceStatement', array($this)));
	}

	public function verifyDatabase() {
		$versionNum = currentVersionNumber();

		$sql = "SELECT * FROM version where code = :code";
		$statement = $this->prepare($sql);
		if ($statement && $statement->execute(array(":code" => $versionNum))) {
			$version = $statement->fetch();
			return ($version != false);
		}
		return false;
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
