<?php

namespace migration;

use \Migrator as Migrator;
use \MigrationFailedException as MigrationFailedException;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \SQL as SQL;

use \model\network\Endpoint_Type as Endpoint_Type;
use \model\network\Endpoint as Endpoint;

use \model\user\Users as Users;
use \model\network\Network as Network;
use \model\network\User_Network as User_Network;

class Migration_3 extends Migrator
{
	public function targetVersion() { return "0.2.1"; }

	public function sqlite_preUpgrade()
	{
		$this->sqlite_backupDatabase();
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

		/** NETWORK */
		$sql = "CREATE TABLE IF NOT EXISTS network ( "
			. Network::id . " INTEGER PRIMARY KEY, "
			. Network::ip_address . " TEXT, "
			. Network::ip_hash . " TEXT, "
			. Network::created . " INTEGER, "
			. Network::disable . " INTEGER "
		. ")";
		$this->sqlite_execute( "network", $sql, "Create table network" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS network_ip_address on network (ip_address)';
		$this->sqlite_execute( "network", $sql, "Index on network (ip_address)" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS network_ip_hash on network (ip_hash)';
		$this->sqlite_execute( "network", $sql, "Index on network (ip_hash)" );


		/** USER_NETWORK */
		$sql = "CREATE TABLE IF NOT EXISTS user_network ( "
			. User_Network::id . " INTEGER PRIMARY KEY, "
			. User_Network::user_id . " INTEGER, "
			. User_Network::network_id . " INTEGER, "
			. "FOREIGN KEY (". User_Network::user_id .") REFERENCES " . Users::TABLE . "(" . Users::id . "),"
			. "FOREIGN KEY (". User_Network::network_id .") REFERENCES " . Network::TABLE . "(" . Network::id . ")"
		. ")";
		$this->sqlite_execute( "user_network", $sql, "Create table user_network" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS user_network_user_idnetwork_id on user_network (user_id,network_id)';
		$this->sqlite_execute( "user_network", $sql, "Index on user_network (user_id,network_id)" );
	}

	public function sqlite_postUpgrade()
	{
		return true;
	}
}
