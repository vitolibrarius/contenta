<?php

namespace migration;

use \Migrator as Migrator;
use \MigrationFailedException as MigrationFailedException;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \Database as Database;

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

class Migration_8 extends Migrator
{
	public function sqlite_preUpgrade()
	{
		// backup sqlite database file
		$db_path = Config::GetPath("Database/path", null);
		if ( strlen($db_path) == 0 ) {
			throw new Exception('No path set in configuration for sqlite database');
		}
		$db_file = appendPath($db_path, "contenta.sqlite" );
		$backupDatabase = appendPath($this->scratch, "contenta.Migration_8." . date('Y-m-d.H-i-s') . ".backup");
		file_exists($db_file) == false || copy($db_file, $backupDatabase) || die('Failed to backup ' . $db_file);
	}

	public function sqlite_upgrade()
	{
		$publisher_model = Model::Named("Publisher");
		$storyArc_model = Model::Named("Story_Arc");
		$publication_model = Model::Named("Publication");

		/** PUBLICATION
		Publication::id, Publication::name, Publication::desc, Publication::series_id, Publication::created,
			Publication::pub_date, Publication::issue_num,
			Publication::xurl, Publication::xsource, Publication::xid, Publication::xupdated*/
		$sql = 'CREATE TABLE IF NOT EXISTS ' . Publication::TABLE
			.  " ( "
			. Publication::id . " INTEGER PRIMARY KEY,  "
			. Publication::series_id . "  INTEGER, "
			. Publication::name . "  TEXT COLLATE NOCASE,  "
			. Publication::desc . "  TEXT,  "
			. Publication::pub_date . "  INTEGER, "
			. Publication::issue_num . "  INTEGER, "
			. Publication::created . "  INTEGER, "
			. Publication::xurl . "  TEXT, "
			. Publication::xsource . "  TEXT,  "
			. Publication::xid . "  TEXT, "
			. Publication::xupdated . "  INTEGER, "
			. "FOREIGN KEY (" . Publication::series_id . ") REFERENCES " . Series::TABLE . " (" . Series::id . ")"
			. ")";
		$this->sqlite_execute( Publication::TABLE, $sql, "Create table " . Publication::TABLE );

		/** PUBLICATION_CHARACTER */
		$sql = 'CREATE TABLE IF NOT EXISTS ' . Publication_Character::TABLE . " ( "
			. Publication_Character::id . " INTEGER PRIMARY KEY, "
			. Publication_Character::publication_id . " INTEGER, "
			. Publication_Character::character_id . " INTEGER, "
			. "FOREIGN KEY (". Publication_Character::publication_id .") REFERENCES " . Publication::TABLE . "(id), "
			. "FOREIGN KEY (". Publication_Character::character_id .") REFERENCES " . Character::TABLE . "(id) "
			. ")";
		$this->sqlite_execute( Publication_Character::TABLE, $sql, "Create table " . Publication_Character::TABLE );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS ' . Publication_Character::TABLE . '_index on ' . Publication_Character::TABLE
			. '(' . Publication_Character::publication_id . ', ' . Publication_Character::character_id . ')';
		$this->sqlite_execute( Publication_Character::TABLE, $sql, "Create unique index(publication_id, character_id) on " . Publication_Character::TABLE );

		/** STORY_ARC_PUBLICATION */
		$sql = 'CREATE TABLE IF NOT EXISTS ' . Story_Arc_Publication::TABLE . " ( "
			. Story_Arc_Publication::id . " INTEGER PRIMARY KEY, "
			. Story_Arc_Publication::story_arc_id . " INTEGER, "
			. Story_Arc_Publication::publication_id . " INTEGER, "
			. "FOREIGN KEY (". Story_Arc_Publication::story_arc_id .") REFERENCES " . Story_Arc::TABLE . "(id), "
			. "FOREIGN KEY (". Story_Arc_Publication::publication_id .") REFERENCES " . Publication::TABLE . "(id) "
			. ")";
		$this->sqlite_execute( Story_Arc_Publication::TABLE, $sql, "Create table " . Story_Arc_Publication::TABLE );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS ' . Story_Arc_Publication::TABLE . '_index on ' . Story_Arc_Publication::TABLE
			. '(' . Story_Arc_Publication::story_arc_id . ', ' . Story_Arc_Publication::publication_id . ')';
		$this->sqlite_execute( Story_Arc_Publication::TABLE, $sql, "Create unique index(story_arc_id, publication_id) on " . Story_Arc_Publication::TABLE );

	}

	public function sqlite_postUpgrade()
	{
	}
}