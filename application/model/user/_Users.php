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
use \model\reading\Reading_Queue as Reading_Queue;
use \model\reading\Reading_QueueDBO as Reading_QueueDBO;
use \model\reading\Reading_Item as Reading_Item;
use \model\reading\Reading_ItemDBO as Reading_ItemDBO;

/** Generated class, do not edit.
 */
abstract class _Users extends Model
{
	const TABLE = 'users';

	// attribute keys
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
	const created = 'created';
	const last_login_timestamp = 'last_login_timestamp';
	const last_failed_login = 'last_failed_login';
	const password_reset_timestamp = 'password_reset_timestamp';

	// relationship keys
	const user_network = 'user_network';
	const reading_queues = 'reading_queues';
	const reading_items = 'reading_items';

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
			Users::created,
			Users::last_login_timestamp,
			Users::last_failed_login,
			Users::password_reset_timestamp
		);
	}

	public function allAttributes()
	{
		return array(
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
			Users::created,
			Users::last_login_timestamp,
			Users::last_failed_login,
			Users::password_reset_timestamp
		);
	}

	public function allForeignKeys()
	{
		return array();
	}

	public function allRelationshipNames()
	{
		return array(
			Users::user_network,
			Users::reading_queues,
			Users::reading_items
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






	/**
	 * Simple relationship fetches
	 */

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "user_network":
					return array( Users::id, "user_id"  );
					break;
				case "reading_queue":
					return array( Users::id, "user_id"  );
					break;
				case "reading_item":
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

			// default values for attributes
			if ( isset($values['name']) == false ) {
				$default_name = $this->attributeDefaultValue( null, null, Users::name);
				if ( is_null( $default_name ) == false ) {
					$values['name'] = $default_name;
				}
			}
			if ( isset($values['email']) == false ) {
				$default_email = $this->attributeDefaultValue( null, null, Users::email);
				if ( is_null( $default_email ) == false ) {
					$values['email'] = $default_email;
				}
			}
			if ( isset($values['active']) == false ) {
				$default_active = $this->attributeDefaultValue( null, null, Users::active);
				if ( is_null( $default_active ) == false ) {
					$values['active'] = $default_active;
				}
			}
			if ( isset($values['account_type']) == false ) {
				$default_account_type = $this->attributeDefaultValue( null, null, Users::account_type);
				if ( is_null( $default_account_type ) == false ) {
					$values['account_type'] = $default_account_type;
				}
			}
			if ( isset($values['rememberme_token']) == false ) {
				$default_rememberme_token = $this->attributeDefaultValue( null, null, Users::rememberme_token);
				if ( is_null( $default_rememberme_token ) == false ) {
					$values['rememberme_token'] = $default_rememberme_token;
				}
			}
			if ( isset($values['api_hash']) == false ) {
				$default_api_hash = $this->attributeDefaultValue( null, null, Users::api_hash);
				if ( is_null( $default_api_hash ) == false ) {
					$values['api_hash'] = $default_api_hash;
				}
			}
			if ( isset($values['password_hash']) == false ) {
				$default_password_hash = $this->attributeDefaultValue( null, null, Users::password_hash);
				if ( is_null( $default_password_hash ) == false ) {
					$values['password_hash'] = $default_password_hash;
				}
			}
			if ( isset($values['password_reset_hash']) == false ) {
				$default_password_reset_hash = $this->attributeDefaultValue( null, null, Users::password_reset_hash);
				if ( is_null( $default_password_reset_hash ) == false ) {
					$values['password_reset_hash'] = $default_password_reset_hash;
				}
			}
			if ( isset($values['activation_hash']) == false ) {
				$default_activation_hash = $this->attributeDefaultValue( null, null, Users::activation_hash);
				if ( is_null( $default_activation_hash ) == false ) {
					$values['activation_hash'] = $default_activation_hash;
				}
			}
			if ( isset($values['failed_logins']) == false ) {
				$default_failed_logins = $this->attributeDefaultValue( null, null, Users::failed_logins);
				if ( is_null( $default_failed_logins ) == false ) {
					$values['failed_logins'] = $default_failed_logins;
				}
			}
			if ( isset($values['created']) == false ) {
				$default_created = $this->attributeDefaultValue( null, null, Users::created);
				if ( is_null( $default_created ) == false ) {
					$values['created'] = $default_created;
				}
			}
			if ( isset($values['last_login_timestamp']) == false ) {
				$default_last_login_timestamp = $this->attributeDefaultValue( null, null, Users::last_login_timestamp);
				if ( is_null( $default_last_login_timestamp ) == false ) {
					$values['last_login_timestamp'] = $default_last_login_timestamp;
				}
			}
			if ( isset($values['last_failed_login']) == false ) {
				$default_last_failed_login = $this->attributeDefaultValue( null, null, Users::last_failed_login);
				if ( is_null( $default_last_failed_login ) == false ) {
					$values['last_failed_login'] = $default_last_failed_login;
				}
			}
			if ( isset($values['password_reset_timestamp']) == false ) {
				$default_password_reset_timestamp = $this->attributeDefaultValue( null, null, Users::password_reset_timestamp);
				if ( is_null( $default_password_reset_timestamp ) == false ) {
					$values['password_reset_timestamp'] = $default_password_reset_timestamp;
				}
			}

			// default conversion for relationships
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
			if ( $user_network_model->deleteAllForKeyValue(User_Network::user_id, $object->id) == false ) {
				return false;
			}
			$reading_queue_model = Model::Named('Reading_Queue');
			if ( $reading_queue_model->deleteAllForKeyValue(Reading_Queue::user_id, $object->id) == false ) {
				return false;
			}
			$reading_item_model = Model::Named('Reading_Item');
			if ( $reading_item_model->deleteAllForKeyValue(Reading_Item::user_id, $object->id) == false ) {
				return false;
			}
			return parent::deleteObject($object);
		}

		return false;
	}


	/**
	 * Named fetches
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


	/**
	 * Attribute editing
	 */
	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Users::name,
				Users::email,
				Users::active,
				Users::account_type,
				Users::password_hash
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesMap() {
		return array(
			Users::name => Model::TEXT_TYPE,
			Users::email => Model::TEXT_TYPE,
			Users::active => Model::FLAG_TYPE,
			Users::account_type => Model::TEXT_TYPE,
			Users::rememberme_token => Model::TEXT_TYPE,
			Users::api_hash => Model::TEXT_TYPE,
			Users::password_hash => Model::TEXT_TYPE,
			Users::password_reset_hash => Model::TEXT_TYPE,
			Users::activation_hash => Model::TEXT_TYPE,
			Users::failed_logins => Model::INT_TYPE,
			Users::created => Model::DATE_TYPE,
			Users::last_login_timestamp => Model::DATE_TYPE,
			Users::last_failed_login => Model::DATE_TYPE,
			Users::password_reset_timestamp => Model::DATE_TYPE
		);
	}

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) === false || is_null($object) == true) {
			switch ($attr) {
				case Users::account_type:
					return 'user';
				case Users::failed_logins:
					return 0;
				case Users::last_login_timestamp:
					return null;
				case Users::last_failed_login:
					return null;
				case Users::password_reset_timestamp:
					return null;
			}
		}
		return parent::attributeDefaultValue($object, $type, $attr);
	}

	/*
	 * return the foreign key object
	 */
	public function attributeObject($object = null, $type = null, $attr, $value)
	{
		$fkObject = false;
		if ( isset( $attr ) ) {
			switch ( $attr ) {
				default:
					break;
			}
		}
		return $fkObject;
	}

	/**
	 * Validation
	 */
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
	function validate_created($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// created date is not changeable
		if ( isset($object, $object->created) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Users::created,
				"IMMUTABLE"
			);
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
