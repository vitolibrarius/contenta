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
use model\Endpoint as Endpoint;

use model\Job_Type as Job_Type;
use model\Job_Running as Job_Running;
use model\Job as Job;

class Migration_9 extends Migrator
{
	public function sqlite_preUpgrade()
	{
		// backup sqlite database file
		$db_path = Config::GetPath("Database/path", null);
		if ( strlen($db_path) == 0 ) {
			throw new \Exception('No path set in configuration for sqlite database');
		}
		$db_file = appendPath($db_path, "contenta.sqlite" );
		$backupDatabase = appendPath($this->scratch, "contenta.Migration_9." . date('Y-m-d.H-i-s') . ".backup");
		file_exists($db_file) == false || copy($db_file, $backupDatabase) || die('Failed to backup ' . $db_file);
	}

	public function sqlite_upgrade()
	{
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
				. Job_Running::job_type_id . " INTEGER, "
				. Job_Running::processor . " TEXT, "
				. Job_Running::guid . " TEXT, "
				. Job_Running::created . " INTEGER, "
				. Job_Running::pid . " INTEGER, "
				. "FOREIGN KEY (". Job_Running::job_id .") REFERENCES " . Job::TABLE . "(" . Job::id . "),"
				. "FOREIGN KEY (". Job_Running::job_type_id .") REFERENCES " . Job_Type::TABLE . "(" . Job_Type::id . ")"
			. ")";
		$this->sqlite_execute( Job_Running::TABLE, $sql, "Create table " . Job_Running::TABLE );
	}

	public function sqlite_postUpgrade()
	{
// 		$job_type_model = Model::Named("Job_Type");
// 		$types = array(
// 			array(
// 				Job_Type::name => "Comic Book RAR",
// 				Job_Type::code => "cbr",
// 				Job_Type::desc => "Convert cbr files into cbz",
// 				Job_Type::scheduled => 0
// 			),
// 			array(
// 				Job_Type::name => "Comic Book ZIP",
// 				Job_Type::code => "cbz",
// 				Job_Type::desc => "Process cbz files into indexed media for database",
// 				Job_Type::scheduled => 0
// 			),
// 			array(
// 				Job_Type::name => "Character profiles",
// 				Job_Type::code => "character",
// 				Job_Type::desc => "Load character profile data from metadata endpoints (like ComicVine)",
// 				Job_Type::scheduled => 1
// 			),
// 			array(
// 				Job_Type::name => "Publisher profiles",
// 				Job_Type::code => "publisher",
// 				Job_Type::desc => "Load publisher profile data from metadata endpoints (like ComicVine)",
// 				Job_Type::scheduled => 1
// 			),
// 			array(
// 				Job_Type::name => "RSS Feed (nzb)",
// 				Job_Type::code => "rss",
// 				Job_Type::desc => "Load data from an RSS feed",
// 				Job_Type::scheduled => 1
// 			)
// 		);
//
// 		foreach ($types as $dict) {
// 			if ($job_type_model->jobTypeForCode($dict[Job_Type::code]) == false) {
// 				$newObjId = $job_type_model->createObject($dict);
// 			}
// 		}
	}
}
