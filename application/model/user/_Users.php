<?php

namespace model\user;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

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


	public function allForFailed_logins($value)
	{
		return $this->allObjectsForKeyValue(Users::failed_logins, $value);
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
	public function createObject( array $values = array() )
	{
		if ( isset($values) ) {
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Users ) {
		}

		return parent::updateObject($object, $values);
	}

	/**
	 *	Delete functions
	 */
	public function deleteObject( DataObject $object = null)
	{
		if ( $object instanceof UsersDBO )
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
	public function userWithRemembermeToken( $user_id, $token )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		$qualifiers[] = Qualifier::Equals( 'id', $user_id);
		$qualifiers[] = Qualifier::Equals( 'rememberme_token', $token);

		if ( count($qualifiers) > 0 ) {
			$select->where( Qualifier::Combine( 'AND', $qualifiers ));
		}

		$result = $select->fetchAll();
		if ( is_array($result) ) {
			$result_size = count($result);
			if ( $result_size == 1 ) {
				return $result[0];
			}
			else if ($result_size > 1 ) {
				throw new \Exception( "userWithRemembermeToken expected 1 result, but fetched " . count($result) );
			}
		}

		return false;
	}



	/** Validation */
	function validate_name($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
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
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Users::email,
				"FIELD_EMPTY"
			);
		}

		// email format
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
		// check for mandatory field
		if (isset($value) == false  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Users::active,
				"FIELD_EMPTY"
			);
		}

		// boolean

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
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Users::account_type,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_rememberme_token($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

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
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

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
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
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
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_activation_hash($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

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
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
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
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_last_login_timestamp($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_last_failed_login($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_password_reset_timestamp($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
}

?>
