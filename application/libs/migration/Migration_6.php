<?php

namespace migration;

use \Migrator as Migrator;
use \MigrationFailedException as MigrationFailedException;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \Database as Database;
use \SQL as SQL;

use \model\user\Users as Users;

use \model\media\Series as Series;
use \model\media\Publisher as Publisher;
use \model\media\Series_Alias as Series_Alias;
use \model\media\Character as Character;
use \model\media\Character_Alias as Character_Alias;
use \model\media\Series_Character as Series_Character;
use \model\media\Story_Arc as Story_Arc;
use \model\media\Story_Arc_Character as Story_Arc_Character;
use \model\media\Story_Arc_Series as Story_Arc_Series;
use \model\media\Story_Arc_Publication as Story_Arc_Publication;
use \model\media\Publication as Publication;
use \model\media\Publication_Character as Publication_Character;
use \model\media\Media_Type as Media_Type;
use \model\media\Media as Media;

class Migration_6 extends Migrator
{
	public function targetVersion() { return "0.6.0"; }

	public function sqlite_preUpgrade()
	{
		$this->sqlite_backupDatabase();
	}

	public function sqlite_upgrade()
	{
		/** MEDIA_TYPE */
		$sql = "CREATE TABLE IF NOT EXISTS media_type ( "
			. Media_Type::code . " TEXT PRIMARY KEY, "
			. Media_Type::name . " TEXT "
		. ")";
		$this->sqlite_execute( "media_type", $sql, "Create table media_type" );

		$sql = 'CREATE  INDEX IF NOT EXISTS media_type_name on media_type (name)';
		$this->sqlite_execute( "media_type", $sql, "Index on media_type (name)" );

		/** PUBLISHER */
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

		/** SERIES */
		$sql = "CREATE TABLE IF NOT EXISTS series ( "
			. Series::id . " INTEGER PRIMARY KEY, "
			. Series::publisher_id . " INTEGER, "
			. Series::created . " INTEGER, "
			. Series::name . " TEXT, "
			. Series::search_name . " TEXT, "
			. Series::desc . " TEXT, "
			. Series::start_year . " INTEGER, "
			. Series::issue_count . " INTEGER, "
			. Series::pub_active . " INTEGER, "
			. Series::pub_wanted . " INTEGER, "
			. Series::pub_available . " INTEGER, "
			. Series::pub_cycle . " INTEGER, "
			. Series::pub_count . " INTEGER, "
			. Series::xurl . " TEXT, "
			. Series::xsource . " TEXT, "
			. Series::xid . " TEXT, "
			. Series::xupdated . " INTEGER, "
			. "FOREIGN KEY (". Series::publisher_id .") REFERENCES " . Publisher::TABLE . "(" . Publisher::id . ")"
		. ")";
		$this->sqlite_execute( "series", $sql, "Create table series" );

		$sql = 'CREATE  INDEX IF NOT EXISTS series_name on series (name)';
		$this->sqlite_execute( "series", $sql, "Index on series (name)" );
		$sql = 'CREATE  INDEX IF NOT EXISTS series_search_name on series (search_name)';
		$this->sqlite_execute( "series", $sql, "Index on series (search_name)" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS series_xidxsource on series (xid,xsource)';
		$this->sqlite_execute( "series", $sql, "Index on series (xid,xsource)" );

		/** STORY_ARC */
		$sql = "CREATE TABLE IF NOT EXISTS story_arc ( "
			. Story_Arc::id . " INTEGER PRIMARY KEY, "
			. Story_Arc::publisher_id . " INTEGER, "
			. Story_Arc::created . " INTEGER, "
			. Story_Arc::name . " TEXT, "
			. Story_Arc::desc . " TEXT, "
			. Story_Arc::pub_active . " INTEGER, "
			. Story_Arc::pub_wanted . " INTEGER, "
			. Story_Arc::pub_cycle . " INTEGER, "
			. Story_Arc::pub_available . " INTEGER, "
			. Story_Arc::pub_count . " INTEGER, "
			. Story_Arc::xurl . " TEXT, "
			. Story_Arc::xsource . " TEXT, "
			. Story_Arc::xid . " TEXT, "
			. Story_Arc::xupdated . " INTEGER, "
			. "FOREIGN KEY (". Story_Arc::publisher_id .") REFERENCES " . Publisher::TABLE . "(" . Publisher::id . ")"
		. ")";
		$this->sqlite_execute( "story_arc", $sql, "Create table story_arc" );

		$sql = 'CREATE  INDEX IF NOT EXISTS story_arc_name on story_arc (name)';
		$this->sqlite_execute( "story_arc", $sql, "Index on story_arc (name)" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS story_arc_xidxsource on story_arc (xid,xsource)';
		$this->sqlite_execute( "story_arc", $sql, "Index on story_arc (xid,xsource)" );

		/** CHARACTER */
		$sql = "CREATE TABLE IF NOT EXISTS character ( "
			. Character::id . " INTEGER PRIMARY KEY, "
			. Character::publisher_id . " INTEGER, "
			. Character::created . " INTEGER, "
			. Character::name . " TEXT, "
			. Character::realname . " TEXT, "
			. Character::desc . " TEXT, "
			. Character::popularity . " INTEGER, "
			. Character::gender . " TEXT, "
			. Character::xurl . " TEXT, "
			. Character::xsource . " TEXT, "
			. Character::xid . " TEXT, "
			. Character::xupdated . " INTEGER, "
			. "FOREIGN KEY (". Character::publisher_id .") REFERENCES " . Publisher::TABLE . "(" . Publisher::id . ")"
		. ")";
		$this->sqlite_execute( "character", $sql, "Create table character" );

		$sql = 'CREATE  INDEX IF NOT EXISTS character_name on character (name)';
		$this->sqlite_execute( "character", $sql, "Index on character (name)" );
		$sql = 'CREATE  INDEX IF NOT EXISTS character_realname on character (realname)';
		$this->sqlite_execute( "character", $sql, "Index on character (realname)" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS character_xidxsource on character (xid,xsource)';
		$this->sqlite_execute( "character", $sql, "Index on character (xid,xsource)" );

		/** PUBLICATION */
		$sql = "CREATE TABLE IF NOT EXISTS publication ( "
			. Publication::id . " INTEGER PRIMARY KEY, "
			. Publication::series_id . " INTEGER, "
			. Publication::created . " INTEGER, "
			. Publication::name . " TEXT, "
			. Publication::desc . " TEXT, "
			. Publication::pub_date . " INTEGER, "
			. Publication::issue_num . " TEXT, "
			. Publication::issue_order . " INTEGER, "
			. Publication::media_count . " INTEGER, "
			. Publication::xurl . " TEXT, "
			. Publication::xsource . " TEXT, "
			. Publication::xid . " TEXT, "
			. Publication::xupdated . " INTEGER, "
			. "FOREIGN KEY (". Publication::series_id .") REFERENCES " . Series::TABLE . "(" . Series::id . ")"
		. ")";
		$this->sqlite_execute( "publication", $sql, "Create table publication" );

		$sql = 'CREATE  INDEX IF NOT EXISTS publication_name on publication (name)';
		$this->sqlite_execute( "publication", $sql, "Index on publication (name)" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS publication_xidxsource on publication (xid,xsource)';
		$this->sqlite_execute( "publication", $sql, "Index on publication (xid,xsource)" );

		/** MEDIA */
		$sql = "CREATE TABLE IF NOT EXISTS media ( "
			. Media::id . " INTEGER PRIMARY KEY, "
			. Media::publication_id . " INTEGER, "
			. Media::type_code . " INTEGER, "
			. Media::filename . " TEXT, "
			. Media::original_filename . " TEXT, "
			. Media::checksum . " TEXT, "
			. Media::created . " INTEGER, "
			. Media::size . " INTEGER, "
			. "FOREIGN KEY (". Media::type_code .") REFERENCES " . Media_Type::TABLE . "(" . Media_Type::code . "),"
			. "FOREIGN KEY (". Media::publication_id .") REFERENCES " . Publication::TABLE . "(" . Publication::id . ")"
		. ")";
		$this->sqlite_execute( "media", $sql, "Create table media" );

		$sql = 'CREATE  INDEX IF NOT EXISTS media_filename on media (filename)';
		$this->sqlite_execute( "media", $sql, "Index on media (filename)" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS media_checksum on media (checksum)';
		$this->sqlite_execute( "media", $sql, "Index on media (checksum)" );

		/* -=-=-=-=-=-=-= JOINS =-=-=-=-=-=-=- */
		/** CHARACTER_ALIAS */
		$sql = "CREATE TABLE IF NOT EXISTS character_alias ( "
			. Character_Alias::id . " INTEGER PRIMARY KEY, "
			. Character_Alias::name . " TEXT, "
			. Character_Alias::character_id . " INTEGER, "
			. "FOREIGN KEY (". Character_Alias::character_id .") REFERENCES " . Character::TABLE . "(" . Character::id . ")"
		. ")";
		$this->sqlite_execute( "character_alias", $sql, "Create table character_alias" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS character_alias_character_idname on character_alias (character_id,name)';
		$this->sqlite_execute( "character_alias", $sql, "Index on character_alias (character_id,name)" );

		/** SERIES_ALIAS */
		$sql = "CREATE TABLE IF NOT EXISTS series_alias ( "
			. Series_Alias::id . " INTEGER PRIMARY KEY, "
			. Series_Alias::name . " TEXT, "
			. Series_Alias::series_id . " INTEGER, "
			. "FOREIGN KEY (". Series_Alias::series_id .") REFERENCES " . Series::TABLE . "(" . Series::id . ")"
		. ")";
		$this->sqlite_execute( "series_alias", $sql, "Create table series_alias" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS series_alias_series_idname on series_alias (series_id,name)';
		$this->sqlite_execute( "series_alias", $sql, "Index on series_alias (series_id,name)" );

		/** PUBLICATION_CHARACTER */
		$sql = "CREATE TABLE IF NOT EXISTS publication_character ( "
			. Publication_Character::id . " INTEGER PRIMARY KEY, "
			. Publication_Character::publication_id . " INTEGER, "
			. Publication_Character::character_id . " INTEGER, "
			. "FOREIGN KEY (". Publication_Character::publication_id .") REFERENCES " . Publication::TABLE . "(" . Publication::id . "),"
			. "FOREIGN KEY (". Publication_Character::character_id .") REFERENCES " . Character::TABLE . "(" . Character::id . ")"
		. ")";
		$this->sqlite_execute( "publication_character", $sql, "Create table publication_character" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS publication_character_publication_idcharacter_id on publication_character (publication_id,character_id)';
		$this->sqlite_execute( "publication_character", $sql, "Index on publication_character (publication_id,character_id)" );

		/** SERIES_CHARACTER */
		$sql = "CREATE TABLE IF NOT EXISTS series_character ( "
			. Series_Character::id . " INTEGER PRIMARY KEY, "
			. Series_Character::series_id . " INTEGER, "
			. Series_Character::character_id . " INTEGER, "
			. "FOREIGN KEY (". Series_Character::series_id .") REFERENCES " . Series::TABLE . "(" . Series::id . "),"
			. "FOREIGN KEY (". Series_Character::character_id .") REFERENCES " . Character::TABLE . "(" . Character::id . ")"
		. ")";
		$this->sqlite_execute( "series_character", $sql, "Create table series_character" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS series_character_series_idcharacter_id on series_character (series_id,character_id)';
		$this->sqlite_execute( "series_character", $sql, "Index on series_character (series_id,character_id)" );

		/** STORY_ARC_CHARACTER */
		$sql = "CREATE TABLE IF NOT EXISTS story_arc_character ( "
			. Story_Arc_Character::id . " INTEGER PRIMARY KEY, "
			. Story_Arc_Character::story_arc_id . " INTEGER, "
			. Story_Arc_Character::character_id . " INTEGER, "
			. "FOREIGN KEY (". Story_Arc_Character::story_arc_id .") REFERENCES " . Story_Arc::TABLE . "(" . Story_Arc::id . "),"
			. "FOREIGN KEY (". Story_Arc_Character::character_id .") REFERENCES " . Character::TABLE . "(" . Character::id . ")"
		. ")";
		$this->sqlite_execute( "story_arc_character", $sql, "Create table story_arc_character" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS story_arc_character_story_arc_idcharacter_id on story_arc_character (story_arc_id,character_id)';
		$this->sqlite_execute( "story_arc_character", $sql, "Index on story_arc_character (story_arc_id,character_id)" );

		/** STORY_ARC_PUBLICATION */
		$sql = "CREATE TABLE IF NOT EXISTS story_arc_publication ( "
			. Story_Arc_Publication::id . " INTEGER PRIMARY KEY, "
			. Story_Arc_Publication::story_arc_id . " INTEGER, "
			. Story_Arc_Publication::publication_id . " INTEGER, "
			. "FOREIGN KEY (". Story_Arc_Publication::story_arc_id .") REFERENCES " . Story_Arc::TABLE . "(" . Story_Arc::id . "),"
			. "FOREIGN KEY (". Story_Arc_Publication::publication_id .") REFERENCES " . Publication::TABLE . "(" . Publication::id . ")"
		. ")";
		$this->sqlite_execute( "story_arc_publication", $sql, "Create table story_arc_publication" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS story_arc_publication_story_arc_idpublication_id on story_arc_publication (story_arc_id,publication_id)';
		$this->sqlite_execute( "story_arc_publication", $sql, "Index on story_arc_publication (story_arc_id,publication_id)" );

		/** STORY_ARC_SERIES */
		$sql = "CREATE TABLE IF NOT EXISTS story_arc_series ( "
			. Story_Arc_Series::id . " INTEGER PRIMARY KEY, "
			. Story_Arc_Series::story_arc_id . " INTEGER, "
			. Story_Arc_Series::series_id . " INTEGER, "
			. "FOREIGN KEY (". Story_Arc_Series::story_arc_id .") REFERENCES " . Story_Arc::TABLE . "(" . Story_Arc::id . "),"
			. "FOREIGN KEY (". Story_Arc_Series::series_id .") REFERENCES " . Series::TABLE . "(" . Series::id . ")"
		. ")";
		$this->sqlite_execute( "story_arc_series", $sql, "Create table story_arc_series" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS story_arc_series_story_arc_idseries_id on story_arc_series (story_arc_id,series_id)';
		$this->sqlite_execute( "story_arc_series", $sql, "Index on story_arc_series (story_arc_id,series_id)" );
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
				$sql = "INSERT INTO " . $media_type_model->tableName() . " ( code, name ) values ( :C, :N )";
				Database::instance()->execute_sql($sql, array( ":C" => $code, ":N" => $name ) );
			}
		}
	}
}
