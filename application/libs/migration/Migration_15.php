<?php

namespace migration;

use \Migrator as Migrator;
use \MigrationFailedException as MigrationFailedException;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \SQL as SQL;

use utilities\CronEvaluator as CronEvaluator;
use db\Qualifier as Qualifier;

use model\Series as Series;
use model\Publisher as Publisher;
use model\Series_Alias as Series_Alias;
use model\Character as Character;
use model\Character_Alias as Character_Alias;
use model\Series_Character as Series_Character;
use model\Users as Users;
use model\User_Series as User_Series;
use model\Story_Arc as Story_Arc;
use model\Story_Arc_Character as Story_Arc_Character;
use model\Story_Arc_Series as Story_Arc_Series;
use model\Story_Arc_Publication as Story_Arc_Publication;
use model\Publication as Publication;
use model\Publication_Character as Publication_Character;
use model\Media_Type as Media_Type;
use model\Media as Media;
use model\Endpoint_Type as Endpoint_Type;
use model\Endpoint as Endpoint;
use model\Rss as Rss;
use model\Job as Job;
use model\Job_Type as Job_Type;
use model\Job_Running as Job_Running;
use model\Flux as Flux;


class Migration_15 extends Migrator
{
	public function sqlite_preUpgrade()
	{
		// backup sqlite database file
		$db_path = Config::GetPath("Database/path", null);
		if ( strlen($db_path) == 0 ) {
			throw new \Exception('No path set in configuration for sqlite database');
		}
		$db_file = appendPath($db_path, "contenta.sqlite" );
		$backupDatabase = appendPath($this->scratch, "contenta.Migration_15." . date('Y-m-d.H-i-s') . ".backup");
		file_exists($db_file) == false || copy($db_file, $backupDatabase) || die('Failed to backup ' . $db_file);
	}

	public function sqlite_upgrade()
	{
		/** JOB */
		$job_model = Model::Named("Job");
		$table_fields = \SQL::pragma_TableInfo(Job::TABLE);
		if ( isset($table_fields[ Job::last_fail ]) == false ) {
			$sql = "ALTER TABLE " . Job::TABLE . " ADD COLUMN " . Job::last_fail . " INT";
			$this->sqlite_execute( Job::TABLE, $sql, "Alter table " . Job::TABLE . " add column '" . Job::last_fail . "'" );
		}
		if ( isset($table_fields[ Job::fail_count ]) == false ) {
			$sql = "ALTER TABLE " . Job::TABLE . " ADD COLUMN " . Job::fail_count . " INT";
			$this->sqlite_execute( Job::TABLE, $sql, "Alter table " . Job::TABLE . " add column '" . Job::fail_count . "'" );
		}

		/** JOB_RUNNING */
		$job_type_model = Model::Named("Job_Running");
		$table_fields = \SQL::pragma_TableInfo(Job_Running::TABLE);
		if ( isset($table_fields[ Job_Running::desc ]) == false ) {
			$sql = "ALTER TABLE " . Job_Running::TABLE . " ADD COLUMN " . Job_Running::desc . " TEXT";
			$this->sqlite_execute( Job_Running::TABLE, $sql, "Alter table " . Job_Running::TABLE . " add column '" . Job_Running::desc . "'" );
		}

		/** NEW INDEXES */
		$indexStatements = array(
			array(
				Migrator::IDX_TABLE => Publication::TABLE,
				Migrator::IDX_COLS => array( Publication::xid, Publication::xsource )
			),
			array(
				Migrator::IDX_TABLE => Series::TABLE,
				Migrator::IDX_COLS => array( Series::xid, Series::xsource )
			),
			array(
				Migrator::IDX_TABLE => Story_Arc::TABLE,
				Migrator::IDX_COLS => array( Story_Arc::xid, Story_Arc::xsource )
			),
			array(
				Migrator::IDX_TABLE => Publisher::TABLE,
				Migrator::IDX_COLS => array( Publisher::xid, Publisher::xsource )
			),
			array(
				Migrator::IDX_TABLE => Character::TABLE,
				Migrator::IDX_COLS => array( Character::xid, Character::xsource )
			),
			array(
				Migrator::IDX_TABLE => Media::TABLE,
				Migrator::IDX_COLS => array( Media::checksum ),
				Migrator::IDX_UNIQUE => true
			),
			array(
				Migrator::IDX_TABLE => Flux::TABLE,
				Migrator::IDX_COLS => array( Flux::dest_endpoint, Flux::dest_guid )
			),
		);
		foreach( $indexStatements as $config ) {
			$table = $config[Migrator::IDX_TABLE];
			$columns = $config[Migrator::IDX_COLS];
			$indexName = $table . '_' . implode("", $columns);
			$unique = (isset($config[Migrator::IDX_UNIQUE]) ? boolval($config[Migrator::IDX_UNIQUE]) : false);

			$statement = 'CREATE ' . ($unique ? 'UNIQUE' : '') . ' INDEX IF NOT EXISTS ' . $indexName
				. ' on ' . $table . '(' . implode(",", $columns) . ')';
			$this->sqlite_execute( $table, $statement, "Index on " . $table );
		}

	}

	public function sqlite_postUpgrade()
	{
		return true;
	}
}
