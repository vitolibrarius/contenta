<?php

namespace migration;

use \Migrator as Migrator;
use \MigrationFailedException as MigrationFailedException;
use \Config as Config;
use \Model as Model;
use \Logger as Logger;
use \SQL as SQL;

use \model\version\Version as Version;
use \model\version\Patch as Patch;

class Migration_0 extends Migrator
{
	public function targetVersion() { return "0.0.1"; }

	public function sqlite_preUpgrade()
	{
		$this->sqlite_backupDatabase();
	}

	public function sqlite_upgrade()
	{
		/** VERSION */
		$sql = "CREATE TABLE IF NOT EXISTS version ( "
			. Version::id . " INTEGER PRIMARY KEY, "
			. Version::code . " TEXT, "
			. Version::major . " INTEGER, "
			. Version::minor . " INTEGER, "
			. Version::patch . " INTEGER, "
			. Version::created . " INTEGER "
		. ")";
		$this->sqlite_execute( "version", $sql, "Create table version" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS version_code on version (code)';
		$this->sqlite_execute( "version", $sql, "Index on version (code)" );
		$sql = 'CREATE  INDEX IF NOT EXISTS version_majorminorpatch on version (major,minor,patch)';
		$this->sqlite_execute( "version", $sql, "Index on version (major,minor,patch)" );


		/** PATCH */
		$sql = "CREATE TABLE IF NOT EXISTS patch ( "
			. Patch::id . " INTEGER PRIMARY KEY, "
			. Patch::name . " TEXT, "
			. Patch::created . " INTEGER, "
			. Patch::version_id . " INTEGER, "
			. "FOREIGN KEY (". Patch::version_id .") REFERENCES " . Version::TABLE . "(" . Version::id . ")"
		. ")";
		$this->sqlite_execute( "patch", $sql, "Create table patch" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS patch_name on patch (name)';
		$this->sqlite_execute( "patch", $sql, "Index on patch (name)" );

		return true;
	}

	public function sqlite_postUpgrade()
	{
	}
}
