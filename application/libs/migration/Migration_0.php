<?php

namespace migration;

use \Migrator as Migrator;
use \MigrationFailedException as MigrationFailedException;
use \Config as Config;
use \Logger as Logger;
use \SQL as SQL;

use model\Version as Version;
use model\Patch as Patch;


class Migration_0 extends Migrator
{
	public function sqlite_preUpgrade()
	{
		// backup sqlite database file
		$db_path = Config::GetPath("Database/path", null);
		if ( strlen($db_path) == 0 ) {
			throw new Exception('No path set in configuration for sqlite database');
		}
		$db_file = appendPath($db_path, "contenta.sqlite" );
		$backupDatabase = appendPath($this->scratch, "contenta.Migration_0." . date('Y-m-d.H-i-s') . ".backup");
		file_exists($db_file) == false || copy($db_file, $backupDatabase) || die('Failed to backup ' . $db_file);
	}

	public function sqlite_upgrade()
	{
		$version_model = new Version();
		$patch_model = new Patch();

		$sql = 'CREATE TABLE IF NOT EXISTS ' . Version::TABLE . " ( "
				. Version::id . " INTEGER PRIMARY KEY, "
				. Version::code . " TEXT COLLATE NOCASE, "
				. Version::major . " INTEGER, "
				. Version::minor . " INTEGER, "
				. Version::patch . " INTEGER, "
				. Version::created . " INTEGER, "
				. Version::hash_code . " TEXT "
				. ")";
		$this->sqlite_execute( Version::TABLE, $sql, "Create table " . Version::TABLE );

		$sql = 'CREATE TABLE IF NOT EXISTS ' . Patch::TABLE . " ( "
				. Patch::id . " INTEGER PRIMARY KEY, "
				. Patch::name . " TEXT COLLATE NOCASE, "
				. Patch::version_id . " INTEGER, "
				. Patch::created . " INTEGER, "
				. "FOREIGN KEY (". Patch::version_id .") REFERENCES " . Version::TABLE ." ( ".Version::id." )"
				. ")";
		$this->sqlite_execute( Patch::TABLE, $sql, "Create table " . Patch::TABLE );

		return true;
	}

	public function sqlite_postUpgrade()
	{
	}
}
