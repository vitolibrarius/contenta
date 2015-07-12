<?php

namespace migration;

use \Migrator as Migrator;
use \MigrationFailedException as MigrationFailedException;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \SQL as SQL;

use utilities\CronEvaluator as CronEvaluator;

use model\Series as Series;
use model\Publisher as Publisher;
use model\Series_Alias as Series_Alias;
use model\Character as Character;
use model\Character_Alias as Character_Alias;
use model\Series_Character as Series_Character;
use model\Users as Users;
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
use model\RSS as RSS;


class Migration_12 extends Migrator
{
	public function sqlite_preUpgrade()
	{
		// backup sqlite database file
		$db_path = Config::GetPath("Database/path", null);
		if ( strlen($db_path) == 0 ) {
			throw new \Exception('No path set in configuration for sqlite database');
		}
		$db_file = appendPath($db_path, "contenta.sqlite" );
		$backupDatabase = appendPath($this->scratch, "contenta.Migration_12." . date('Y-m-d.H-i-s') . ".backup");
		file_exists($db_file) == false || copy($db_file, $backupDatabase) || die('Failed to backup ' . $db_file);
	}

	public function sqlite_upgrade()
	{
		/** RSS */
		$sql = 'CREATE TABLE IF NOT EXISTS ' . RSS::TABLE . " ( "
			. RSS::id . " INTEGER PRIMARY KEY, "
			. RSS::endpoint_id . " INTEGER, "
			. RSS::created . " INTEGER, "
			. RSS::title . " TEXT, "
			. RSS::desc . " TEXT, "
			. RSS::pub_date . " INTEGER, "
			. RSS::guid . " TEXT, "
			. RSS::clean_name . " TEXT, "
			. RSS::clean_issue . " TEXT, "
			. RSS::clean_year . " INTEGER, "
			. RSS::enclosure_url . " TEXT, "
			. RSS::enclosure_length . " INTEGER, "
			. RSS::enclosure_mime . " TEXT, "
			. RSS::enclosure_hash . " TEXT, "
			. RSS::enclosure_password . " INTEGER, "
			. "FOREIGN KEY (". RSS::endpoint_id .") REFERENCES " . Endpoint::TABLE . "(" . Endpoint::id . ")"
			. ")";
		$this->sqlite_execute( RSS::TABLE, $sql, "Create table " . RSS::TABLE );

		$indexStatements = array(
			array( Migrator::IDX_TABLE => RSS::TABLE, Migrator::IDX_COLS => array( RSS::title ) ),
			array( Migrator::IDX_TABLE => RSS::TABLE, Migrator::IDX_COLS => array( RSS::clean_name ) ),
			array( Migrator::IDX_TABLE => RSS::TABLE, Migrator::IDX_COLS => array( RSS::endpoint_id, RSS::guid ), Migrator::IDX_UNIQUE => true ),
		);
		foreach( $indexStatements as $config ) {
			$table = $config[Migrator::IDX_TABLE];
			$columns = $config[Migrator::IDX_COLS];
			$indexName = $table . '_' . implode("", $columns);
			$unique = (isset($config[Migrator::IDX_UNIQUE]) ? boolval($config[Migrator::IDX_UNIQUE]) : false);

			$statement = 'CREATE ' . ($unique ? 'UNIQUE' : '') . ' INDEX IF NOT EXISTS ' . $indexName . ' on ' . $table . '(' . implode(",", $columns) . ')';
			$this->sqlite_execute( $table, $statement, "Index on " . $table );
		}
	}

	public function sqlite_postUpgrade()
	{
	}
}