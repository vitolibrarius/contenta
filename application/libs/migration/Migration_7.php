<?php

namespace migration;

use \Migrator as Migrator;
use \MigrationFailedException as MigrationFailedException;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \SQL as SQL;

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

class Migration_7 extends Migrator
{
	public function targetVersion() { return "0.3.2"; }

	public function sqlite_preUpgrade()
	{
		$this->sqlite_backupDatabase();
	}

	public function sqlite_upgrade()
	{
		$publisher_model = Model::Named("Publisher");
		$storyArc_model = Model::Named("Story_Arc");

		/** STORY_ARC */
		$sql = 'CREATE TABLE IF NOT EXISTS ' . Story_Arc::TABLE
			.  " ( "
			. Story_Arc::id . " INTEGER PRIMARY KEY,  "
			. Story_Arc::publisher_id . "  INTEGER, "
			. Story_Arc::name . "  TEXT COLLATE NOCASE,  "
			. Story_Arc::desc . "  TEXT,  "
			. Story_Arc::xurl . "  TEXT, "
			. Story_Arc::xsource . "  TEXT,  "
			. Story_Arc::xid . "  TEXT, "
			. Story_Arc::xupdated . "  INTEGER, "
			. Story_Arc::created . "  INTEGER, "
			. "FOREIGN KEY (" . Story_Arc::publisher_id . ") REFERENCES " . Publisher::TABLE . " (" . Publisher::id . ")"
			. ")";
		$this->sqlite_execute( Story_Arc::TABLE, $sql, "Create table " . Story_Arc::TABLE );

		/** STORY_ARC_CHARACTER */
		$sql = 'CREATE TABLE IF NOT EXISTS ' . Story_Arc_Character::TABLE . " ( "
			. Story_Arc_Character::id . " INTEGER PRIMARY KEY, "
			. Story_Arc_Character::story_arc_id . " INTEGER, "
			. Story_Arc_Character::character_id . " INTEGER, "
			. "FOREIGN KEY (". Story_Arc_Character::story_arc_id .") REFERENCES " . Story_Arc::TABLE . "(id), "
			. "FOREIGN KEY (". Story_Arc_Character::character_id .") REFERENCES " . Character::TABLE . "(id) "
			. ")";
		$this->sqlite_execute( Story_Arc_Character::TABLE, $sql, "Create table " . Story_Arc_Character::TABLE );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS ' . Story_Arc_Character::TABLE . '_index on ' . Story_Arc_Character::TABLE
			. '(' . Story_Arc_Character::story_arc_id . ', ' . Story_Arc_Character::character_id . ')';
		$this->sqlite_execute( Story_Arc_Character::TABLE, $sql, "Create unique index(story_arc_id, character_id) on " . Story_Arc_Character::TABLE );

		/** STORY_ARC_SERIES */
		$sql = 'CREATE TABLE IF NOT EXISTS ' . Story_Arc_Series::TABLE . " ( "
			. Story_Arc_Series::id . " INTEGER PRIMARY KEY, "
			. Story_Arc_Series::story_arc_id . " INTEGER, "
			. Story_Arc_Series::series_id . " INTEGER, "
			. "FOREIGN KEY (". Story_Arc_Series::story_arc_id .") REFERENCES " . Story_Arc::TABLE . "(id), "
			. "FOREIGN KEY (". Story_Arc_Series::series_id .") REFERENCES " . Series::TABLE . "(id) "
			. ")";
		$this->sqlite_execute( Story_Arc_Series::TABLE, $sql, "Create table " . Story_Arc_Series::TABLE );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS ' . Story_Arc_Series::TABLE . '_index on ' . Story_Arc_Series::TABLE
			. '(' . Story_Arc_Series::story_arc_id . ', ' . Story_Arc_Series::series_id . ')';
		$this->sqlite_execute( Story_Arc_Series::TABLE, $sql, "Create unique index(story_arc_id, series_id) on " . Story_Arc_Series::TABLE );
	}

	public function sqlite_postUpgrade()
	{
	}
}
