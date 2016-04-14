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

use model\Job_Type as Job_Type;
use \model\Job as Job;
use \model\Endpoint as Endpoint;

use \model\pull_list\Pull_List as Pull_List;
use \model\pull_list\Pull_List_Item as Pull_List_Item;
use \model\pull_list\Pull_List_Exclusion as Pull_List_Exclusion;
use \model\pull_list\Pull_List_Expansion as Pull_List_Expansion;

class Migration_16 extends Migrator
{
	public function targetVersion() { return "0.5.0"; }

	public function sqlite_preUpgrade()
	{
		$this->sqlite_backupDatabase();
	}

	public function sqlite_upgrade()
	{
			/** PULL_LIST */
		$sql = "CREATE TABLE IF NOT EXISTS pull_list ( "
			. Pull_List::id . " INTEGER PRIMARY KEY, "
			. Pull_List::name . " TEXT, "
			. Pull_List::etag . " TEXT, "
			. Pull_List::created . " INTEGER, "
			. Pull_List::published . " INTEGER, "
			. Pull_List::endpoint_id . " INTEGER, "
			. "FOREIGN KEY (". Pull_List::endpoint_id .") REFERENCES " . Endpoint::TABLE . "(" . Endpoint::id . ")"
		. ")";
		$this->sqlite_execute( "pull_list", $sql, "Create table pull_list" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS pull_list_etag on pull_list (etag)';
		$this->sqlite_execute( "pull_list", $sql, "Index on pull_list (etag)" );

		/** PULL_LIST_ITEM */
		$sql = "CREATE TABLE IF NOT EXISTS pull_list_item ( "
			. Pull_List_Item::id . " INTEGER PRIMARY KEY, "
			. Pull_List_Item::group_name . " TEXT, "
			. Pull_List_Item::data . " TEXT, "
			. Pull_List_Item::created . " INTEGER, "
			. Pull_List_Item::name . " TEXT, "
			. Pull_List_Item::issue . " TEXT, "
			. Pull_List_Item::year . " INTEGER, "
			. Pull_List_Item::pull_list_id . " INTEGER, "
			. "FOREIGN KEY (". Pull_List_Item::pull_list_id .") REFERENCES " . Pull_List::TABLE . "(" . Pull_List::id . ")"
		. ")";
		$this->sqlite_execute( "pull_list_item", $sql, "Create table pull_list_item" );

		$sql = 'CREATE  INDEX IF NOT EXISTS pull_list_item_name on pull_list_item (name)';
		$this->sqlite_execute( "pull_list_item", $sql, "Index on pull_list_item (name)" );

		/** PULL_LIST_EXCL */
		$sql = "CREATE TABLE IF NOT EXISTS pull_list_excl ( "
			. Pull_List_Exclusion::id . " INTEGER PRIMARY KEY, "
			. Pull_List_Exclusion::pattern . " TEXT, "
			. Pull_List_Exclusion::type . " TEXT, "
			. Pull_List_Exclusion::created . " INTEGER, "
			. Pull_List_Exclusion::endpoint_id . " INTEGER, "
			. "FOREIGN KEY (". Pull_List_Exclusion::endpoint_id .") REFERENCES " . Endpoint::TABLE . "(" . Endpoint::id . ")"
		. ")";
		$this->sqlite_execute( "pull_list_excl", $sql, "Create table pull_list_excl" );

		/** PULL_LIST_EXPANSION */
		$sql = "CREATE TABLE IF NOT EXISTS pull_list_expansion ( "
			. Pull_List_Expansion::id . " INTEGER PRIMARY KEY, "
			. Pull_List_Expansion::pattern . " TEXT, "
			. Pull_List_Expansion::replace . " TEXT, "
			. Pull_List_Expansion::created . " INTEGER, "
			. Pull_List_Expansion::endpoint_id . " INTEGER, "
			. "FOREIGN KEY (". Pull_List_Expansion::endpoint_id .") REFERENCES " . Endpoint::TABLE . "(" . Endpoint::id . ")"
		. ")";
		$this->sqlite_execute( "pull_list_expansion", $sql, "Create table pull_list_expansion" );
	}

	public function sqlite_postUpgrade()
	{
		$job_type_model = Model::Named("Job_Type");
		$types = array(
			array(
				Job_Type::name => "Newznab Search Processing",
				Job_Type::code => "newznab_search",
				Job_Type::desc => "Automated Search for Wanted publications against all Newznab endpoints and submission to a Sabnzbd serice.  Requires at least one Newznab endpoint and one Sabnzbd endpoint",
				Job_Type::processor => "NewznabSearchProcessor",
				Job_Type::parameter => null,
				Job_Type::scheduled => Model::TERTIARY_TRUE,
				Job_Type::requires_endpoint => Model::TERTIARY_FALSE
			)
		);

		foreach ($types as $dict) {
			$existing = \SQL::raw( "select id FROM " . Job_Type::TABLE . " where code = :code", array( ":code" => $dict[Job_Type::code]));
			if ( is_array($existing) && count($existing) > 0) {
				$update = \SQL::Update($job_type_model, Qualifier::Equals( "code", $dict[Job_Type::code]), $dict);
				$update->commitTransaction();
			}
			else {
				$inserts = \SQL::Insert( $job_type_model, array(
						"name",
						"code",
						"desc",
						"processor",
						"scheduled",
						"parameter",
						"requires_endpoint"
					)
				);
				$inserts->addRecord( $dict );
				$inserts->commitTransaction(true);
			}
		}

		$job_model = Model::Named("Job");
		$existing = \SQL::raw( "select id, code FROM " . Job_Type::TABLE );
		$map = array();
		foreach ($existing as $row) {
			$map[$row->code] = $row->id;
		}

		$job_data = array(
			array(
				"type_id" => $map["newznab_search"],
				"minute" => "10",
				"hour" => "3",
				"dayOfWeek" => "*",
				"one_shot" => Model::TERTIARY_FALSE,
				"enabled" => Model::TERTIARY_TRUE
			)
		);

		foreach ($job_data as $dict) {
			$existing = \SQL::raw( "select id FROM " . Job::TABLE . " where type_id = :type_id",
				array(":type_id" => $dict["type_id"]));
			if ( is_array($existing) == false || count($existing) == 0) {
				$cronEval = new CronEvaluator( $dict["minute"], $dict["hour"], $dict["dayOfWeek"]);
				$dict["next"] = $cronEval->nextDate()->getTimestamp();

				$inserts = \SQL::Insert( $job_model, array( "type_id", "minute", "hour", "dayOfWeek", "one_shot", "enabled", "next") );
				$inserts->addRecord( $dict );
				$inserts->commitTransaction(true);
			}
		}

		return true;
	}
}
