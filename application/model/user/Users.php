<?php

namespace model\user;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\user\UsersDBO as UsersDBO;

/* import related objects */
use \model\network\User_Network as User_Network;
use \model\network\User_NetworkDBO as User_NetworkDBO;
use \model\User_Series as User_Series;
use \model\User_SeriesDBO as User_SeriesDBO;

class Users extends _Users
{
	const AdministratorRole = "admin";
	const StandardRole = "user";

	/**
	 *	Create/Update functions
	 */
	public function create( $name, $email, $active, $account_type, $rememberme_token, $api_hash, $password_hash, $password_reset_hash, $activation_hash, $failed_logins, $creation_timestamp, $last_login_timestamp, $last_failed_login, $password_reset_timestamp)
	{
		return $this->base_create(
			$name,
			$email,
			$active,
			$account_type,
			$rememberme_token,
			$api_hash,
			$password_hash,
			$password_reset_hash,
			$activation_hash,
			$failed_logins,
			$creation_timestamp,
			$last_login_timestamp,
			$last_failed_login,
			$password_reset_timestamp
		);
	}

	public function update( UsersDBO $obj,
		$name, $email, $active, $account_type, $rememberme_token, $api_hash, $password_hash, $password_reset_hash, $activation_hash, $failed_logins, $creation_timestamp, $last_login_timestamp, $last_failed_login, $password_reset_timestamp)
	{
		if ( isset( $obj ) && is_null($obj) == false ) {
			return $this->base_update(
				$obj,
				$name,
				$email,
				$active,
				$account_type,
				$rememberme_token,
				$api_hash,
				$password_hash,
				$password_reset_hash,
				$activation_hash,
				$failed_logins,
				$creation_timestamp,
				$last_login_timestamp,
				$last_failed_login,
				$password_reset_timestamp
			);
		}
		return $obj;
	}


	public function attributesFor($object = null, $type = null) {
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
			Users::creation_timestamp => Model::DATE_TYPE,
			Users::last_login_timestamp => Model::DATE_TYPE,
			Users::last_failed_login => Model::DATE_TYPE,
			Users::password_reset_timestamp => Model::DATE_TYPE
		);
	}

	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Users::name,
				Users::email,
				Users::password_hash
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributeIsEditable($object = null, $type = null, $attr)
	{
		// add customization here
		return parent::attributeIsEditable($object, $type, $attr);
	}

	public function attributeRestrictionMessage($object = null, $type = null, $attr)
	{
		if ( $attr == Users::name ) {
			return Localized::ModelRestriction($this->tableName(), $attr );
		}

		if ( $attr == Users::email ) {
			return Localized::ModelRestriction($this->tableName(), $attr );
		}

		if ( $attr == "password" ) {
			return Localized::ModelRestriction($this->tableName(), $attr );
		}

		return null;
	}
	/*
	public function attributePlaceholder($object = null, $type = null, $attr)	{ return null; }
	*/
	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) == false || is_null($object) == true) {
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

	public function attributeEditPattern($object = null, $type = null, $attr)
	{
		if ( $attr == Users::name ) {
			return "[a-zA-Z0-9]{2,64}";
		}

		return null;
	}

	public function attributeOptions($object = null, $type = null, $attr)
	{
		return null;
	}

	/** Validation */
	function validate_name($object = null, $value)
	{
		if (empty($value)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Users::name,
				"FIELD_EMPTY"
			);
		}
		// make sure Name is unique
		$existing = $this->objectForName($value);
		if ( is_null($object) == false && $existing != false && $existing->id != $object->id) {
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
		if (empty($value)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Users::email,
				"FIELD_EMPTY"
			);
		}
		// make sure Email is unique
		$existing = $this->objectForEmail($value);
		if ( is_null($object) == false && $existing != false && $existing->id != $object->id) {
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
		return null;
	}
	function validate_account_type($object = null, $value)
	{
		return null;
	}
	function validate_rememberme_token($object = null, $value)
	{
		// make sure Rememberme_token is unique
		$existing = $this->objectForRememberme_token($value);
		if ( is_null($object) == false && $existing != false && $existing->id != $object->id) {
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
		// make sure Api_hash is unique
		$existing = $this->objectForApi_hash($value);
		if ( is_null($object) == false && $existing != false && $existing->id != $object->id) {
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
		return null;
	}
	function validate_activation_hash($object = null, $value)
	{
		// make sure Activation_hash is unique
		$existing = $this->objectForActivation_hash($value);
		if ( is_null($object) == false && $existing != false && $existing->id != $object->id) {
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
