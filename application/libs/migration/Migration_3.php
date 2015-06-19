<?php

namespace migration;

use \Migrator as Migrator;
use \MigrationFailedException as MigrationFailedException;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \SQL as SQL;

use model\Users as Users;
use model\Endpoint_Type as Endpoint_Type;
use model\Endpoint as Endpoint;
use model\Network as Network;
use model\User_Network as User_Network;

class Migration_3 extends Migrator
{
	public function sqlite_preUpgrade()
	{
		// backup sqlite database file
		$db_path = Config::GetPath("Database/path", null);
		if ( strlen($db_path) == 0 ) {
			throw new \Exception('No path set in configuration for sqlite database');
		}
		$db_file = appendPath($db_path, "contenta.sqlite" );
		$backupDatabase = appendPath($this->scratch, "contenta.Migration_3." . date('Y-m-d.H-i-s') . ".backup");
		file_exists($db_file) == false || copy($db_file, $backupDatabase) || die('Failed to backup ' . $db_file);
	}

	public function sqlite_upgrade()
	{
		$model = Model::Named("Endpoint");
		$sql = 'CREATE TABLE IF NOT EXISTS ' . Endpoint_Type::TABLE . " ( "
				. Endpoint_Type::id . " INTEGER PRIMARY KEY, "
				. Endpoint_Type::name . " TEXT COLLATE NOCASE, "
				. Endpoint_Type::code . " TEXT COLLATE NOCASE, "
				. Endpoint_Type::data_type . " TEXT, "
				. Endpoint_Type::site_url . " TEXT, "
				. Endpoint_Type::api_url . " TEXT, "
				. Endpoint_Type::favicon_url . " TEXT, "
				. Endpoint_Type::comments . " TEXT, "
				. Endpoint_Type::throttle_hits . " INTEGER, "
				. Endpoint_Type::throttle_time . " INTEGER "
				. ")";
		$this->sqlite_execute( Endpoint_Type::TABLE, $sql, "Create table " . Endpoint_Type::TABLE );

		$table_fields = \SQL::pragma_TableInfo(Endpoint_Type::TABLE);
		if ( isset($table_fields[ Endpoint_Type::data_type ]) == false ) {
			$this->sqlite_execute(
				Endpoint_Type::TABLE,
				"ALTER TABLE " . Endpoint_Type::TABLE . " ADD COLUMN " . Endpoint_Type::data_type . " TEXT",
				Endpoint_Type::TABLE . " - " . Endpoint_Type::data_type . " column added"
			);
		}

		$sql = 'CREATE TABLE IF NOT EXISTS ' . Endpoint::TABLE . " ( "
				. Endpoint::id . " INTEGER PRIMARY KEY, "
				. Endpoint::type_id . " INTEGER, "
				. Endpoint::name . " TEXT COLLATE NOCASE, "
				. Endpoint::base_url . " TEXT, "
				. Endpoint::api_key . " TEXT, "
				. Endpoint::username . " TEXT, "
				. Endpoint::enabled . " INTEGER, "
				. Endpoint::compressed . " INTEGER, "
				. "FOREIGN KEY (". Endpoint::type_id .") REFERENCES " . Endpoint_Type::TABLE . "(" . Endpoint_Type::id . ")"
				. ")";
		$this->sqlite_execute( Endpoint::TABLE, $sql, "Create table " . Endpoint::TABLE );

		$table_fields = \SQL::pragma_TableInfo(Endpoint::TABLE);
		if ( isset($table_fields[ Endpoint::compressed ]) == false ) {
			$this->sqlite_execute(
				Endpoint::TABLE,
				"ALTER TABLE " . Endpoint::TABLE . " ADD COLUMN " . Endpoint::compressed . " INTEGER",
				Endpoint::TABLE . " - " . Endpoint::compressed . " column added"
			);
		}

		$sql = "CREATE TABLE IF NOT EXISTS " . Network::TABLE
				. " ( "
				. Network::id . " INTEGER PRIMARY KEY,  "
				. Network::ipAddress . " TEXT, "
				. Network::ipHash . " TEXT, "
				. Network::created . " INTEGER, "
				. Network::disable . " INTEGER "
				. ")";
		$this->sqlite_execute( Network::TABLE, $sql, "Create table " . Network::TABLE );

		$this->sqlite_execute(
			Network::TABLE,
			'CREATE UNIQUE INDEX IF NOT EXISTS ' . Network::TABLE . '_ipaddress on ' . Network::TABLE . '(' . Network::ipAddress . ')',
			"Unique index on " . Network::TABLE
		);

		$sql = 'CREATE TABLE IF NOT EXISTS ' . User_Network::TABLE . " ( "
							. User_Network::id . " INTEGER PRIMARY KEY, "
							. User_Network::user_id . " INTEGER, "
							. User_Network::network_id . " INTEGER, "
							. "FOREIGN KEY (". User_Network::user_id .") REFERENCES " . Users::TABLE . "(id), "
							. "FOREIGN KEY (". User_Network::network_id .") REFERENCES " . Network::TABLE . "(id) "
							. ")";
		$this->sqlite_execute( User_Network::TABLE, $sql, "Create table " . User_Network::TABLE );
	}

	public function sqlite_postUpgrade()
	{
		return true;
	}
}
