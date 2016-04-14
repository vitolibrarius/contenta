<?php

namespace migration;

use \Migrator as Migrator;
use \MigrationFailedException as MigrationFailedException;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \SQL as SQL;

use utilities\CronEvaluator as CronEvaluator;

use model\Endpoint as Endpoint;

use \model\network\Rss as Rss;


class Migration_12 extends Migrator
{
	public function targetVersion() { return "0.4.1"; }

	public function sqlite_preUpgrade()
	{
		$this->sqlite_backupDatabase();
	}

	public function sqlite_upgrade()
	{
		/** RSS */
		$sql = "CREATE TABLE IF NOT EXISTS rss ( "
			. Rss::id . " INTEGER PRIMARY KEY, "
			. Rss::endpoint_id . " INTEGER, "
			. Rss::created . " INTEGER, "
			. Rss::title . " TEXT, "
			. Rss::desc . " TEXT, "
			. Rss::pub_date . " INTEGER, "
			. Rss::guid . " TEXT, "
			. Rss::clean_name . " TEXT, "
			. Rss::clean_issue . " TEXT, "
			. Rss::clean_year . " INTEGER, "
			. Rss::enclosure_url . " TEXT, "
			. Rss::enclosure_length . " INTEGER, "
			. Rss::enclosure_mime . " TEXT, "
			. Rss::enclosure_hash . " TEXT, "
			. Rss::enclosure_password . " INTEGER, "
			. "FOREIGN KEY (". Rss::endpoint_id .") REFERENCES " . Endpoint::TABLE . "(" . Endpoint::id . ")"
		. ")";
		$this->sqlite_execute( "rss", $sql, "Create table rss" );

		$sql = 'CREATE  INDEX IF NOT EXISTS rss_clean_nameclean_issueclean_year on rss (clean_name,clean_issue,clean_year)';
		$this->sqlite_execute( "rss", $sql, "Index on rss (clean_name,clean_issue,clean_year)" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS rss_guid on rss (guid)';
		$this->sqlite_execute( "rss", $sql, "Index on rss (guid)" );
	}

	public function sqlite_postUpgrade()
	{
	}
}
