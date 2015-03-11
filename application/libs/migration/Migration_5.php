<?php

namespace migration;

use \Migrator as Migrator;
use \MigrationFailedException as MigrationFailedException;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \Database as Database;

use model\Publisher as Publisher;

class Migration_5 extends Migrator
{
	public function sqlite_preUpgrade()
	{
		// backup sqlite database file
		$db_path = Config::GetPath("Database/path", null);
		if ( strlen($db_path) == 0 ) {
			throw new Exception('No path set in configuration for sqlite database');
		}
		$db_file = appendPath($db_path, "contenta.sqlite" );
		$backupDatabase = appendPath($this->scratch, "contenta.Migration_5." . date('Y-m-d.H-i-s') . ".backup");
		file_exists($db_file) == false || copy($db_file, $backupDatabase) || die('Failed to backup ' . $db_file);
	}

	public function sqlite_upgrade()
	{
		$model = Model::Named("Publisher");

		$sql = "CREATE TABLE IF NOT EXISTS " . Publisher::TABLE
				. " ( "
				. Publisher::id . " INTEGER PRIMARY KEY, "
				. Publisher::name . " TEXT COLLATE NOCASE,  "
				. Publisher::xurl . " TEXT,  "
				. Publisher::xsource . " TEXT,  "
				. Publisher::xid . " TEXT, "
				. Publisher::created . " INTEGER, "
				. Publisher::updated . " INTEGER, "
				. Publisher::xupdated . " INTEGER "
				. ")";

		$statement = $this->db->prepare($sql);
		if ($statement == false || $statement->execute() == false) {
			$errPoint = ($statement ? $statement : $this->db);
			$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
			Logger::logSQLError('Log', 'createTable', $errPoint->errorCode(), $pdoError, $sql, null);
			throw new MigrationFailedException("Error creating Log_Level table");
		}

		Logger::logInfo( "Created table " . Publisher::TABLE, "Migration", Publisher::TABLE);

		$this->db->exec('CREATE UNIQUE INDEX IF NOT EXISTS ' . Publisher::TABLE . '_nameindex on ' . Publisher::TABLE . '(' . Publisher::name . ')');
	}

	public function sqlite_postUpgrade()
	{
	}
}
