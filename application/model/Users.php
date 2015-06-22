<?php

namespace model;

use \Session as Session;
use \DataObject as DataObject;
use \Model as Model;
use \Localized as Localized;

use db\Qualifier as Qualifier;

class Users extends Model
{
	const AdministratorRole = "admin";
	const StandardRole = "standard";

	const TABLE = 'users';

	const id = 'id';
	const name = 'name';
	const password_hash = 'password_hash';
	const email = 'email';
	const active = 'active';
	const account_type = 'account_type';
	const rememberme_token = 'rememberme_token';
	const creation_timestamp = 'creation_timestamp';
	const last_login_timestamp = 'last_login_timestamp';
	const failed_logins = 'failed_logins';
	const last_failed_login = 'last_failed_login';
	const activation_hash = 'activation_hash';
	const api_hash = 'api_hash';
	const password_reset_hash = 'password_reset_hash';
	const password_reset_timestamp = 'password_reset_timestamp';

	public function tableName() { return Users::TABLE; }
	public function tablePK() { return Users::id; }
	public function sortOrder() { return array(Users::name); }

	public function userTypes() {
		return array( Users::StandardRole => "Standard", Users::AdministratorRole => "Administrator");

	}

	public function allColumnNames()
	{
		return array(
			Users::id, Users::name, Users::password_hash, Users::email, Users::active, Users::account_type,
			Users::rememberme_token, Users::creation_timestamp, Users::last_login_timestamp,
			Users::failed_logins, Users::last_failed_login, Users::activation_hash, Users::password_reset_hash,
			Users::password_reset_timestamp, Users::api_hash
		);
	}

	public function allUsers($role = null)
	{
		return $this->allObjectsForKeyValue(Users::account_type, $role);
	}

	public function userByName($name)
	{
		return $this->singleObjectForKeyValue(Users::name, $name);
	}

	public function userByToken($id, $token)
	{
		$qualifier = Qualifier::AndQualifier(
			Qualifier::Equals( Users::id, $id ),
			Qualifier::Equals( Users::rememberme_token, $token )
		);
		return $this->singleObject($qualifier);
	}

	public function userByApiHash($token = '')
	{
		return $this->singleObjectForKeyValue(Users::api_hash, $token);
	}

	function createUserIfMissing($name, $pass, $email, $type)
	{
		$user = $this->userByName($name);
		if ( $user === false )
		{
			$user = $this->userByName($email);
			if ( $user === false )
			{
				if ( PHP_VERSION_ID > 50500 )
				{
					$hash_cost_factor = (defined('HASH_COST_FACTOR') ? HASH_COST_FACTOR : null);
					$password_hash = password_hash($pass, PASSWORD_DEFAULT, array('cost' => $hash_cost_factor));
				}
				else
				{
					$password_hash = hash(HASH_DEFAULT_ALGO, $pass);
				}

				$user = $this->create($name, $password_hash, $email, true, $type,
					null, null, 0, null, null, null, null);
			}
		}
		return $user;
	}

	public function create($name, $password_hash, $email, $active, $account_type, $rememberme_token,
					$last_login_timestamp, $failed_logins, $last_failed_login, $activation_hash,
					$password_reset_hash, $password_reset_timestamp)
	{
		return $this->createObject(array(
			Users::name => strip_tags($name),
			Users::password_hash => $password_hash,
			Users::email => strip_tags($email),
			Users::active => ($active) ? 1 : 0,
			Users::account_type => $account_type,
			Users::rememberme_token => $rememberme_token,
			Users::api_hash => uuidShort(),
			Users::creation_timestamp => time(),
			Users::last_login_timestamp => $last_login_timestamp,
			Users::failed_logins => $failed_logins,
			Users::last_failed_login => $last_failed_login,
			Users::activation_hash => $activation_hash,
			Users::password_reset_hash => $password_reset_hash,
			Users::password_reset_timestamp => $password_reset_timestamp
			)
		);
	}


