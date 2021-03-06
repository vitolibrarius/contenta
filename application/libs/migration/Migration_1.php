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
	public function targetVersion() { return "0.1.0"; }

	public function sqlite_preUpgrade()
	{
		$this->sqlite_backupDatabase();
	}

	public function sqlite_upgrade()
	{
		$sql = "CREATE TABLE IF NOT EXISTS users ( "
			. Users::id . " INTEGER PRIMARY KEY, "
			. Users::name . " TEXT, "
			. Users::email . " TEXT, "
			. Users::active . " INTEGER, "
			. Users::account_type . " TEXT, "
			. Users::rememberme_token . " TEXT, "
			. Users::api_hash . " TEXT, "
			. Users::password_hash . " TEXT, "
			. Users::password_reset_hash . " TEXT, "
			. Users::activation_hash . " TEXT, "
			. Users::failed_logins . " INTEGER, "
			. Users::created . " INTEGER, "
			. Users::last_login_timestamp . " INTEGER, "
			. Users::last_failed_login . " INTEGER, "
			. Users::password_reset_timestamp . " INTEGER "
		. ")";
		$this->sqlite_execute( "users", $sql, "Create table users" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS users_rememberme_token on users (rememberme_token)';
		$this->sqlite_execute( "users", $sql, "Index on users (rememberme_token)" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS users_namepassword_hash on users (name,password_hash)';
		$this->sqlite_execute( "users", $sql, "Index on users (name,password_hash)" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS users_activation_hash on users (activation_hash)';
		$this->sqlite_execute( "users", $sql, "Index on users (activation_hash)" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS users_api_hash on users (api_hash)';
		$this->sqlite_execute( "users", $sql, "Index on users (api_hash)" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS users_email on users (email)';
		$this->sqlite_execute( "users", $sql, "Index on users (email)" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS users_name on users (name)';
		$this->sqlite_execute( "users", $sql, "Index on users (name)" );
	}

	public function sqlite_postUpgrade()
	{
		$values = array(
			"name" => "vito",
			"password_hash" => "$2y$10$486J2llu2CS.2DnXlTIQgOsk3tzcIVki428mjELHoEr/evhymDLGO",
			"email" => "vitolibrarius@gmail.com",
			"active" => 1,
			"account_type" => Users::AdministratorRole,
			"created" => time(),
			"failed_logins" => 0,
			"api_hash" => "4bd3b2b9075571c95ade00002334e7b2"
		);

		$users_model = new Users();
		$insert = SQL::Insert( $users_model );
		$insert->addRecord( $values );
		$success = $insert->commitTransaction();

		$user = $users_model->objectForName('vito');
		($user != false && $user->name == 'vito') || die("Could not find 'vito' user");
		Logger::logInfo( "Created user ". $user, get_class(), 'Users');
	}
}
