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
use \model\media\Story_Arc_Publication as Story_Arc_Publication;

use \model\media\Publication as Publication;
use \model\media\Publication_Character as Publication_Character;
use \model\media\Media_Type as Media_Type;
use \model\media\Media as Media;

class Migration_8 extends Migrator
{
	public function targetVersion() { return "0.3.3"; }

	public function sqlite_preUpgrade()
	{
		$this->sqlite_backupDatabase();
	}

	public function sqlite_upgrade()
	{
		$publisher_model = Model::Named("Publisher");
		$storyArc_model = Model::Named("Story_Arc");
		$publication_model = Model::Named("Publication");
		$media_type_model = Model::Named("Media_Type");

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
			. Publication::issue_num . "  TEXT, "
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

		/** MEDIA_TYPE */
		$sql = 'CREATE TABLE IF NOT EXISTS ' . Media_Type::TABLE . " ( "
			. Media_Type::id . " INTEGER PRIMARY KEY, "
			. Media_Type::code . " TEXT, "
			. Media_Type::name . " TEXT "
			. ")";
		$this->sqlite_execute( Media_Type::TABLE, $sql, "Create table " . Media_Type::TABLE );

		/** MEDIA */
		$sql = 'CREATE TABLE IF NOT EXISTS ' . Media::TABLE . " ( "
			. Media::id . " INTEGER PRIMARY KEY, "
			. Media::publication_id . " INTEGER, "
			. Media::type_id . " INTEGER, "
			. Media::created . " INTEGER, "
			. Media::filename . " TEXT, "
			. Media::original_filename . " TEXT, "
			. Media::checksum . " TEXT, "
			. Media::size . " INTEGER, "
			. "FOREIGN KEY (". Media::publication_id .") REFERENCES " . Publication::TABLE . "(" . Publication::id . "),"
			. "FOREIGN KEY (". Media::type_id .") REFERENCES " . Media_Type::TABLE . "(" . Media_Type::id . ")"
			. ")";
		$this->sqlite_execute( Media::TABLE, $sql, "Create table " . Media::TABLE );
	}

	public function sqlite_postUpgrade()
	{
		$media_type_model = Model::Named("Media_Type");
		$types = array(
			'cbz' => 'Comic Book',
			'cbr' => 'Comic Book',
			'epub' => 'Electronic Book',
			'pdf' => 'Portable Document Format'
		);
		foreach ($types as $code => $name) {
			if ($media_type_model->objectForCode($code) == false)
			{
				$newObjId = \SQL::Insert($media_type_model)->addRecord(array(
					Media_Type::code => $code,
					Media_Type::name => $name
					)
				)->commitTransaction();
			}
		}
	}
}
