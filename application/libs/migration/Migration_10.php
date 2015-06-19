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

//
class Migration_10 extends Migrator
{
	public function sqlite_preUpgrade()
	{
		// backup sqlite database file
		$db_path = Config::GetPath("Database/path", null);
		if ( strlen($db_path) == 0 ) {
			throw new \Exception('No path set in configuration for sqlite database');
		}
		$db_file = appendPath($db_path, "contenta.sqlite" );
		$backupDatabase = appendPath($this->scratch, "contenta.Migration_10." . date('Y-m-d.H-i-s') . ".backup");
		file_exists($db_file) == false || copy($db_file, $backupDatabase) || die('Failed to backup ' . $db_file);
	}

	public function sqlite_upgrade()
	{
		/** JOB_TYPE */
		$job_type_model = Model::Named("Job_Type");
		$table_fields = \SQL::pragma_TableInfo(Job_Type::TABLE);
		if ( isset($table_fields[ Job_Type::processor ]) == false ) {
			$this->sqlite_execute(
				Job_Type::TABLE ,
				"ALTER TABLE " . Job_Type::TABLE . " ADD COLUMN " . Job_Type::processor . " TEXT",
				"Adding the processor column to Job_Type"
			);
		}

		/** JOB */
		$job_model = Model::Named("Job");
		$table_fields = \SQL::pragma_TableInfo(Job::TABLE);
		if ( isset($table_fields[ Job::last_run ]) == false ) {
			$this->sqlite_execute(
				Job::TABLE ,
				"ALTER TABLE " . Job::TABLE . " ADD COLUMN " . Job::last_run . " integer",
				"Adding the last_run column to Job"
			);
		}
	}

	public function sqlite_postUpgrade()
	{
		$job_type_model = Model::Named("Job_Type");
		$types = array(
			array(
				Job_Type::name => "Comic Book RAR",
				Job_Type::code => "cbr",
				Job_Type::desc => "Convert cbr files into cbz",
				Job_Type::scheduled => 0
			),
			array(
				Job_Type::name => "Comic Book ZIP",
				Job_Type::code => "cbz",
				Job_Type::desc => "Process cbz files into indexed media for database",
				Job_Type::scheduled => 0
			),
			array(
				Job_Type::name => "Character profiles",
				Job_Type::code => "character",
				Job_Type::desc => "Load character profile data from metadata endpoints (like ComicVine)",
				Job_Type::processor => "ComicVineImporter",
				Job_Type::scheduled => 1
			),
			array(
				Job_Type::name => "Publisher profiles",
				Job_Type::code => "publisher",
				Job_Type::desc => "Load publisher profile data from metadata endpoints (like ComicVine)",
				Job_Type::processor => "ComicVineImporter",
				Job_Type::scheduled => 1
			),
			array(
				Job_Type::name => "RSS Feed (nzb)",
				Job_Type::code => "rss",
				Job_Type::desc => "Load data from an RSS feed",
				Job_Type::processor => "RSSImporter",
				Job_Type::scheduled => 1
			)
		);

		foreach ($types as $dict) {
			$jobType = $job_type_model->jobTypeForCode($dict[Job_Type::code]);
			if ($jobType == false) {
				$newObjId = $job_type_model->createObject($dict);
			}
			else {
				$job_type_model->updateObject($jobType, $dict);
			}
		}
	}
}
