<?php

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
