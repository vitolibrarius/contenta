<?php

namespace migration;

use \Migrator as Migrator;
use \MigrationFailedException as MigrationFailedException;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \SQL as SQL;
use db\Qualifier as Qualifier;

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

//
class Migration_10 extends Migrator
{
	public function targetVersion() { return "0.3.5"; }

	public function sqlite_preUpgrade()
	{
		$this->sqlite_backupDatabase();
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
			$existing = \SQL::raw( "select id FROM " . Job_Type::TABLE . " where code = :code", array( ":code" => $dict[Job_Type::code]));
			if ( is_array($existing) && count($existing) > 0) {
				\SQL::Update($job_type_model, Qualifier::Equals( "code", $dict[Job_Type::code]), $dict)->commitTransaction();
			}
			else {
				$inserts = \SQL::Insert( $job_type_model, array( "name", "code", "desc", "processor", "scheduled") );
				$inserts->addRecord( $dict );
				$inserts->commitTransaction(true);
			}
		}
	}
}
