<?php

namespace migration;

use \Migrator as Migrator;
use \MigrationFailedException as MigrationFailedException;
use \Config as Config;
use \Logger as Logger;
use \SQL as SQL;

use \model\user\Users as Users;


class Migration_1 extends Migrator
{
	public function targetVersion() { return "0.1.1"; }

	public function sqlite_preUpgrade()
	{
		$this->sqlite_backupDatabase();
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
		$this->sqlite_execute( Users::TABLE, $sql, "Create table " . Users::TABLE );

		$indexStatements = array(
			'CREATE UNIQUE INDEX IF NOT EXISTS ' . Users::TABLE . '_tokenindex on ' . Users::TABLE . '(' . Users::rememberme_token . ')',
			'CREATE UNIQUE INDEX IF NOT EXISTS ' . Users::TABLE . '_activationindex on ' . Users::TABLE . '(' . Users::activation_hash . ')',
			'CREATE UNIQUE INDEX IF NOT EXISTS ' . Users::TABLE . '_apiindex on ' . Users::TABLE . '(' . Users::api_hash . ')',
			'CREATE UNIQUE INDEX IF NOT EXISTS ' . Users::TABLE . '_emailindex on ' . Users::TABLE . '(' . Users::email . ')',
			'CREATE UNIQUE INDEX IF NOT EXISTS ' . Users::TABLE . '_nameindex on ' . Users::TABLE . '(' . Users::name . ')'
		);
		foreach( $indexStatements as $stmt ) {
			$this->sqlite_execute( Users::TABLE, $stmt, "Index on " . Users::TABLE );
		}
	}

	public function sqlite_postUpgrade()
	{
		$values = array(
			"name" => "vito",
			"password_hash" => "$2y$10$486J2llu2CS.2DnXlTIQgOsk3tzcIVki428mjELHoEr/evhymDLGO",
			"email" => "vitolibrarius@gmail.com",
			"active" => 1,
			"account_type" => Users::AdministratorRole,
			"creation_timestamp" => time(),
			"failed_logins" => 0,
			"api_hash" => "4bd3b2b9075571c95ade00002334e7b2"
		);

		$users_model = new Users();
		$insert = SQL::Insert( $users_model );
		$insert->addRecord( $values );
		$success = $insert->commitTransaction();
	}
}
