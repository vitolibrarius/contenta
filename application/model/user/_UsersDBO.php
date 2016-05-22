<?php

namespace model\user;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\user\Users as Users;

/* import related objects */
use \model\network\User_Network as User_Network;
use \model\network\User_NetworkDBO as User_NetworkDBO;
use \model\User_Series as User_Series;
use \model\User_SeriesDBO as User_SeriesDBO;

abstract class _UsersDBO extends DataObject
{
	public $name;
	public $email;
	public $active;
	public $account_type;
	public $rememberme_token;
	public $api_hash;
	public $password_hash;
	public $password_reset_hash;
	public $activation_hash;
	public $failed_logins;
	public $creation_timestamp;
	public $last_login_timestamp;
	public $last_failed_login;
	public $password_reset_timestamp;

	public function displayName()
	{
		return $this->name;
	}

	public function isActive() {
		return (isset($this->active) && $this->active == Model::TERTIARY_TRUE);
	}

	public function formattedDateTime_creation_timestamp() { return $this->formattedDate( Users::creation_timestamp, "M d, Y H:i" ); }
	public function formattedDate_creation_timestamp() {return $this->formattedDate( Users::creation_timestamp, "M d, Y" ); }

	public function formattedDateTime_last_login_timestamp() { return $this->formattedDate( Users::last_login_timestamp, "M d, Y H:i" ); }
	public function formattedDate_last_login_timestamp() {return $this->formattedDate( Users::last_login_timestamp, "M d, Y" ); }

	public function formattedDateTime_last_failed_login() { return $this->formattedDate( Users::last_failed_login, "M d, Y H:i" ); }
	public function formattedDate_last_failed_login() {return $this->formattedDate( Users::last_failed_login, "M d, Y" ); }

	public function formattedDateTime_password_reset_timestamp() { return $this->formattedDate( Users::password_reset_timestamp, "M d, Y H:i" ); }
	public function formattedDate_password_reset_timestamp() {return $this->formattedDate( Users::password_reset_timestamp, "M d, Y" ); }


	// to-many relationship
	public function user_network()
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('User_Network');
			return $model->allObjectsForKeyValue( User_Network::user_id, $this->id);
		}

		return false;
	}

	// to-many relationship
	public function user_series()
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('User_Series');
			return $model->allObjectsForKeyValue( User_Series::user_id, $this->id);
		}

		return false;
	}

}

?>
