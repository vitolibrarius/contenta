<?php

namespace migration;

use \Migrator as Migrator;
use \MigrationFailedException as MigrationFailedException;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \SQL as SQL;

use \model\jobs\Job_Type as Job_Type;
use \model\jobs\Job_Running as Job_Running;
use \model\jobs\Job as Job;
use \model\network\Endpoint_Type as Endpoint_Type;
use \model\network\Endpoint as Endpoint;

class Migration_4 extends Migrator
{
	public function targetVersion() { return "0.4.0"; }

	public function sqlite_preUpgrade()
	{
		$this->sqlite_backupDatabase();
	}

	public function sqlite_upgrade()
	{
		/** JOB_TYPE */
		$sql = "CREATE TABLE IF NOT EXISTS job_type ( "
			. Job_Type::id . " INTEGER PRIMARY KEY, "
			. Job_Type::code . " TEXT, "
			. Job_Type::name . " TEXT, "
			. Job_Type::desc . " TEXT, "
			. Job_Type::processor . " TEXT, "
			. Job_Type::parameter . " TEXT, "
			. Job_Type::scheduled . " INTEGER, "
			. Job_Type::requires_endpoint . " INTEGER "
		. ")";
		$this->sqlite_execute( "job_type", $sql, "Create table job_type" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS job_type_code on job_type (code)';
		$this->sqlite_execute( "job_type", $sql, "Index on job_type (code)" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS job_type_name on job_type (name)';
		$this->sqlite_execute( "job_type", $sql, "Index on job_type (name)" );

		/** JOB */
		$sql = "CREATE TABLE IF NOT EXISTS job ( "
			. Job::id . " INTEGER PRIMARY KEY, "
			. Job::type_id . " INTEGER, "
			. Job::endpoint_id . " INTEGER, "
			. Job::enabled . " INTEGER, "
			. Job::one_shot . " INTEGER, "
			. Job::fail_count . " INTEGER, "
			. Job::elapsed . " INTEGER, "
			. Job::minute . " TEXT, "
			. Job::hour . " TEXT, "
			. Job::dayOfWeek . " TEXT, "
			. Job::parameter . " TEXT, "
			. Job::next . " INTEGER, "
			. Job::last_run . " INTEGER, "
			. Job::last_fail . " INTEGER, "
			. Job::created . " INTEGER, "
			. "FOREIGN KEY (". Job::type_id .") REFERENCES " . Job_Type::TABLE . "(" . Job_Type::id . "),"
			. "FOREIGN KEY (". Job::endpoint_id .") REFERENCES " . Endpoint::TABLE . "(" . Endpoint::id . ")"
		. ")";
		$this->sqlite_execute( "job", $sql, "Create table job" );

		/** JOB_RUNNING */
		$sql = "CREATE TABLE IF NOT EXISTS job_running ( "
			. Job_Running::id . " INTEGER PRIMARY KEY, "
			. Job_Running::job_id . " INTEGER, "
			. Job_Running::type_id . " INTEGER, "
			. Job_Running::processor . " TEXT, "
			. Job_Running::guid . " TEXT, "
			. Job_Running::pid . " INTEGER, "
			. Job_Running::desc . " TEXT, "
			. Job_Running::created . " INTEGER, "
			. "FOREIGN KEY (". Job_Running::job_id .") REFERENCES " . Job::TABLE . "(" . Job::id . "),"
			. "FOREIGN KEY (". Job_Running::type_id .") REFERENCES " . Job_Type::TABLE . "(" . Job_Type::id . ")"
		. ")";
		$this->sqlite_execute( "job_running", $sql, "Create table job_running" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS job_running_pid on job_running (pid)';
		$this->sqlite_execute( "job_running", $sql, "Index on job_running (pid)" );
	}

	public function sqlite_postUpgrade()
	{
		$job_type_model = Model::Named("Job_Type");
		$types = array(
			array(
				Job_Type::name => "Character Updates",
				Job_Type::code => "character",
				Job_Type::desc => "Load character profile data from metadata endpoints (like ComicVine) in batches of 20",
				Job_Type::processor => "ComicVineImporter",
				Job_Type::parameter => json_encode(array( "enqueueBatch" => array("character", 20)), JSON_PRETTY_PRINT),
				Job_Type::requires_endpoint => Model::TERTIARY_TRUE,
				Job_Type::scheduled => Model::TERTIARY_TRUE
			),
			array(
				Job_Type::name => "Publisher Updates",
				Job_Type::code => "publisher",
				Job_Type::desc => "Load publisher profile data from metadata endpoints (like ComicVine) in batches of 40",
				Job_Type::processor => "ComicVineImporter",
				Job_Type::parameter => json_encode(array( "enqueueBatch" => array("publisher", 40)), JSON_PRETTY_PRINT),
				Job_Type::requires_endpoint => Model::TERTIARY_TRUE,
				Job_Type::scheduled => Model::TERTIARY_TRUE
			),
			array(
				Job_Type::name => "Series Updates",
				Job_Type::code => "series",
				Job_Type::desc => "Load series data from metadata endpoints (like ComicVine) in batches of 5",
				Job_Type::processor => "ComicVineImporter",
				Job_Type::parameter => json_encode(array( "enqueueBatch" => array("series", 5)), JSON_PRETTY_PRINT),
				Job_Type::requires_endpoint => Model::TERTIARY_TRUE,
				Job_Type::scheduled => Model::TERTIARY_TRUE
			),
			array(
				Job_Type::name => "Story Arc Updates",
				Job_Type::code => "story_arc",
				Job_Type::desc => "Load Story Arc data from metadata endpoints (like ComicVine) in batches of 20",
				Job_Type::processor => "ComicVineImporter",
				Job_Type::parameter => json_encode(array( "enqueueBatch" => array("story_arc", 20)), JSON_PRETTY_PRINT),
				Job_Type::requires_endpoint => Model::TERTIARY_TRUE,
				Job_Type::scheduled => Model::TERTIARY_TRUE
			),
			array(
				Job_Type::name => "Publication Updates",
				Job_Type::code => "publication",
				Job_Type::desc => "Load publication data from metadata endpoints (like ComicVine) in batches of 20",
				Job_Type::processor => "ComicVineImporter",
				Job_Type::parameter => json_encode(array( "enqueueBatch" => array("publication", 20)), JSON_PRETTY_PRINT),
				Job_Type::scheduled => Model::TERTIARY_TRUE,
				Job_Type::requires_endpoint => Model::TERTIARY_TRUE
			),
			array(
				Job_Type::name => "SABnzbd status",
				Job_Type::code => "sabnzbd",
				Job_Type::desc => "Update the download status from SABnzbd",
				Job_Type::processor => "FluxStatusUpdater",
				Job_Type::parameter => null,
				Job_Type::requires_endpoint => Model::TERTIARY_TRUE,
				Job_Type::scheduled => Model::TERTIARY_TRUE
			),
			array(
				Job_Type::name => "RSS Feed (nzb)",
				Job_Type::code => "rss",
				Job_Type::desc => "Load data from an RSS feed",
				Job_Type::processor => "RSSImporter",
				Job_Type::scheduled => Model::TERTIARY_TRUE,
				Job_Type::requires_endpoint => Model::TERTIARY_TRUE
			),
			array(
				Job_Type::name => "Import Queue Reprocessor",
				Job_Type::code => "reprocessor",
				Job_Type::desc => "Reprocess items in import queue that paused due to system load",
				Job_Type::processor => "ImportQueueReprocess",
				Job_Type::parameter => null,
				Job_Type::scheduled => Model::TERTIARY_TRUE,
				Job_Type::requires_endpoint => Model::TERTIARY_FALSE
			),
			array(
				Job_Type::name => "Newznab Search Processing",
				Job_Type::code => "newznab_search",
				Job_Type::desc => "Automated Search for Wanted publications against all Newznab endpoints and submission to a Sabnzbd serice.  Requires at least one Newznab endpoint and one Sabnzbd endpoint",
				Job_Type::processor => "NewznabSearchProcessor",
				Job_Type::parameter => null,
				Job_Type::scheduled => Model::TERTIARY_TRUE,
				Job_Type::requires_endpoint => Model::TERTIARY_FALSE
			),
			array(
				Job_Type::name => "PreviewsWorld Updates",
				Job_Type::code => "previewsworld",
				Job_Type::desc => "Load PreviewsWorld release lists",
				Job_Type::processor => "PreviewsWorldImporter",
				Job_Type::parameter => null,
				Job_Type::scheduled => Model::TERTIARY_TRUE,
				Job_Type::requires_endpoint => Model::TERTIARY_TRUE
			)
		);

		foreach ($types as $dict) {
			$existing = \SQL::raw( "select id FROM " . Job_Type::TABLE . " where code = :code", array( ":code" => $dict[Job_Type::code]));
			if ( is_array($existing) && count($existing) > 0) {
				$update = \SQL::Update($job_type_model, Qualifier::Equals( "code", $dict[Job_Type::code]), $dict);
				$update->commitTransaction();
			}
			else {
				$inserts = \SQL::Insert( $job_type_model, array( "name", "code", "desc", "processor", "scheduled", "requires_endpoint", "parameter") );
				$inserts->addRecord( $dict );
				$inserts->commitTransaction(true);
			}
		}

	}
}
