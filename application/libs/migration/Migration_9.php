<?php

namespace migration;

use \Migrator as Migrator;
use \MigrationFailedException as MigrationFailedException;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \SQL as SQL;

use model\Series as Series;
use model\Publisher as Publisher;
use model\Series_Alias as Series_Alias;
use model\Character as Character;
use model\Character_Alias as Character_Alias;
use model\Series_Character as Series_Character;
use model\User_Series as User_Series;
use model\Story_Arc as Story_Arc;
use model\Story_Arc_Character as Story_Arc_Character;
use model\Story_Arc_Series as Story_Arc_Series;
use model\Story_Arc_Publication as Story_Arc_Publication;
use model\Publication as Publication;
use model\Publication_Character as Publication_Character;
use model\Media_Type as Media_Type;
use model\Media as Media;
use model\Endpoint as Endpoint;

use \model\user\Users as Users;
use \model\jobs\Job_Type as Job_Type;
use \model\jobs\Job_Running as Job_Running;
use \model\jobs\Job as Job;

class Migration_9 extends Migrator
{
	public function targetVersion() { return "0.3.4"; }

	public function sqlite_preUpgrade()
	{
		$this->sqlite_backupDatabase();
	}

	public function sqlite_upgrade()
	{
		$table_fields = \SQL::pragma_TableInfo(Job_Running::TABLE);
		if ( isset($table_fields[ Job_Running::type_id ]) == false ) {
			// easier to just drop these tables and re-create them
			$dropOrder = array(
				Job_Running::TABLE
			);
			foreach( $dropOrder as $tbl ) {
				try {
					$this->sqlite_execute( $tbl, "DROP TABLE " . $tbl, $tbl . " - droppped" );
				}
				catch( \Exception $e ) {
					Logger::logException($e);
				}
			}
		}

		$job_type_model = Model::Named("Job_Type");

		/** JOB_TYPE */
		$sql = 'CREATE TABLE IF NOT EXISTS ' . Job_Type::TABLE . " ( "
				. Job_Type::id . " INTEGER PRIMARY KEY, "
				. Job_Type::name . " TEXT COLLATE NOCASE, "
				. Job_Type::code . " TEXT COLLATE NOCASE, "
				. Job_Type::desc . " TEXT, "
				. Job_Type::scheduled . " INTEGER "
			. ")";
		$this->sqlite_execute( Job_Type::TABLE, $sql, "Create table " . Job_Type::TABLE );

		/** JOB */
		$sql = 'CREATE TABLE IF NOT EXISTS ' . Job::TABLE . " ( "
				. Job::id . " INTEGER PRIMARY KEY, "
				. Job::type_id . " INTEGER, "
				. Job::endpoint_id . " INTEGER, "
				. Job::minute . " TEXT, "
				. Job::hour . " TEXT, "
				. Job::dayOfWeek . " TEXT, "
				. Job::parameter . " TEXT, "
				. Job::created . " INTEGER, "
				. Job::next . " INTEGER, "
				. Job::one_shot . " INTEGER, "
				. Job::enabled . " INTEGER, "
				. "FOREIGN KEY (". Job::endpoint_id .") REFERENCES " . Endpoint::TABLE . "(" . Endpoint::id . "),"
				. "FOREIGN KEY (". Job::type_id .") REFERENCES " . Job_Type::TABLE . "(" . Job_Type::id . ")"
			. ")";
		$this->sqlite_execute( Job::TABLE, $sql, "Create table " . Job::TABLE );

		/** JOB_RUNNING */
		$sql = 'DROP TABLE IF EXISTS ' . Job_Running::TABLE;
		$this->sqlite_execute( Job_Running::TABLE, $sql, "Drop table for recreate " . Job_Running::TABLE );

		$sql = 'CREATE TABLE IF NOT EXISTS ' . Job_Running::TABLE . " ( "
				. Job_Running::id . " INTEGER PRIMARY KEY, "
				. Job_Running::job_id . " INTEGER, "
				. Job_Running::type_id . " INTEGER, "
				. Job_Running::processor . " TEXT, "
				. Job_Running::guid . " TEXT, "
				. Job_Running::created . " INTEGER, "
				. Job_Running::pid . " INTEGER, "
				. "FOREIGN KEY (". Job_Running::job_id .") REFERENCES " . Job::TABLE . "(" . Job::id . "),"
				. "FOREIGN KEY (". Job_Running::type_id .") REFERENCES " . Job_Type::TABLE . "(" . Job_Type::id . ")"
			. ")";
		$this->sqlite_execute( Job_Running::TABLE, $sql, "Create table " . Job_Running::TABLE );
	}

	public function sqlite_postUpgrade()
	{
	}
}
