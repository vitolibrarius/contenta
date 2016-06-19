<?php

namespace migration;

use \Migrator as Migrator;
use \MigrationFailedException as MigrationFailedException;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \SQL as SQL;

use \model\media\Publisher as Publisher;

class Migration_5 extends Migrator
{
	public function targetVersion() { return "0.3.0"; }

	public function sqlite_preUpgrade()
	{
		$this->sqlite_backupDatabase();
	}

	public function sqlite_upgrade()
	{
		$sql = "CREATE TABLE IF NOT EXISTS publisher ( "
			. Publisher::id . " INTEGER PRIMARY KEY, "
			. Publisher::name . " TEXT, "
			. Publisher::created . " INTEGER, "
			. Publisher::xurl . " TEXT, "
			. Publisher::xsource . " TEXT, "
			. Publisher::xid . " TEXT, "
			. Publisher::xupdated . " INTEGER "
		. ")";
		$this->sqlite_execute( "publisher", $sql, "Create table publisher" );

		$sql = 'CREATE  INDEX IF NOT EXISTS publisher_name on publisher (name)';
		$this->sqlite_execute( "publisher", $sql, "Index on publisher (name)" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS publisher_xidxsource on publisher (xid,xsource)';
		$this->sqlite_execute( "publisher", $sql, "Index on publisher (xid,xsource)" );
	}

	public function sqlite_postUpgrade()
	{
	}
}
