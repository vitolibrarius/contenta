<?php

namespace model\user;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use model\user\Users as Users;

class UsersDBO extends DataObject
{
	public $name;
	public $email;
	public $active;
	public $account_type;
	public $rememberme_token;
	public $api_hash;
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
		return (isset($this->active) && $this->active == 1);
	}

	public function formattedDateTimeCreation_timestamp() { return $this->formattedDate( Users::creation_timestamp, "M d, Y H:i" ); }
	public function formattedDateCreation_timestamp() {return $this->formattedDate( Users::creation_timestamp, "M d, Y" ); }

	public function formattedDateTimeLast_login_timestamp() { return $this->formattedDate( Users::last_login_timestamp, "M d, Y H:i" ); }
	public function formattedDateLast_login_timestamp() {return $this->formattedDate( Users::last_login_timestamp, "M d, Y" ); }

	public function formattedDateTimeLast_failed_login() { return $this->formattedDate( Users::last_failed_login, "M d, Y H:i" ); }
	public function formattedDateLast_failed_login() {return $this->formattedDate( Users::last_failed_login, "M d, Y" ); }

	public function formattedDateTimePassword_reset_timestamp() { return $this->formattedDate( Users::password_reset_timestamp, "M d, Y H:i" ); }
	public function formattedDatePassword_reset_timestamp() {return $this->formattedDate( Users::password_reset_timestamp, "M d, Y" ); }


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
