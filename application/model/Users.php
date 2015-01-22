<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;

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
		$qualifier = null;
		if ( isset($role) ) {
			$qualifier = array(Users::account_type => $role );
		}

		return $this->fetchAll(Users::TABLE, $this->allColumns(), $qualifier, array(Users::name));
	}

	public function user($id)
	{
		return $this->fetch(Users::TABLE, $this->allColumns(), array(Users::id => $id));
	}

	public function userByName($name)
	{
		return $this->fetch(Users::TABLE, $this->allColumns(), array(Users::name => $name));
	}

	public function userByToken($id, $token)
	{
		return $this->fetch(Users::TABLE, $this->allColumns(), array(Users::id => $id, Users::rememberme_token => $token));
	}

	public function userByApiHash($token = '')
	{
		return $this->fetch(Users::TABLE, $this->allColumns(), array(Users::api_hash => $token));
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
		$userId = $this->createObj(Users::TABLE, array(
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
		return ($userId != false ? $this->user($userId) : false);
	}


	/**
	 * Deletes a specific note
	 * @param int $note_id id of the note
	 * @return bool feedback (was the note deleted properly ?)
	 */
	public function setAPIHash($userObj, $newHash)
	{
		if ($this->update(Users::TABLE, array( Users::api_hash => $newHash), array(Users::id => $userObj->id)))
		{
			return $this->refresh($userObj);
		}
		return false;
	}

	public function setInactive($userObj)
	{
		return $this->update(Users::TABLE, array( Users::active => 0), array(Users::id => $userObj->id));
	}

	public function clearFailedLogin($userObj)
	{
		return $this->update(Users::TABLE, array( Users::failed_logins => 0, Users::last_failed_login => null), array(Users::id => $userObj->id));
	}

	public function updatePassword($userObj, $newpassword_hash )
	{
		if ($this->update(Users::TABLE,
			array( Users::password_hash => $newpassword_hash ),
			array( Users::id => $userObj->id, Users::password_hash => $userObj->password_hash)) )
		{
			return $this->refresh($userObj);
		}
		return false;
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
			$success = $this->update(Users::TABLE, $changes, array( Users::id => $userObj->id ));
			if ($success != false)
			{
				return $this->user($userObj->id);
			}
		}
		return false;
	}

	function increaseFailedLogin($userObj)
	{
		if ( $this->update(Users::TABLE,
			array( Users::failed_logins => $userObj->failed_logins + 1, Users::last_failed_login => time() ),
			array( Users::id => $userObj->id)) )
		{
			return $this->refresh($userObj);
		}
		return false;
	}

	function stampLoginTimestamp($userObj)
	{
		if ( $this->update(Users::TABLE,
			array( Users::last_login_timestamp => time() ),
			array( Users::id => $userObj->id)))
		{
			return $this->refresh($userObj);
		}
		return false;
	}

	function generateRememberMeToken($userObj)
	{
		$random_token_string = hash('sha256', mt_rand());
		if ( $this->update(Users::TABLE,
			array( Users::rememberme_token => $random_token_string ),
			array( Users::id => $userObj->id)) ) {

			return $random_token_string;
		}
		return false;
	}

	function validateUsername($username, $newrecord = true)
	{
		$success = false;
		if (empty($username))
		{
			$_SESSION["feedback_negative"][] = FEEDBACK_USERNAME_FIELD_EMPTY;
		}
		elseif (strlen($username) > 64 OR strlen($username) < 2)
		{
			$_SESSION["feedback_negative"][] = FEEDBACK_USERNAME_TOO_SHORT_OR_TOO_LONG;
		}
		elseif (!preg_match('/^[a-z\d]{2,64}$/i', $username))
		{
			$_SESSION["feedback_negative"][] = FEEDBACK_USERNAME_DOES_NOT_FIT_PATTERN;
		}
		else
		{
			// make sure username is unique
			$user = $this->userByName($username);
			if ( $newrecord == false OR $user == false )
			{
				$success = true;
			}
			else
			{
				$_SESSION["feedback_negative"][] = FEEDBACK_USERNAME_ALREADY_TAKEN;
			}
		}

		return $success;
	}

	function validatePassword($passwd, $passwdrepeat)
	{
		$success = false;
		if (empty($passwd) OR empty($passwdrepeat))
		{
			$_SESSION["feedback_negative"][] = FEEDBACK_PASSWORD_FIELD_EMPTY;
		}
		elseif ($passwd !== $passwdrepeat)
		{
			$_SESSION["feedback_negative"][] = FEEDBACK_PASSWORD_REPEAT_WRONG;
		}
		elseif (strlen($passwd) < 6)
		{
			$_SESSION["feedback_negative"][] = FEEDBACK_PASSWORD_TOO_SHORT;
		}
		else
		{
			$success = true;
		}
		return $success;
	}

	function validateEmail($email, $newrecord = true)
	{
		$success = false;
		if (empty($email))
		{
			$_SESSION["feedback_negative"][] = FEEDBACK_EMAIL_FIELD_EMPTY;
		}
		elseif (strlen($email) > 64 OR strlen($email) < 5)
		{
			$_SESSION["feedback_negative"][] = FEEDBACK_EMAIL_TOO_LONG;
		}
		elseif (!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$_SESSION["feedback_negative"][] = FEEDBACK_EMAIL_DOES_NOT_FIT_PATTERN;
		}
		else
		{
			// make sure email is unique
			$user = $this->userByName($email);
			if ( $newrecord == false OR $user == false )
			{
				$success = true;
			}
			else
			{
				$_SESSION["feedback_negative"][] = FEEDBACK_USER_EMAIL_ALREADY_TAKEN;
			}
		}

		return $success;
	}
}

?>