	public function generateAPIHash($userObj = null)
	{
		if ( is_a($userObj, "model\\UsersDBO" )) {				;
			if ($this->updateObject( $userObj, array(Users::api_hash => uuidShort())) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setAPIHash($userObj, $newHash = null)
	{
		if ($this->updateObject( $userObj, array(Users::api_hash => $newHash)) )
		{
			return $this->refreshObject($userObj);
		}
		return false;
	}

	public function setInactive($userObj)
	{
		return $this->updateObject( $userObj, array(Users::active => 0));
	}

	public function clearFailedLogin($userObj)
	{
		return $this->updateObject( $userObj, array(Users::failed_logins => 0, Users::last_failed_login => null));
	}

	public function updatePassword($userObj, $newpassword_hash )
	{
		$update = \SQL::UpdateObject( $userObj, array( Users::password_hash => $newpassword_hash ));
		$update->whereEqual( Users::password_hash, $userObj->password_hash );
		if ( $update->commitTransaction() ) {
			return $this->refreshObject($userObj);
		}
		else {
			throw new \Exception( "Failed to update password" );
		}
		return false;
	}

	public function updateObject(DataObject $object = null, array $values) {
		$updates = array();
		if (isset($object) && is_a($object, "\\model\UsersDBO" )) {

			if ( isset($values[Users::name]) && $values[Users::name] != $object->name ) {
				$updates[Users::name] = $values[Users::name];
			}

			if ( isset($values[Users::account_type]) && $values[Users::account_type] != $object->account_type ) {
				$updates[Users::account_type] = $values[Users::account_type];
			}

			if ( isset($values[Users::email]) && $values[Users::email] != $object->email ) {
				$updates[Users::email] = $values[Users::email];
			}

			if ( isset($values[Users::active]) && $values[Users::active] != $object->isActive()) {
				$updates[Users::active] = ($values[Users::active]) ? 1 : 0;
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
				$updates['password'] = $values['password'];
				$updates['password_check'] = $values['password_check'];
				$updates[Users::password_hash] = $password_hash;
			}
		}

		return parent::updateObject($object, $updates);
	}

	public function createObject(array $values = array()) {
		if ( isset($values) && isset($values[Users::creation_timestamp]) == false) {
			$values[Users::creation_timestamp] = time();
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

		return parent::createObject($values);
	}

	public function updateNameAndEmail($userObj, $newname, $newemail )
	{
		$changes = array();
		if ( $userObj->name != $newname ) {
			$changes[Users::name] = $newname;
		}
		if ( $userObj->email != $newemail ) {
			$changes[Users::email] = $newemail;
		}

		if (count($changes) > 0) {
			if ($this->updateObject( $userObj, $changes) ) {
				return $this->objectForId($userObj->id);
			}
		}
		return false;
	}

	function increaseFailedLogin($userObj)
	{
		if ($this->updateObject( $userObj, array(
			Users::failed_logins => $userObj->failed_logins + 1, Users::last_failed_login => time() )) ) {
			return $this->refreshObject($userObj);
		}
		return false;
	}

	function stampLoginTimestamp($userObj)
	{
		if ($this->updateObject( $userObj, array(Users::last_login_timestamp => time() )) ) {
			return $this->refreshObject($userObj);
		}
		return false;
	}

	function generateRememberMeToken($userObj)
	{
		$random_token_string = hash('sha256', mt_rand());
		if ($this->updateObject( $userObj, array(Users::rememberme_token => $random_token_string)) ) {
			return $random_token_string;
		}
		return false;
	}

	public function validateForSave($object = null, array &$values = array())
	{
		$validationErrors = parent::validateForSave($object, $values);

		if ( is_null($object) || isset($values, $values[Users::password_hash]) ) {
			$pswd = (isset($values['password']) ? $values['password'] : null);
			$chk = (isset($values['password_check']) ? $values['password_check'] : null);
			$error = $this->validatePassword($object, $pswd, $chk);
			if ( empty($error) == false ) {
				$validationErrors['password'] = $error;
			}
		}
		return $validationErrors;
	}

	function validate_name($object = null, $username)
	{
		if (empty($username))
		{
			return Localized::ModelValidation($this->tableName(), Users::name, "USERNAME_FIELD_EMPTY");
		}
		elseif (strlen($username) > 64 OR strlen($username) < 2)
		{
			return Localized::ModelValidation($this->tableName(), Users::name, "USERNAME_TOO_SHORT_OR_TOO_LONG");
		}
		elseif (!preg_match('/^[a-z\d]{2,64}$/i', $username))
		{
			return Localized::ModelValidation($this->tableName(), Users::name, "USERNAME_DOES_NOT_FIT_PATTERN");
		}
		else
		{
			// make sure username is unique
			$user = $this->userByName($username);
			if ( is_null($object) == false && $user != false && $user->id != $object->id)
			{
				return Localized::ModelValidation($this->tableName(), Users::name, "USERNAME_ALREADY_TAKEN");
			}
		}

		return null;
	}

	function validatePassword($object = null, $passwd = null, $passwdrepeat = null)
	{
		if (empty($passwd) OR empty($passwdrepeat))
		{
			return Localized::ModelValidation($this->tableName(), "password", "PASSWORD_FIELD_EMPTY");
		}
		elseif ($passwd !== $passwdrepeat)
		{
			return Localized::ModelValidation($this->tableName(), "password", "PASSWORD_REPEAT_WRONG");
		}
		elseif (strlen($passwd) < 6)
		{
			return Localized::ModelValidation($this->tableName(), "password", "PASSWORD_TOO_SHORT");
		}
		return null;
	}

	function validate_email($object = null, $email)
	{
		if (empty($email))
		{
			return Localized::ModelValidation($this->tableName(), Users::email, "EMAIL_FIELD_EMPTY");
		}
		elseif (strlen($email) > 64 OR strlen($email) < 5)
		{
			return Localized::ModelValidation($this->tableName(), Users::email, "EMAIL_TOO_LONG" );
		}
		elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			return Localized::ModelValidation($this->tableName(), Users::email, "EMAIL_DOES_NOT_FIT_PATTERN" );
		}
		else
		{
			// make sure email is unique
			$user = $this->userByName($email);
			if ( is_null($object) == false && $user != false && $user->id != $object->id)
			{
				return Localized::ModelValidation($this->tableName(), Users::email, "USER_EMAIL_ALREADY_TAKEN");
			}
		}

		return null;
	}

	/* EditableModelInterface */

	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Users::name,
				Users::email,
				"password",
				"password_check"
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesFor($object = null, $type = null ) {
		return array(
			Users::name => Model::TEXT_TYPE,
			Users::email => Model::TEXT_TYPE,
			"password" => Model::PASSWORD_TYPE,
			"password_check" => Model::PASSWORD_TYPE,
			Users::active => Model::FLAG_TYPE,
		);
	}

	public function attributeOptions($object = null, $type = null, $attr) {
		return null;
	}

	public function attributeIsEditable($object = null, $type = null, $attr) {
		return true;
	}

	public function attributeEditPattern($object = null, $type = null, $attr)
	{
		if ( $attr == Users::name ) {
			return "[a-zA-Z0-9]{2,64}";
		}

		return null;
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

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) == false || is_null($object) == true) {
			switch ($attr) {
				case Users::active:
					return Model::TERTIARY_TRUE;
			}
		}
		return parent::attributeDefaultValue($object, $type, $attr);
	}
}

?>
