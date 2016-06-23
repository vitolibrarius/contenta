<?php

namespace migration;

use \Migrator as Migrator;
use \MigrationFailedException as MigrationFailedException;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \SQL as SQL;
use db\Qualifier as Qualifier;

use utilities\CronEvaluator as CronEvaluator;

use \model\media\Series as Series;
use \model\media\Publisher as Publisher;
use \model\media\Series_Alias as Series_Alias;
use \model\media\Character as Character;
use \model\media\Character_Alias as Character_Alias;
use \model\media\Series_Character as Series_Character;
use \model\user\Users as Users;
use \model\media\User_Series as User_Series;
use \model\media\Story_Arc as Story_Arc;
use \model\media\Story_Arc_Character as Story_Arc_Character;
use \model\media\Story_Arc_Series as Story_Arc_Series;
use \model\media\Story_Arc_Publication as Story_Arc_Publication;
use \model\media\Publication as Publication;
use \model\media\Publication_Character as Publication_Character;
use \model\media\Media_Type as Media_Type;
use \model\media\Media as Media;
use \model\network\Endpoint as Endpoint;

use \model\jobs\Job_Type as Job_Type;
use \model\jobs\Job_Running as Job_Running;
use \model\jobs\Job as Job;

//
class Migration_11 extends Migrator
{
	public function targetVersion() { return "0.4.0"; }

	public function sqlite_preUpgrade()
	{
		$this->sqlite_backupDatabase();
	}

	public function sqlite_upgrade()
	{
		/** JOB_TYPE */
		$table_fields = \SQL::pragma_TableInfo(Job_Type::TABLE);
		if ( isset($table_fields[ Job_Type::parameter ]) == false ) {
			$this->sqlite_execute(
				Job_Type::TABLE ,
				"ALTER TABLE " . Job_Type::TABLE . " ADD COLUMN " . Job_Type::parameter . " TEXT",
				"Adding the parameter column to Job_Type"
			);
		}

		/** JOB */
		$table_fields = \SQL::pragma_TableInfo(Job::TABLE);
		if ( isset($table_fields[ Job::elapsed ]) == false ) {
			$this->sqlite_execute(
				Job::TABLE ,
				"ALTER TABLE " . Job::TABLE . " ADD COLUMN " . Job::elapsed . " integer",
				"Adding the elapsed column to Job"
			);
		}

		/** SERIES */
		$table_fields = \SQL::pragma_TableInfo(Series::TABLE);
		if ( isset($table_fields[ Series::xupdated ]) == false ) {
			$this->sqlite_execute(
				Series::TABLE ,
				"ALTER TABLE " . Series::TABLE . " ADD COLUMN " . Series::xupdated . " INTEGER",
				"Adding the parameter column to Series"
			);
		}
	}

	public function sqlite_postUpgrade()
	{
		foreach( array( Job_Running::TABLE, Job::TABLE, Job_Type::TABLE) as $table ) {
			$this->sqlite_execute( $table,	"DELETE FROM " . $table, "Delete data from " . $table );
		}

		$job_type_model = Model::Named("Job_Type");
		$types = array(
			array(
				Job_Type::name => "Character Updates",
				Job_Type::code => "character",
				Job_Type::desc => "Load character profile data from metadata endpoints (like ComicVine) in batches of 20",
				Job_Type::processor => "ComicVineImporter",
				Job_Type::parameter => json_encode(array( "enqueueBatch" => array("character", 20)), JSON_PRETTY_PRINT),
				Job_Type::scheduled => 1
			),
			array(
				Job_Type::name => "Publisher Updates",
				Job_Type::code => "publisher",
				Job_Type::desc => "Load publisher profile data from metadata endpoints (like ComicVine) in batches of 40",
				Job_Type::processor => "ComicVineImporter",
				Job_Type::parameter => json_encode(array( "enqueueBatch" => array("publisher", 40)), JSON_PRETTY_PRINT),
				Job_Type::scheduled => 1
			),
			array(
				Job_Type::name => "Series Updates",
				Job_Type::code => "series",
				Job_Type::desc => "Load series data from metadata endpoints (like ComicVine) in batches of 5",
				Job_Type::processor => "ComicVineImporter",
				Job_Type::parameter => json_encode(array( "enqueueBatch" => array("series", 5)), JSON_PRETTY_PRINT),
				Job_Type::scheduled => 1
			),
			array(
				Job_Type::name => "Story Arc Updates",
				Job_Type::code => "story_arc",
				Job_Type::desc => "Load Story Arc data from metadata endpoints (like ComicVine) in batches of 20",
				Job_Type::processor => "ComicVineImporter",
				Job_Type::parameter => json_encode(array( "enqueueBatch" => array("story_arc", 20)), JSON_PRETTY_PRINT),
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
				$update = \SQL::Update($job_type_model, Qualifier::Equals( "code", $dict[Job_Type::code]), $dict);
				$update->commitTransaction();
			}
			else {
				$inserts = \SQL::Insert( $job_type_model, array( "name", "code", "desc", "processor", "scheduled", "parameter") );
				$inserts->addRecord( $dict );
				$inserts->commitTransaction(true);
			}
		}

		// now create some default jobs
		$job_model = Model::Named("Job");
		$existing = \SQL::raw( "select id, code FROM " . Job_Type::TABLE );
		$map = array();
		foreach ($existing as $row) {
			$map[$row->code] = $row->id;
		}

		$job_data = array(
			array(
				"type_id" => $map["publisher"],
				"minute" => "10",
				"hour" => "1",
				"dayOfWeek" => "1",
				"one_shot" => Model::TERTIARY_FALSE,
				"enabled" => Model::TERTIARY_FALSE
			),
			array(
				"type_id" => $map["series"],
				"minute" => "*/20",
				"hour" => "0-6",
				"dayOfWeek" => "1,2,3,4,5",
				"one_shot" => Model::TERTIARY_FALSE,
				"enabled" => Model::TERTIARY_FALSE
			),
			array(
				"type_id" => $map["character"],
				"minute" => "*/30",
				"hour" => "0-6",
				"dayOfWeek" => "1,2,3,4,5",
				"one_shot" => Model::TERTIARY_FALSE,
				"enabled" => Model::TERTIARY_FALSE
			),
			array(
				"type_id" => $map["story_arc"],
				"minute" => "*/30",
				"hour" => "0-6",
				"dayOfWeek" => "1,2,3,4,5",
				"one_shot" => Model::TERTIARY_FALSE,
				"enabled" => Model::TERTIARY_FALSE
			)
		);

		foreach ($job_data as $dict) {
			$cronEval = new CronEvaluator( $dict["minute"], $dict["hour"], $dict["dayOfWeek"]);
			$dict["next"] = $cronEval->nextDate()->getTimestamp();

			$inserts = \SQL::Insert( $job_model, array( "type_id", "minute", "hour", "dayOfWeek", "one_shot", "enabled", "next") );
			$inserts->addRecord( $dict );
			$inserts->commitTransaction(true);
		}
	}
}
