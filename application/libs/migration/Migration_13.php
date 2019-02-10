<?php

namespace migration;

use \Migrator as Migrator;
use \MigrationFailedException as MigrationFailedException;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \Database as Database;

use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \model\user\Users as Users;

use \model\network\Endpoint as Endpoint;

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

class Migration_13 extends Migrator
{
	public function targetVersion() { return "0.8.2"; }

	public function sqlite_preUpgrade()
	{
		$this->sqlite_backupDatabase();
	}

	public function reRunMigration() { return true; }

	public function sqlite_upgrade()
	{
		/** BOOK */
		$sql = "CREATE TABLE IF NOT EXISTS book ("
			. "id INTEGER PRIMARY KEY,"
			. "type_code TEXT,"
			. "filename TEXT,"
			. "original_filename TEXT,"
			. "checksum TEXT,"
			. "created INTEGER,"
			. "size INTEGER,"
			. "name TEXT,"
			. "author TEXT,"
			. "desc TEXT,"
			. "pub_date INTEGER,"
			. "pub_order INTEGER,"
			. "FOREIGN KEY ( type_code ) REFERENCES media_type ( code )"
		. ");";
		$this->sqlite_execute( "book", $sql, "Create table book" );

		$sql = 'CREATE INDEX IF NOT EXISTS bookMedia_Type_type__01_fk on book (type_code);';
		$this->sqlite_execute( "artist_role", $sql, "Index on artist_role (name)" );

		$sql = 'CREATE  INDEX IF NOT EXISTS book_name_02 on book (name COLLATE NOCASE);';
		$this->sqlite_execute( "artist_role", $sql, "Index on artist_role (name)" );

		$sql = 'CREATE  INDEX IF NOT EXISTS book_author_03 on book (author COLLATE NOCASE);';
		$this->sqlite_execute( "artist_role", $sql, "Index on artist_role (name)" );

		$sql = 'CREATE  INDEX IF NOT EXISTS book_filename_04 on book (filename);';
		$this->sqlite_execute( "artist_role", $sql, "Index on artist_role (name)" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS book_checksum_05 on book (checksum COLLATE NOCASE);';
		$this->sqlite_execute( "artist_role", $sql, "Index on artist_role (name)" );
	}

	public function sqlite_postUpgrade()
	{
	}
}
