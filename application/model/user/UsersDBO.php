<?php

namespace model\user;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \db\Qualifier as Qualifier;

use \model\user\Users as Users;

/* import related objects */
use \model\network\User_Network as User_Network;
use \model\network\User_NetworkDBO as User_NetworkDBO;
use \model\media\Series as Series;
use \model\media\SeriesDBO as SeriesDBO;

class UsersDBO extends _UsersDBO
{

	public function recordLoginFromIp_address($ip = null)
	{
		$join_model = Model::Named('User_Network');
		return $join_model->createForIp_address($this, $ip);
	}

	public function allLoginIP() {
		$join_model = Model::Named('User_Network');
		$array = $join_model->allForUser($this);
		return $array;
	}

	/** login tracking */
	function increaseFailedLogin()
	{
		$count = $this->failed_logins();
		if ( is_null($count) || intval($count) == 0) {
			$count = 1;
		}
		else {
			$count ++;
		}
		$this->setFailed_logins($count);
		$this->setLast_failed_login(time());
		return $this->saveChanges();
	}

	public function clearFailedLogin()
	{
		$this->setFailed_logins(0);
		$this->setLast_failed_login(null);
		return $this->saveChanges();
	}

	function stampLogin()
	{
		$this->setLast_login_timestamp(time());
		$this->setFailed_logins(0);
		$this->setLast_failed_login(null);
		return $this->saveChanges();
	}

	/** token generation */
	function generateRememberme_token()
	{
		$random_token_string = hash(HASH_DEFAULT_ALGO, mt_rand());
		$this->setRememberme_token($random_token_string);
		if ( $this->saveChanges() ) {
			return $this->rememberme_token();
		}
		return false;
	}

	function generateActivation_hash()
	{
		$random_token_string = hash(HASH_DEFAULT_ALGO, mt_rand());
		$this->setActivation_hash($random_token_string);
		if ( $this->saveChanges() ) {
			return $this->activation_hash();
		}
		return false;
	}

	function generatePassword_reset_hash()
	{
		$random_token_string = hash(HASH_DEFAULT_ALGO, mt_rand());
		$this->setPassword_reset_hash($random_token_string);
		$this->setPassword_reset_timestamp(time());
		if ( $this->saveChanges() ) {
			return $this->password_reset_hash();
		}
		return false;
	}

	public function generateApi_hash($userObj = null)
	{
		// API hash codes often must be types by users into other systems, UUID's are easier to use than sha265
		$random_token_string = uuidShort();
		$this->setApi_hash($random_token_string);
		if ( $this->saveChanges() ) {
			return $this->api_hash();
		}
		return false;
	}

}

?>
