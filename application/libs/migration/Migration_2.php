<?php

namespace migration;

use \Migrator as Migrator;
use \MigrationFailedException as MigrationFailedException;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \SQL as SQL;

use \model\Users as Users;
use \model\logs\Log_Level as Log_Level;
use \model\logs\Log as Log;


class Migration_2 extends Migrator
{
	public function targetVersion() { return "0.2.0"; }

	public function sqlite_preUpgrade()
	{
		$this->sqlite_backupDatabase();
	}

	public function sqlite_upgrade()
	{
		/** LOG_LEVEL */
		$sql = "CREATE TABLE IF NOT EXISTS log_level ( "
			. Log_Level::id . " INTEGER PRIMARY KEY, "
			. Log_Level::code . " TEXT, "
			. Log_Level::name . " TEXT "
		. ")";
		$this->sqlite_execute( "log_level", $sql, "Create table log_level" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS log_level_code on log_level (code)';
		$this->sqlite_execute( "log_level", $sql, "Index on log_level (code)" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS log_level_name on log_level (name)';
		$this->sqlite_execute( "log_level", $sql, "Index on log_level (name)" );

		/** LOG */
		$sql = "CREATE TABLE IF NOT EXISTS log ( "
			. Log::id . " INTEGER PRIMARY KEY, "
			. Log::trace . " TEXT, "
			. Log::trace_id . " TEXT, "
			. Log::context . " TEXT, "
			. Log::context_id . " TEXT, "
			. Log::message . " TEXT, "
			. Log::session . " TEXT, "
			. Log::level_code . " TEXT, "
			. Log::created . " INTEGER, "
			. "FOREIGN KEY (". Log::level_code .") REFERENCES " . Log_Level::TABLE . "(" . Log_Level::code . ")"
		. ")";
		$this->sqlite_execute( "log", $sql, "Create table log" );

		$sql = 'CREATE  INDEX IF NOT EXISTS log_level_code on log (level_code)';
		$this->sqlite_execute( "log", $sql, "Index on log (level_code)" );
		$sql = 'CREATE  INDEX IF NOT EXISTS log_tracetrace_id on log (trace,trace_id)';
		$this->sqlite_execute( "log", $sql, "Index on log (trace,trace_id)" );
		$sql = 'CREATE  INDEX IF NOT EXISTS log_contextcontext_id on log (context,context_id)';
		$this->sqlite_execute( "log", $sql, "Index on log (context,context_id)" );
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
