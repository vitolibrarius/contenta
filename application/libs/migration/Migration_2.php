<?php

namespace migration;

use \Migrator as Migrator;
use \MigrationFailedException as MigrationFailedException;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \SQL as SQL;

use model\Users as Users;
use model\logs\Log_Level as Log_Level;
use model\logs\Log as Log;


class Migration_2 extends Migrator
{
	public function sqlite_preUpgrade()
	{
		// backup sqlite database file
		$db_path = Config::GetPath("Database/path", null);
		if ( strlen($db_path) == 0 ) {
			throw new \Exception('No path set in configuration for sqlite database');
		}
		$db_file = appendPath($db_path, "contenta.sqlite" );
		$backupDatabase = appendPath($this->scratch, "contenta.Migration_2." . date('Y-m-d.H-i-s') . ".backup");
		file_exists($db_file) == false || copy($db_file, $backupDatabase) || die('Failed to backup ' . $db_file);
	}

	public function sqlite_upgrade()
	{
		$sql = "CREATE TABLE IF NOT EXISTS " . Log_Level::TABLE
				. " ( "
				. Log_Level::id . " INTEGER, "
				. Log_Level::code . " TEXT PRIMARY KEY, "
				. Log_Level::name . " TEXT COLLATE NOCASE "
				. ")";
		$this->sqlite_execute( Log_Level::TABLE, $sql, "Create table " . Log_Level::TABLE );

		$sql = "CREATE TABLE IF NOT EXISTS " . Log::TABLE
				. " ( "
				. Log::id . " INTEGER PRIMARY KEY,  "
				. Log::trace . " TEXT, "
				. Log::trace_id . " TEXT, "
				. Log::context . " TEXT, "
				. Log::context_id . " TEXT, "
				. Log::level . " TEXT, "
				. Log::message . " TEXT, "
				. Log::created . " INTEGER, "
				. "FOREIGN KEY (" . Log::level . ") REFERENCES " . Log_Level::TABLE . "(" . Log_Level::code . ")"
				. ")";
		$this->sqlite_execute( Log::TABLE, $sql, "Create table " . Log::TABLE );
		$indexStatements = array(
			'CREATE UNIQUE INDEX IF NOT EXISTS ' . Log_Level::TABLE . '_idindex on ' . Log_Level::TABLE . '(' . Log_Level::id . ')',
			'CREATE UNIQUE INDEX IF NOT EXISTS ' . Log_Level::TABLE . '_nameindex on ' . Log_Level::TABLE . '(' . Log_Level::name . ')'
		);
		foreach( $indexStatements as $stmt ) {
			$this->sqlite_execute( Log::TABLE, $stmt, "Index on " . Log::TABLE );
		}
	}

	public function sqlite_postUpgrade()
	{
		$log_level_model = Model::Named("Log_Level");
		$log_levels = array(
			'info' => 'INFO',
			'warning' => 'WARNING',
			'error' => 'ERROR',
			'fatal' => 'FATAL'
		);
		foreach ($log_levels as $code => $name) {
			if ($log_level_model->objectForCode($code) == false)
			{
				$insert = SQL::Insert( $log_level_model );
				$insert->addRecord( array(
					Log_Level::id => array_search($code, array_keys($log_levels)),
					Log_Level::code => $code,
					Log_Level::name => $name
					)
				 );
				$success = $insert->commitTransaction();
			}
		}
	}
}
