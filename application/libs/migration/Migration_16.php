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

use \model\jobs\Job_Type as Job_Type;
use \model\jobs\Job_Running as Job_Running;
use \model\jobs\Job as Job;
use \model\network\Endpoint as Endpoint;
use \model\network\Endpoint_Type as Endpoint_Type;

use \model\pull_list\Pull_List as Pull_List;
use \model\pull_list\Pull_List_Group as Pull_List_Group;
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
		$table_fields = \SQL::pragma_TableInfo(Pull_List_Item::TABLE);
		if ( isset($table_fields[ Pull_List_Item::search_name ]) == false ) {
			// easier to just drop these tables and re-create them
			$dropOrder = array(
				Pull_List_Expansion::TABLE,
				Pull_List_Exclusion::TABLE,
				Pull_List_Group::TABLE,
				Pull_List_Item::TABLE,
				Pull_List::TABLE
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

		/** PULL_LIST_GROUP */
		$sql = "CREATE TABLE IF NOT EXISTS pull_list_group ( "
			. Pull_List_Group::id . " INTEGER PRIMARY KEY, "
			. Pull_List_Group::name . " TEXT, "
			. Pull_List_Group::data . " TEXT, "
			. Pull_List_Group::created . " INTEGER "
		. ")";
		$this->sqlite_execute( "pull_list_group", $sql, "Create table pull_list_group" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS pull_list_group_data on pull_list_group (data)';
		$this->sqlite_execute( "pull_list_group", $sql, "Index on pull_list_group (data)" );
		$sql = 'CREATE  INDEX IF NOT EXISTS pull_list_group_name on pull_list_group (name)';
		$this->sqlite_execute( "pull_list_group", $sql, "Index on pull_list_group (name)" );

		/** PULL_LIST_ITEM */
		$sql = "CREATE TABLE IF NOT EXISTS pull_list_item ( "
			. Pull_List_Item::id . " INTEGER PRIMARY KEY, "
			. Pull_List_Item::data . " TEXT, "
			. Pull_List_Item::created . " INTEGER, "
			. Pull_List_Item::search_name . " TEXT, "
			. Pull_List_Item::name . " TEXT, "
			. Pull_List_Item::issue . " TEXT, "
			. Pull_List_Item::year . " INTEGER, "
			. Pull_List_Item::pull_list_id . " INTEGER, "
			. Pull_List_Item::pull_list_group_id . " INTEGER, "
			. "FOREIGN KEY (". Pull_List_Item::pull_list_id .") REFERENCES " . Pull_List::TABLE . "(" . Pull_List::id . ")"
		. ")";
		$this->sqlite_execute( "pull_list_item", $sql, "Create table pull_list_item" );

		$sql = 'CREATE  INDEX IF NOT EXISTS pull_list_item_name on pull_list_item (name)';
		$this->sqlite_execute( "pull_list_item", $sql, "Index on pull_list_item (name)" );
		$sql = 'CREATE  INDEX IF NOT EXISTS pull_list_item_search_name on pull_list_item (search_name)';
		$this->sqlite_execute( "pull_list_item", $sql, "Index on pull_list_item (search_name)" );

		/** PULL_LIST_EXCL */
		$sql = "CREATE TABLE IF NOT EXISTS pull_list_excl ( "
			. Pull_List_Exclusion::id . " INTEGER PRIMARY KEY, "
			. Pull_List_Exclusion::pattern . " TEXT, "
			. Pull_List_Exclusion::type . " TEXT, "
			. Pull_List_Exclusion::created . " INTEGER, "
			. Pull_List_Exclusion::endpoint_type_id . " INTEGER, "
			. "FOREIGN KEY (". Pull_List_Exclusion::endpoint_type_id .") REFERENCES " . Endpoint_Type::TABLE . "(" . Endpoint_Type::id . ")"
		. ")";
		$this->sqlite_execute( "pull_list_excl", $sql, "Create table pull_list_excl" );

		/** PULL_LIST_EXPANSION */
		$sql = "CREATE TABLE IF NOT EXISTS pull_list_expansion ( "
			. Pull_List_Expansion::id . " INTEGER PRIMARY KEY, "
			. Pull_List_Expansion::sequence . " INTEGER, "
			. Pull_List_Expansion::pattern . " TEXT, "
			. Pull_List_Expansion::replace . " TEXT, "
			. Pull_List_Expansion::created . " INTEGER, "
			. Pull_List_Expansion::endpoint_type_id . " INTEGER, "
			. "FOREIGN KEY (". Pull_List_Expansion::endpoint_type_id .") REFERENCES " . Endpoint_Type::TABLE . "(" . Endpoint_Type::id . ")"
		. ")";
		$this->sqlite_execute( "pull_list_expansion", $sql, "Create table pull_list_expansion" );
	}

	public function sqlite_postUpgrade()
	{
		$endpoint_type_data = array(
			"Newznab" => "Newznab",
			"RSS" => "RSS",
			"ComicVine" => "ComicVine",
			"SABnzbd" => "SABnzbd",
			"PreviewsWorld" => "TXT_",
		);
		foreach ($endpoint_type_data as $code => $data) {
			$update = \SQL::Update(Model::Named("Endpoint_Type"), Qualifier::Equals( "code", $code ), array("data_type" => $data));
			$update->commitTransaction();
		}

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

		$expansion_model = Model::Named('Pull_List_Expansion');
		$pw_endpoint_type = Model::Named('Endpoint_Type')->objectForCode(Endpoint_Type::PreviewsWorld);
		$pl_expansions = array(
			0 => array(
				'(MR)' => '',
				'(NOTE PRICE)' => ''
			),
			1 => array(
				'GFT GRIMM FAIRY TALES' => 'GRIMM FAIRY TALES PRESENTS',
				'GFT GRIMM TALES OF TERROR' => 'GRIMM TALES OF TERROR'
			),
			2 => array(
				'GFT' => 'GRIMM FAIRY TALES PRESENTS',
				'SW ' => 'Star Wars ',
				'HELLRAISER' => 'CLIVE BARKER\'S HELLRAISER',
				'BTVS SEASON 9' => 'BUFFY THE VAMPIRE SLAYER SEASON NINE',
				'BTVS SEASON 10' => 'BUFFY THE VAMPIRE SLAYER SEASON 10',
				'SUPURBIA' => 'GRACE RANDOLPH\'S SUPURBIA'
			)
		);
		$inserts = \SQL::Insert(  Model::Named("Pull_List_Expansion"), array(
			"sequence",
			"pattern",
			"replace",
			"created",
			"endpoint_type_id"
			)
		);
		foreach ($pl_expansions as $sequence => $list) {
			foreach ($list as $pattern => $replacement) {
				$existing = \SQL::raw( "select id FROM " . Pull_List_Expansion::TABLE . " where endpoint_type_id = :type_id and pattern = :pattern" ,
					array(":type_id" => $pw_endpoint_type->id, ":pattern" => $pattern));
				if ( is_array($existing) == false || count($existing) == 0) {
					$inserts->addRecord( array(
						"sequence" => $sequence,
						"pattern" => $pattern,
						"replace" => $replacement,
						"created" => time(),
						"endpoint_type_id" => $pw_endpoint_type->id
						)
					);
				}
			}
		}
		if ( $inserts->hasData() ) {
			$inserts->commitTransaction(true);
		}

		$inserts = \SQL::Insert( Model::Named("Pull_List_Exclusion"), array(
			"pattern",
			"type",
			"created",
			"endpoint_type_id"
			)
		);
		$pl_exclusions = array(
			"item" => array(
				'2ND PTG', // 2nd printing
				'3RD PTG',
				'4TH PTG',
				'5TH PTG',
				'NEW PTG',
				'POSTER',	// poster
				'COMBO PACK',	// combo
				' HC ',		// hard cover
				' TP ',		// trade paperback
				' DVD ',	// dvd
				' BD ',		// blue ray
				'BOX SET',	// box set
				'WALL CAL',	// wall calendar
				' SGN',		// signed
				' FIG',		// figurine
				' STATUE',	// statue
				' AF ',		// action figure
				'DIECAST',	// DIECAST metal
				'  T/S ',	// t-shirt
				'T-SHIRT',	// t-shirt
				' TOTE',	// tote
				' CUP',		// cup
				'TRAVEL MUG',	// mug
				'WATER BOTTLE',	// water bottle
				'CERAMIC STEIN',// stein
				'CERAMIC MUG',	// mugs again
				' EXP ',	// expansion
				'MDL KIT'	// model kit
			),
			"group" => array(
				'PREVIEWS',
				'Shipping',
				'Every Wednesday',
				'Please check with',
				'PREMIER PUBLISHERS',
				'BOOKS',
				'COLLECTIBLES',
				'MCFARLANE TOYS',
				'New Releases',
				'Upcoming Releases',
				'SUPPLIES',
				'MERCHANDISE'
			)
		);
		foreach ($pl_exclusions as $type => $list) {
			foreach( $list as $value ) {
				$existing = \SQL::raw( "select id FROM " . Pull_List_Exclusion::TABLE . " where endpoint_type_id = :type_id and pattern = :pattern" ,
					array(":type_id" => $pw_endpoint_type->id, ":pattern" => $pattern));
				if ( is_array($existing) == false || count($existing) == 0) {
					$inserts->addRecord( array(
						"pattern" => $value,
						"type" => $type,
						"created" => time(),
						"endpoint_type_id" => $pw_endpoint_type->id
						)
					);
				}
			}
		}
		if ( $inserts->hasData() ) {
			$inserts->commitTransaction(true);
		}

		return true;
	}
}
