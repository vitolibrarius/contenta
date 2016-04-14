<?php

namespace model\user;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \model\user\UsersDBO as UsersDBO;

/* import related objects */
use \model\network\User_Network as User_Network;
use \model\network\User_NetworkDBO as User_NetworkDBO;
use \model\User_Series as User_Series;
use \model\User_SeriesDBO as User_SeriesDBO;

/** Sample Creation script */
		/** USERS */
/*
		$sql = "CREATE TABLE IF NOT EXISTS users ( "
			. Users::id . " INTEGER PRIMARY KEY, "
			. Users::name . " TEXT, "
			. Users::email . " TEXT, "
			. Users::active . " INTEGER, "
			. Users::account_type . " TEXT, "
			. Users::rememberme_token . " TEXT, "
			. Users::api_hash . " TEXT, "
			. Users::password_reset_hash . " TEXT, "
			. Users::activation_hash . " TEXT, "
			. Users::failed_logins . " INTEGER, "
			. Users::creation_timestamp . " INTEGER, "
			. Users::last_login_timestamp . " INTEGER, "
			. Users::last_failed_login . " INTEGER, "
			. Users::password_reset_timestamp . " INTEGER "
		. ")";
		$this->sqlite_execute( "users", $sql, "Create table users" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS users_rememberme_token on users (rememberme_token)';
		$this->sqlite_execute( "users", $sql, "Index on users (rememberme_token)" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS users_activation_hash on users (activation_hash)';
		$this->sqlite_execute( "users", $sql, "Index on users (activation_hash)" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS users_api_hash on users (api_hash)';
		$this->sqlite_execute( "users", $sql, "Index on users (api_hash)" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS users_email on users (email)';
		$this->sqlite_execute( "users", $sql, "Index on users (email)" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS users_name on users (name)';
		$this->sqlite_execute( "users", $sql, "Index on users (name)" );
*/
abstract class _Users extends Model
{
	const TABLE = 'users';
	const id = 'id';
	const name = 'name';
	const email = 'email';
	const active = 'active';
	const account_type = 'account_type';
	const rememberme_token = 'rememberme_token';
	const api_hash = 'api_hash';
	const password_reset_hash = 'password_reset_hash';
	const activation_hash = 'activation_hash';
	const failed_logins = 'failed_logins';
	const creation_timestamp = 'creation_timestamp';
	const last_login_timestamp = 'last_login_timestamp';
	const last_failed_login = 'last_failed_login';
	const password_reset_timestamp = 'password_reset_timestamp';

	public function tableName() { return Users::TABLE; }
	public function tablePK() { return Users::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Users::name)
		);
	}

	public function allColumnNames()
	{
		return array(
			Users::id,
			Users::name,
			Users::email,
			Users::active,
			Users::account_type,
			Users::rememberme_token,
			Users::api_hash,
			Users::password_reset_hash,
			Users::activation_hash,
			Users::failed_logins,
			Users::creation_timestamp,
			Users::last_login_timestamp,
			Users::last_failed_login,
			Users::password_reset_timestamp
		);
	}

	/** * * * * * * * * *
		Basic search functions
	 */
	public function objectForName($value)
	{
		return $this->singleObjectForKeyValue(Users::name, $value);
	}

	public function objectForEmail($value)
	{
		return $this->singleObjectForKeyValue(Users::email, $value);
	}

	public function allForAccount_type($value)
	{
		return $this->allObjectsForKeyValue(Users::account_type, $value);
	}

	public function objectForRememberme_token($value)
	{
		return $this->singleObjectForKeyValue(Users::rememberme_token, $value);
	}

	public function objectForApi_hash($value)
	{
		return $this->singleObjectForKeyValue(Users::api_hash, $value);
	}

	public function allForPassword_reset_hash($value)
	{
		return $this->allObjectsForKeyValue(Users::password_reset_hash, $value);
	}

	public function objectForActivation_hash($value)
	{
		return $this->singleObjectForKeyValue(Users::activation_hash, $value);
	}



	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				default:
					break;
			}
		}
		return parent::joinAttributes( $joinModel );
	}

	public function create( $name, $email, $active, $account_type, $rememberme_token, $api_hash, $password_reset_hash, $activation_hash, $failed_logins, $creation_timestamp, $last_login_timestamp, $last_failed_login, $password_reset_timestamp)
	{
		$obj = false;
		if ( isset($name, $email) ) {
			$params = array(
				Users::name => (isset($name) ? $name : null),
				Users::email => (isset($email) ? $email : null),
				Users::active => (isset($active) ? $active : Model::TERTIARY_TRUE),
				Users::account_type => (isset($account_type) ? $account_type : 'user'),
				Users::rememberme_token => (isset($rememberme_token) ? $rememberme_token : null),
				Users::api_hash => (isset($api_hash) ? $api_hash : null),
				Users::password_reset_hash => (isset($password_reset_hash) ? $password_reset_hash : null),
				Users::activation_hash => (isset($activation_hash) ? $activation_hash : null),
				Users::failed_logins => (isset($failed_logins) ? $failed_logins : 0),
				Users::creation_timestamp => (isset($creation_timestamp) ? $creation_timestamp : time()),
				Users::last_login_timestamp => (isset($last_login_timestamp) ? $last_login_timestamp : null),
				Users::last_failed_login => (isset($last_failed_login) ? $last_failed_login : null),
				Users::password_reset_timestamp => (isset($password_reset_timestamp) ? $password_reset_timestamp : null),
			);


			list( $obj, $errorList ) = $this->createObject($params);
			if ( is_array($errorList) ) {
				return $errorList;
			}
		}
		return $obj;
	}

	public function deleteObject( DataObject $object = null)
	{
		if ( $object instanceof Users )
		{
			$user_network_model = Model::Named('User_Network');
			if ( $user_network_model->deleteAllForKeyValue(User_Network::user_id, $this->id) == false ) {
				return false;
			}
			$user_series_model = Model::Named('User_Series');
			if ( $user_series_model->deleteAllForKeyValue(User_Series::user_id, $this->id) == false ) {
				return false;
			}
			return parent::deleteObject($object);
		}

		return false;
	}

}

?>
