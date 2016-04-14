<?php

namespace migration;

use \Migrator as Migrator;
use \MigrationFailedException as MigrationFailedException;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \SQL as SQL;

use model\Publisher as Publisher;

class Migration_5 extends Migrator
{
	public function targetVersion() { return "0.3.0"; }

	public function sqlite_preUpgrade()
	{
		$this->sqlite_backupDatabase();
	}

	public function sqlite_upgrade()
	{
		$model = Model::Named("Publisher");

		$sql = "CREATE TABLE IF NOT EXISTS " . Publisher::TABLE
				. " ( "
				. Publisher::id . " INTEGER PRIMARY KEY, "
				. Publisher::name . " TEXT COLLATE NOCASE,  "
				. Publisher::xurl . " TEXT,  "
				. Publisher::xsource . " TEXT,  "
				. Publisher::xid . " TEXT, "
				. Publisher::created . " INTEGER, "
				. Publisher::updated . " INTEGER, "
				. Publisher::xupdated . " INTEGER "
				. ")";
		$this->sqlite_execute( Publisher::TABLE, $sql, "Create table " . Publisher::TABLE );

		$this->sqlite_execute(
			Publisher::TABLE,
			'CREATE INDEX IF NOT EXISTS ' . Publisher::TABLE . '_nameindex on ' . Publisher::TABLE . '(' . Publisher::name . ')',
			"Index on " . Publisher::TABLE
		);
	}

	public function sqlite_postUpgrade()
	{
	}
}
