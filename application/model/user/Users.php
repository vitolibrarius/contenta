<?php

namespace model\user;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \model\user\UsersDBO as UsersDBO;

/* import related objects */
use \model\network\User_Network as User_Network;
use \model\network\User_NetworkDBO as User_NetworkDBO;
use \model\media\User_Series as User_Series;
use \model\media\User_SeriesDBO as User_SeriesDBO;

class Users extends _Users
{
	const AdministratorRole = "admin";
	const StandardRole = "user";

	/**
	 *	Create/Update functions
	 */
	public function createObject(array $values = array())
	{
		if ( isset($values) ) {
			if ( isset($values[Users::creation_timestamp]) == false) {
				$values[Users::creation_timestamp] = time();
			}

			if ( isset($values[Users::active]) ) {
				$values[Users::active] = boolValue($values[Users::active], true);
			}
			else {
				$values[Users::active] = true;
			}

			if ( isset($values[Users::account_type]) == false) {
				$values[Users::account_type] = Users::StandardRole;
			}

			if ( isset($values['password'], $values['password_check'])
				&& empty($values['password']) == false && empty($values['password_check']) == false ) {
				if ( PHP_VERSION_ID > 50500 )
				{
					$hash_cost_factor = (defined('HASH_COST_FACTOR') ? HASH_COST_FACTOR : null);
					$password_hash = password_hash($values['password'], PASSWORD_DEFAULT, array('cost' => $hash_cost_factor));
				}
				else
				{
					$password_hash = hash(HASH_DEFAULT_ALGO, $values['password']);
				}
				$values[Users::password_hash] = $password_hash;
			}
		}

		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = NULL, array $values = array()) {
		if (isset($object) && $object instanceof UsersDBO ) {
			if ( isset($values['password'], $values['password_check'])
				&& empty($values['password']) == false && empty($values['password_check']) == false
				&& $values['password'] === $values['password_check']) {
				if ( PHP_VERSION_ID > 50500 )
				{
					$hash_cost_factor = (defined('HASH_COST_FACTOR') ? HASH_COST_FACTOR : null);
					$password_hash = password_hash($values['password'], PASSWORD_DEFAULT, array('cost' => $hash_cost_factor));
				}
				else
				{
					$password_hash = hash(HASH_DEFAULT_ALGO, $values['password']);
				}
				$values[Users::password_hash] = $password_hash;
			}

			if ( isset($values[Users::active]) == true) {
				$values[Users::active] = boolValue($values[Users::active], true);
			}
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null ) {
		$attrFor = array(
			Users::name,
			Users::email,
			Users::active
		);
		$results = array_intersect_key($this->attributesMap(),array_flip($attrFor));
		$results["password"] = Model::PASSWORD_TYPE;
		$results["password_check"] = Model::PASSWORD_TYPE;
		return $results;
	}

	/*
	public function attributeIsEditable($object = null, $type = null, $attr)
	{
		// add customization here
		return parent::attributeIsEditable($object, $type, $attr);
	}
	*/

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

	/*
	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		return parent::attributeDefaultValue($object, $type, $attr);
	}
	*/

	public function attributeEditPattern($object = null, $type = null, $attr)
	{
		if ( $attr == Users::name ) {
			return "/^[a-zA-Z0-9]{2,64}$/";
		}

		return null;
	}

	public function attributeOptions($object = null, $type = null, $attr)
	{
		return null;
	}

	/** Validation */
	public function validateForSave($object = null, array &$values = array())
	{
		$validationErrors = parent::validateForSave($object, $values);

		// if the object is null, then the password is mandatory
		if ( is_null($object) || isset($values[Users::password_hash]) ) {
			$pswd = (isset($values['password']) ? $values['password'] : null);
			$chk = (isset($values['password_check']) ? $values['password_check'] : null);
			$error = $this->validatePassword($object, $pswd, $chk);
			if ( empty($error) == false ) {
				$validationErrors['password'] = $error;
			}
		}
		return $validationErrors;
	}

	function validatePassword($object = null, $passwd = null, $passwdrepeat = null)
	{
		if (empty($passwd) OR empty($passwdrepeat)) {
			return Localized::ModelValidation($this->tableName(), "password", "PASSWORD_FIELD_EMPTY");
		}
		else if ($passwd !== $passwdrepeat) {
			return Localized::ModelValidation($this->tableName(), "password", "PASSWORD_REPEAT_WRONG");
		}
		else if (strlen($passwd) < 6) {
			return Localized::ModelValidation($this->tableName(), "password", "PASSWORD_TOO_SHORT");
		}
		return null;
	}

	function validate_name($object = null, $value)
	{
		$validation = parent::validate_name($object, $value);
		if ( is_null($validation) ) {
			$pattern = $this->attributeEditPattern ( $object, null, Users::name);
			$match = preg_match( $pattern, $value );
			if ($match === 0) {
				return Localized::ModelValidation(
					$this->tableName(),
					Users::name,
					"INVALID_PATTERN"
				);
			}
		}
		return $validation;
	}

/*
	function validate_email($object = null, $value)
	{
		return parent::validate_email($object, $value);
	}
*/

/*
	function validate_active($object = null, $value)
	{
		return parent::validate_active($object, $value);
	}
*/

	function validate_account_type($object = null, $value)
	{
		$validation = parent::validate_account_type($object, $value);
		if ( is_null($validation) ) {
			if ( in_array( $value, array(Users::StandardRole, Users::AdministratorRole) ) == false ) {
				return Localized::ModelValidation(
					$this->tableName(),
					Users::account_type,
					"INVALID"
				);
			}
		}
		return $validation;
	}

/*
	function validate_rememberme_token($object = null, $value)
	{
		return parent::validate_rememberme_token($object, $value);
	}
*/

/*
	function validate_api_hash($object = null, $value)
	{
		return parent::validate_api_hash($object, $value);
	}
*/

/*
	function validate_password_hash($object = null, $value)
	{
		return parent::validate_password_hash($object, $value);
	}
*/

/*
	function validate_password_reset_hash($object = null, $value)
	{
		return parent::validate_password_reset_hash($object, $value);
	}
*/

/*
	function validate_activation_hash($object = null, $value)
	{
		return parent::validate_activation_hash($object, $value);
	}
*/

	function validate_failed_logins($object = null, $value)
	{
		$validation = parent::validate_failed_logins($object, $value);
		if ( is_null($validation) ) {
			if ( intval($value) < 0 ) {
				return Localized::ModelValidation(
					$this->tableName(),
					Users::failed_logins,
					"NEGATIVE"
				);
			}
		}
		return $validation;
	}

/*
	function validate_creation_timestamp($object = null, $value)
	{
		return parent::validate_creation_timestamp($object, $value);
	}
*/

/*
	function validate_last_login_timestamp($object = null, $value)
	{
		return parent::validate_last_login_timestamp($object, $value);
	}
*/

/*
	function validate_last_failed_login($object = null, $value)
	{
		return parent::validate_last_failed_login($object, $value);
	}
*/

/*
	function validate_password_reset_timestamp($object = null, $value)
	{
		return parent::validate_password_reset_timestamp($object, $value);
	}
*/

	public function userTypes() {
		return array( Users::StandardRole => "Standard", Users::AdministratorRole => "Administrator");
	}
}

?>
