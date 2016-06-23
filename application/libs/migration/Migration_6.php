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

class Migration_6 extends Migrator
{
	public function targetVersion() { return "0.3.1"; }

	public function sqlite_preUpgrade()
	{
		$this->sqlite_backupDatabase();
	}

	public function sqlite_upgrade()
	{
		$publisher_model = Model::Named("Publisher");
		$series_model = Model::Named('Series');
		$series_alias_model = Model::Named('Series_Alias');
		$character_model = Model::Named('Character');
		$character_alias_model = Model::Named('Character_Alias');
		$series_character_model = Model::Named('Series_Character');
		$users_model = Model::Named('Users');
		$user_series_model = Model::Named('User_Series');

		/** SERIES */
		$sql = 'CREATE TABLE IF NOT EXISTS ' . Series::TABLE
			.  " ( "
			. Series::id . " INTEGER PRIMARY KEY,  "
			. Series::parent_id . "  INTEGER, "
			. Series::publisher_id . "  INTEGER, "
			. Series::name . "  TEXT COLLATE NOCASE,  "
			. Series::desc . "  TEXT,  "
			. Series::start_year . "  INTEGER, "
			. Series::issue_count . "  INTEGER, "
			. Series::xurl . "  TEXT, "
			. Series::xsource . "  TEXT,  "
			. Series::xid . "  TEXT, "
			. Series::created . "  INTEGER, "
			. "FOREIGN KEY (" . Series::parent_id . ") REFERENCES " . Series::TABLE . " (" . Series::id . "),"
			. "FOREIGN KEY (" . Series::publisher_id . ") REFERENCES " . Publisher::TABLE . " (" . Publisher::id . ")"
			. ")";
		$this->sqlite_execute( Series::TABLE, $sql, "Create table " . Series::TABLE );


		/** SERIES ALIAS */
		$sql = 'CREATE TABLE IF NOT EXISTS ' . Series_Alias::TABLE . " ( "
				. Series_Alias::id . " INTEGER PRIMARY KEY, "
				. Series_Alias::series_id . " INTEGER, "
				. Series_Alias::name . " TEXT COLLATE NOCASE, "
				. "FOREIGN KEY (". Series_Alias::series_id . ") REFERENCES ". Series::TABLE . "(". Series::id . ")"
				. ")";
		$this->sqlite_execute( Series_Alias::TABLE, $sql, "Create table " . Series_Alias::TABLE );


		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS ' . Series_Alias::TABLE . '_index on ' . Series_Alias::TABLE
			. '(' . Series_Alias::series_id . ', ' . Series_Alias::name . ')';
		$this->sqlite_execute( Series_Alias::TABLE, $sql, "Create unique index(series_id, name) on " . Series_Alias::TABLE );


		/** CHARACTER */
		$sql = 'CREATE TABLE IF NOT EXISTS ' . Character::TABLE . " ( "
				. Character::id . " INTEGER PRIMARY KEY, "
				. Character::publisher_id . " INTEGER, "
				. Character::name . " TEXT COLLATE NOCASE, "
				. Character::realname . " TEXT COLLATE NOCASE, "
				. Character::desc . " TEXT, "
				. Character::popularity . " INTEGER, "
				. Character::gender . " TEXT, "
				. Character::created . " INTEGER, "
				. Character::xurl . " TEXT, "
				. Character::xsource . " TEXT, "
				. Character::xid . " TEXT, "
				. Character::xupdated . " INTEGER, "
				. "FOREIGN KEY (". Character::publisher_id .") REFERENCES publisher(id)"
				. ")";
		$this->sqlite_execute( Character::TABLE, $sql, "Create table " . Character::TABLE );

		/** CHARACTER_ALIAS */
		$sql = 'CREATE TABLE IF NOT EXISTS ' . Character_Alias::TABLE . " ( "
			. Character_Alias::id . " INTEGER PRIMARY KEY, "
			. Character_Alias::character_id . " INTEGER, "
			. Character_Alias::name . " TEXT COLLATE NOCASE, "
			. "FOREIGN KEY (" .Character_Alias::character_id . ") REFERENCES " . Character::TABLE . "(" . Character::id . ")"
			. ")";
		$this->sqlite_execute( Character_Alias::TABLE, $sql, "Create table " . Character_Alias::TABLE );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS ' . Character_Alias::TABLE . '_index on ' . Character_Alias::TABLE
				. '(' . Character_Alias::character_id . ', ' . Character_Alias::name . ')';
		$this->sqlite_execute( Character_Alias::TABLE, $sql, "Create table " . Character_Alias::TABLE );

		/** SERIES_CHARACTER */
		$sql = 'CREATE TABLE IF NOT EXISTS ' . Series_Character::TABLE . " ( "
			. Series_Character::id . " INTEGER PRIMARY KEY, "
			. Series_Character::series_id . " INTEGER, "
			. Series_Character::character_id . " INTEGER, "
			. "FOREIGN KEY (". Series_Character::series_id .") REFERENCES " . Series::TABLE . "(id), "
			. "FOREIGN KEY (". Series_Character::character_id .") REFERENCES " . Character::TABLE . "(id) "
			. ")";
		$this->sqlite_execute( Series_Character::TABLE, $sql, "Create table " . Series_Character::TABLE );

		/** USET_SERIES */
		$sql = 'CREATE TABLE IF NOT EXISTS ' . User_Series::TABLE . " ( "
			. User_Series::id . " INTEGER PRIMARY KEY, "
			. User_Series::user_id . " INTEGER, "
			. User_Series::series_id . " INTEGER, "
			. User_Series::favorite . " INTEGER, "
			. User_Series::read . " INTEGER, "
			. User_Series::mislabeled . " INTEGER, "
			. "FOREIGN KEY (". User_Series::user_id .") REFERENCES " . Users::TABLE . "(id), "
			. "FOREIGN KEY (". User_Series::series_id .") REFERENCES " . Series::TABLE . "(id) "
			. ")";
		$this->sqlite_execute( User_Series::TABLE, $sql, "Create table " . User_Series::TABLE );
	}

	public function sqlite_postUpgrade()
	{
	}
}
