<?php

namespace migration;

use \Migrator as Migrator;
use \MigrationFailedException as MigrationFailedException;
use \Config as Config;
use \Logger as Logger;
use \Database as Database;

use model\Users as Users;


class Migration_1 extends Migrator
{
	public function sqlite_preUpgrade()
	{
		// backup sqlite database file
		$db_path = Config::GetPath("Database/path", null);
		if ( strlen($db_path) == 0 ) {
			throw new Exception('No path set in configuration for sqlite database');
		}
		$db_file = appendPath($db_path, "contenta.sqlite" );
		$backupDatabase = appendPath($this->scratch, "contenta.Migration_1." . date('Y-m-d.H-i-s') . ".backup");
		file_exists($db_file) == false || copy($db_file, $backupDatabase) || die('Failed to backup ' . $db_file);
	}

	public function sqlite_upgrade()
	{
		$sql = "CREATE TABLE IF NOT EXISTS " . Users::TABLE
			. " ( "
			. Users::id . " INTEGER PRIMARY KEY, "
			. Users::name . " TEXT, "
			. Users::password_hash . " TEXT, "
			. Users::email . " TEXT, "
			. Users::active . " INTEGER, "
			. Users::account_type . " TEXT, "
			. Users::rememberme_token . " TEXT, "
			. Users::creation_timestamp . " TEXT, "
			. Users::last_login_timestamp . " TEXT, "
			. Users::failed_logins . " INTEGER, "
			. Users::last_failed_login . " TEXT, "
			. Users::activation_hash . " TEXT, "
			. Users::api_hash . " TEXT, "
			. Users::password_reset_hash . " TEXT, "
			. Users::password_reset_timestamp . " TEXT "
			. ")";

		$statement = $this->db->prepare($sql);
		if ($statement == false || $statement->execute() == false) {
			$errPoint = ($statement ? $statement : $this->db);
			$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
			Logger::logSQLError('Users', 'createTable', $errPoint->errorCode(), $pdoError, $sql, null);
			throw new MigrationFailedException("Error creating Users table");
		}

		$this->db->exec('CREATE UNIQUE INDEX IF NOT EXISTS ' . Users::TABLE . '_tokenindex on '
			. Users::TABLE . '(' . Users::rememberme_token . ')');
		$this->db->exec('CREATE UNIQUE INDEX IF NOT EXISTS ' . Users::TABLE . '_activationindex on '
			. Users::TABLE . '(' . Users::activation_hash . ')');
		$this->db->exec('CREATE UNIQUE INDEX IF NOT EXISTS ' . Users::TABLE . '_apiindex on '
			. Users::TABLE . '(' . Users::api_hash . ')');
		$this->db->exec('CREATE UNIQUE INDEX IF NOT EXISTS ' . Users::TABLE . '_emailindex on '
			. Users::TABLE . '(' . Users::email . ')');
		$this->db->exec('CREATE UNIQUE INDEX IF NOT EXISTS ' . Users::TABLE . '_nameindex on '
			. Users::TABLE . '(' . Users::name . ')');

		Logger::logInfo( "Created table " . Users::TABLE, "Migration", Users::TABLE);
	}

	public function sqlite_postUpgrade()
	{
		$users_model = new Users(Database::instance());
		$vito = $users_model->createUserIfMissing('vito', 'omega1Zulu!', 'vitolibrarius@gmail.com', Users::AdministratorRole);
	}
}
