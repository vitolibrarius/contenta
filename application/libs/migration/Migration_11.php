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

class Migration_11 extends Migrator
{
	public function targetVersion() { return "0.8.0"; }

	public function sqlite_preUpgrade()
	{
		$this->sqlite_backupDatabase();
	}

	public function sqlite_upgrade()
	{
		/** ARTIST_ROLE */
		$sql = "CREATE TABLE IF NOT EXISTS artist_role ("
			. "code TEXT PRIMARY KEY,"
			. "name TEXT,"
			. "enabled INTEGER"
		. ");";
		$this->sqlite_execute( "artist_role", $sql, "Create table artist_role" );

		$sql = 'CREATE  INDEX IF NOT EXISTS artist_role_name_01 on artist_role (name COLLATE NOCASE);';
		$this->sqlite_execute( "artist_role", $sql, "Index on artist_role (name)" );

		/** ARTIST */
		$sql = "CREATE TABLE IF NOT EXISTS artist ( "
			. "id INTEGER PRIMARY KEY,"
			. "created INTEGER,"
			. "name TEXT,"
			. "desc TEXT,"
			. "gender TEXT,"
			. "birth_date INTEGER,"
			. "death_date INTEGER,"
			. "pub_wanted INTEGER,"
			. "xurl TEXT,"
			. "xsource TEXT,"
			. "xid TEXT,"
			. "xupdated INTEGER"
		. ")";
		$this->sqlite_execute( "artist", $sql, "Create table artist" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS artist_xidxsource_02 on artist (xid COLLATE NOCASE,xsource COLLATE NOCASE)';
		$this->sqlite_execute( "artist", $sql, "Index on artist (xid,xsource)" );

		$sql = 'CREATE  INDEX IF NOT EXISTS artist_name_01 on artist (name COLLATE NOCASE)';
		$this->sqlite_execute( "artist", $sql, "Index on artist (name)" );

		/** ARTIST_ALIAS */
		$sql = "CREATE TABLE IF NOT EXISTS artist_alias ("
			. "id INTEGER PRIMARY KEY,"
			. "name TEXT,"
			. "artist_id INTEGER,"
			. "FOREIGN KEY ( artist_id ) REFERENCES artist ( id )"
		. ")";
		$this->sqlite_execute( "artist_alias", $sql, "Create table artist_alias" );

		$sql = 'CREATE INDEX IF NOT EXISTS artist_aliasArtist_a_01_fk on artist_alias (artist_id);';
		$this->sqlite_execute( "artist_alias", $sql, "Index on artist_alias (artist_id)" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS artist_alias_artist__02 on artist_alias (artist_id,name COLLATE NOCASE);';
		$this->sqlite_execute( "artist_alias", $sql, "Index on artist_alias (artist_id,name)" );

		/** PUBLICATION_ARTIST */
		$sql = "CREATE TABLE IF NOT EXISTS publication_artist ("
			. "id INTEGER PRIMARY KEY,"
			. "publication_id INTEGER,"
			. "artist_id INTEGER,"
			. "role_code TEXT,"
			. "FOREIGN KEY ( publication_id ) REFERENCES publication ( id ),"
			. "FOREIGN KEY ( artist_id ) REFERENCES artist ( id ),"
			. "FOREIGN KEY ( role_code ) REFERENCES artist_role ( code )"
		. ")";
		$this->sqlite_execute( "publication_artist", $sql, "Create table publication_artist" );

		$sql = 'CREATE INDEX IF NOT EXISTS publication_artistPu_01_fk on publication_artist (publication_id);';
		$this->sqlite_execute( "publication_artist", $sql, "Index on publication_artist (publication_id)" );

		$sql = 'CREATE INDEX IF NOT EXISTS publication_artistAr_02_fk on publication_artist (artist_id);';
		$this->sqlite_execute( "publication_artist", $sql, "Index on publication_artist (artist_id)" );

		$sql = 'CREATE INDEX IF NOT EXISTS publication_artistAr_03_fk on publication_artist (role_code);';
		$this->sqlite_execute( "publication_artist", $sql, "Index on publication_artist (role_code)" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS publication_artist_p_04 on publication_artist (publication_id,artist_id,role_code);';
		$this->sqlite_execute( "publication_artist", $sql, "Index on publication_artist (publication_id,artist_id,role_code)" );

		/** SERIES_ARTIST */
		$sql = "CREATE TABLE IF NOT EXISTS series_artist ("
			. "id INTEGER PRIMARY KEY,"
			. "series_id INTEGER,"
			. "artist_id INTEGER,"
			. "role_code TEXT,"
			. "FOREIGN KEY ( series_id ) REFERENCES series ( id ),"
			. "FOREIGN KEY ( artist_id ) REFERENCES artist ( id ),"
			. "FOREIGN KEY ( role_code ) REFERENCES artist_role ( code )"
		. ")";
		$this->sqlite_execute( "artist", $sql, "Create table artist" );

		$sql = 'CREATE INDEX IF NOT EXISTS series_artistSeries__01_fk on series_artist (series_id);';
		$this->sqlite_execute( "series_artist", $sql, "Index on series_artist (series_id)" );

		$sql = 'CREATE INDEX IF NOT EXISTS series_artistartist__02_fk on series_artist (artist_id);';
		$this->sqlite_execute( "series_artist", $sql, "Index on series_artist (artist_id)" );

		$sql = 'CREATE INDEX IF NOT EXISTS series_artistArtist__03_fk on series_artist (role_code);';
		$this->sqlite_execute( "series_artist", $sql, "Index on series_artist (role_code)" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS series_artist_series_04 on series_artist (series_id,artist_id,role_code);';
		$this->sqlite_execute( "series_artist", $sql, "Index on series_artist (series_id,artist_id,role_code)" );
	}

	public function sqlite_postUpgrade()
	{
		$role_model = Model::Named("Artist_Role");
		$roles = array(
			array("code"=>"unknown",	"name"=>"Unknown",	"enabled"=>1),
			array("code"=>"colorist",	"name"=>"colorist",	"enabled"=>0),
			array("code"=>"cover",		"name"=>"cover",	"enabled"=>0),
			array("code"=>"editor",		"name"=>"editor",	"enabled"=>0),
			array("code"=>"inker",		"name"=>"inker",	"enabled"=>0),
			array("code"=>"letterer",	"name"=>"letterer",	"enabled"=>0),
			array("code"=>"other",		"name"=>"other",	"enabled"=>0),
			array("code"=>"penciler",	"name"=>"penciler",	"enabled"=>0),
			array("code"=>"production",	"name"=>"production","enabled"=>0),
			array("code"=>"artist",		"name"=>"artist",	"enabled"=>1),
			array("code"=>"writer",		"name"=>"writer",	"enabled"=>1)
		);
		foreach ($roles as $roleDict) {
			if ($role_model->objectForCode($roleDict['code']) == false)
			{
				$sql = "INSERT INTO " . $role_model->tableName() . " ( code, name, enabled ) values ( :C, :N, :E )";
				Database::instance()->execute_sql($sql, array(
					":C" => $roleDict['code'], ":N" => $roleDict['name'], ":E" => $roleDict['enabled'] )
				);
			}
		}
	}
}
