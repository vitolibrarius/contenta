<?php

namespace model\user;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
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
			. Users::password_hash . " TEXT, "
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
	const password_hash = 'password_hash';
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
			Users::password_hash,
			Users::password_reset_hash,
			Users::activation_hash,
			Users::failed_logins,
			Users::creation_timestamp,
			Users::last_login_timestamp,
			Users::last_failed_login,
			Users::password_reset_timestamp
		);
	}

	/**
	 *	Simple fetches
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

	public function allForPassword_hash($value)
	{
		return $this->allObjectsForKeyValue(Users::password_hash, $value);
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
				case "user_network":
					return array( Users::id, "user_id"  );
					break;
				case "user_series":
					return array( Users::id, "user_id"  );
					break;
				default:
					break;
			}
		}
		return parent::joinAttributes( $joinModel );
	}

	/**
	 *	Create/Update functions
	 */
	public function base_create( $name, $email, $active, $account_type, $rememberme_token, $api_hash, $password_hash, $password_reset_hash, $activation_hash, $failed_logins, $creation_timestamp, $last_login_timestamp, $last_failed_login, $password_reset_timestamp)
	{
		$obj = false;
		if ( isset($name, $email, $password_hash) ) {
			$params = array(
				Users::name => (isset($name) ? $name : null),
				Users::email => (isset($email) ? $email : null),
				Users::active => (isset($active) ? $active : Model::TERTIARY_TRUE),
				Users::account_type => (isset($account_type) ? $account_type : 'user'),
				Users::rememberme_token => (isset($rememberme_token) ? $rememberme_token : null),
				Users::api_hash => (isset($api_hash) ? $api_hash : null),
				Users::password_hash => (isset($password_hash) ? $password_hash : null),
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

	public function base_update( UsersDBO $obj,
		$name, $email, $active, $account_type, $rememberme_token, $api_hash, $password_hash, $password_reset_hash, $activation_hash, $failed_logins, $creation_timestamp, $last_login_timestamp, $last_failed_login, $password_reset_timestamp)
	{
		if ( isset( $obj ) && is_null($obj) == false ) {
			$updates = array();

			if (isset($name) && (isset($obj->name) == false || $name != $obj->name)) {
				$updates[Users::name] = $name;
			}
			if (isset($email) && (isset($obj->email) == false || $email != $obj->email)) {
				$updates[Users::email] = $email;
			}
			if (isset($active) && (isset($obj->active) == false || $active != $obj->active)) {
				$updates[Users::active] = $active;
			}
			if (isset($account_type) && (isset($obj->account_type) == false || $account_type != $obj->account_type)) {
				$updates[Users::account_type] = $account_type;
			}
			if (isset($rememberme_token) && (isset($obj->rememberme_token) == false || $rememberme_token != $obj->rememberme_token)) {
				$updates[Users::rememberme_token] = $rememberme_token;
			}
			if (isset($api_hash) && (isset($obj->api_hash) == false || $api_hash != $obj->api_hash)) {
				$updates[Users::api_hash] = $api_hash;
			}
			if (isset($password_hash) && (isset($obj->password_hash) == false || $password_hash != $obj->password_hash)) {
				$updates[Users::password_hash] = $password_hash;
			}
			if (isset($password_reset_hash) && (isset($obj->password_reset_hash) == false || $password_reset_hash != $obj->password_reset_hash)) {
				$updates[Users::password_reset_hash] = $password_reset_hash;
			}
			if (isset($activation_hash) && (isset($obj->activation_hash) == false || $activation_hash != $obj->activation_hash)) {
				$updates[Users::activation_hash] = $activation_hash;
			}
			if (isset($failed_logins) && (isset($obj->failed_logins) == false || $failed_logins != $obj->failed_logins)) {
				$updates[Users::failed_logins] = $failed_logins;
			}
			if (isset($creation_timestamp) && (isset($obj->creation_timestamp) == false || $creation_timestamp != $obj->creation_timestamp)) {
				$updates[Users::creation_timestamp] = $creation_timestamp;
			}
			if (isset($last_login_timestamp) && (isset($obj->last_login_timestamp) == false || $last_login_timestamp != $obj->last_login_timestamp)) {
				$updates[Users::last_login_timestamp] = $last_login_timestamp;
			}
			if (isset($last_failed_login) && (isset($obj->last_failed_login) == false || $last_failed_login != $obj->last_failed_login)) {
				$updates[Users::last_failed_login] = $last_failed_login;
			}
			if (isset($password_reset_timestamp) && (isset($obj->password_reset_timestamp) == false || $password_reset_timestamp != $obj->password_reset_timestamp)) {
				$updates[Users::password_reset_timestamp] = $password_reset_timestamp;
			}


			if ( count($updates) > 0 ) {
				list($obj, $errorList) = $this->updateObject( $obj, $updates );
				if ( is_array($errorList) ) {
					return $errorList;
				}
			}
		}
		return $obj;
	}

	/**
	 *	Delete functions
	 */
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


	/**
	 *	Named fetches
	 */

	/** Set attributes */
	public function setName( UsersDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Users::name => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setEmail( UsersDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Users::email => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setActive( UsersDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Users::active => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setAccount_type( UsersDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Users::account_type => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setRememberme_token( UsersDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Users::rememberme_token => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setApi_hash( UsersDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Users::api_hash => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setPassword_hash( UsersDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Users::password_hash => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setPassword_reset_hash( UsersDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Users::password_reset_hash => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setActivation_hash( UsersDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Users::activation_hash => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setFailed_logins( UsersDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Users::failed_logins => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setCreation_timestamp( UsersDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Users::creation_timestamp => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setLast_login_timestamp( UsersDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Users::last_login_timestamp => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setLast_failed_login( UsersDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Users::last_failed_login => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setPassword_reset_timestamp( UsersDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Users::password_reset_timestamp => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}


	/** Validation */
	function validate_name($object = null, $value)
	{
		$value = trim($value);
		if (empty($value)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Users::name,
				"FIELD_EMPTY"
			);
		}
		// make sure Name is unique
		$existing = $this->objectForName($value);
		if ( $existing != false && ( is_null($object) || $existing->id != $object->id)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Users::name,
				"UNIQUE_FIELD_VALUE"
			);
		}
		return null;
	}
	function validate_email($object = null, $value)
	{
		$value = trim($value);
		if (empty($value)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Users::email,
				"FIELD_EMPTY"
			);
		}
		if ( filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Users::email,
				"FILTER_VALIDATE_EMAIL"
			);
		}
		// make sure Email is unique
		$existing = $this->objectForEmail($value);
		if ( $existing != false && ( is_null($object) || $existing->id != $object->id)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Users::email,
				"UNIQUE_FIELD_VALUE"
			);
		}
		return null;
	}
	function validate_active($object = null, $value)
	{
		if ( is_null($value) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Users::active,
				"FIELD_EMPTY"
			);
		}

		// Returns TRUE for "1", "true", "on" and "yes"
		// Returns FALSE for "0", "false", "off" and "no"
		// Returns NULL otherwise.
		$v = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
		if (is_null($v)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Users::active,
				"FILTER_VALIDATE_BOOLEAN"
			);
		}
		return null;
	}
	function validate_account_type($object = null, $value)
	{
		$value = trim($value);
		return null;
	}
	function validate_rememberme_token($object = null, $value)
	{
		$value = trim($value);
		// make sure Rememberme_token is unique
		$existing = $this->objectForRememberme_token($value);
		if ( $existing != false && ( is_null($object) || $existing->id != $object->id)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Users::rememberme_token,
				"UNIQUE_FIELD_VALUE"
			);
		}
		return null;
	}
	function validate_api_hash($object = null, $value)
	{
		$value = trim($value);
		// make sure Api_hash is unique
		$existing = $this->objectForApi_hash($value);
		if ( $existing != false && ( is_null($object) || $existing->id != $object->id)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Users::api_hash,
				"UNIQUE_FIELD_VALUE"
			);
		}
		return null;
	}
	function validate_password_hash($object = null, $value)
	{
		$value = trim($value);
		if (empty($value)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Users::password_hash,
				"FIELD_EMPTY"
			);
		}
		return null;
	}
	function validate_password_reset_hash($object = null, $value)
	{
		$value = trim($value);
		return null;
	}
	function validate_activation_hash($object = null, $value)
	{
		$value = trim($value);
		// make sure Activation_hash is unique
		$existing = $this->objectForActivation_hash($value);
		if ( $existing != false && ( is_null($object) || $existing->id != $object->id)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Users::activation_hash,
				"UNIQUE_FIELD_VALUE"
			);
		}
		return null;
	}
	function validate_failed_logins($object = null, $value)
	{
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Users::failed_logins,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
	function validate_creation_timestamp($object = null, $value)
	{
		return null;
	}
	function validate_last_login_timestamp($object = null, $value)
	{
		return null;
	}
	function validate_last_failed_login($object = null, $value)
	{
		return null;
	}
	function validate_password_reset_timestamp($object = null, $value)
	{
		return null;
	}
}

?>
