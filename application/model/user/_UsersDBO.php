<?php

namespace model\user;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \model\user\Users as Users;

/* import related objects */
use \model\network\User_Network as User_Network;
use \model\network\User_NetworkDBO as User_NetworkDBO;
use \model\reading\Reading_Queue as Reading_Queue;
use \model\reading\Reading_QueueDBO as Reading_QueueDBO;
use \model\reading\Reading_Item as Reading_Item;
use \model\reading\Reading_ItemDBO as Reading_ItemDBO;

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
	public $created;
	public $last_login_timestamp;
	public $last_failed_login;
	public $password_reset_timestamp;

	public function displayName()
	{
		return $this->name;
	}

	public function pkValue()
	{
		return $this->{Users::id};
	}

	public function modelName()
	{
		return "Users";
	}

	public function dboName()
	{
		return "\model\user\UsersDBO";
	}

	public function isActive() {
		return (isset($this->active) && $this->active == Model::TERTIARY_TRUE);
	}

	public function formattedDateTime_created() { return $this->formattedDate( Users::created, "M d, Y H:i" ); }
	public function formattedDate_created() {return $this->formattedDate( Users::created, "M d, Y" ); }

	public function formattedDateTime_last_login_timestamp() { return $this->formattedDate( Users::last_login_timestamp, "M d, Y H:i" ); }
	public function formattedDate_last_login_timestamp() {return $this->formattedDate( Users::last_login_timestamp, "M d, Y" ); }

	public function formattedDateTime_last_failed_login() { return $this->formattedDate( Users::last_failed_login, "M d, Y H:i" ); }
	public function formattedDate_last_failed_login() {return $this->formattedDate( Users::last_failed_login, "M d, Y" ); }

	public function formattedDateTime_password_reset_timestamp() { return $this->formattedDate( Users::password_reset_timestamp, "M d, Y H:i" ); }
	public function formattedDate_password_reset_timestamp() {return $this->formattedDate( Users::password_reset_timestamp, "M d, Y" ); }


	// to-many relationship
	public function user_network($limit = SQL::SQL_DEFAULT_LIMIT)
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('User_Network');
			return $model->allObjectsForKeyValue(
				User_Network::user_id,
				$this->id,
				null,
				$limit
			);
		}

		return false;
	}

	// to-many relationship
	public function reading_queues($limit = SQL::SQL_DEFAULT_LIMIT)
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Reading_Queue');
			return $model->allObjectsForKeyValue(
				Reading_Queue::user_id,
				$this->id,
				null,
				$limit
			);
		}

		return false;
	}

	// to-many relationship
	public function reading_items($limit = SQL::SQL_DEFAULT_LIMIT)
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Reading_Item');
			return $model->allObjectsForKeyValue(
				Reading_Item::user_id,
				$this->id,
				null,
				$limit
			);
		}

		return false;
	}


	/** Attributes */
	public function name()
	{
		return parent::changedValue( Users::name, $this->name );
	}

	public function setName( $value = null)
	{
		parent::storeChange( Users::name, $value );
	}

	public function email()
	{
		return parent::changedValue( Users::email, $this->email );
	}

	public function setEmail( $value = null)
	{
		parent::storeChange( Users::email, $value );
	}

	public function active()
	{
		return parent::changedValue( Users::active, $this->active );
	}

	public function setActive( $value = null)
	{
		parent::storeChange( Users::active, $value );
	}

	public function account_type()
	{
		return parent::changedValue( Users::account_type, $this->account_type );
	}

	public function setAccount_type( $value = null)
	{
		parent::storeChange( Users::account_type, $value );
	}

	public function rememberme_token()
	{
		return parent::changedValue( Users::rememberme_token, $this->rememberme_token );
	}

	public function setRememberme_token( $value = null)
	{
		parent::storeChange( Users::rememberme_token, $value );
	}

	public function api_hash()
	{
		return parent::changedValue( Users::api_hash, $this->api_hash );
	}

	public function setApi_hash( $value = null)
	{
		parent::storeChange( Users::api_hash, $value );
	}

	public function password_hash()
	{
		return parent::changedValue( Users::password_hash, $this->password_hash );
	}

	public function setPassword_hash( $value = null)
	{
		parent::storeChange( Users::password_hash, $value );
	}

	public function password_reset_hash()
	{
		return parent::changedValue( Users::password_reset_hash, $this->password_reset_hash );
	}

	public function setPassword_reset_hash( $value = null)
	{
		parent::storeChange( Users::password_reset_hash, $value );
	}

	public function activation_hash()
	{
		return parent::changedValue( Users::activation_hash, $this->activation_hash );
	}

	public function setActivation_hash( $value = null)
	{
		parent::storeChange( Users::activation_hash, $value );
	}

	public function failed_logins()
	{
		return parent::changedValue( Users::failed_logins, $this->failed_logins );
	}

	public function setFailed_logins( $value = null)
	{
		parent::storeChange( Users::failed_logins, $value );
	}

	public function last_login_timestamp()
	{
		return parent::changedValue( Users::last_login_timestamp, $this->last_login_timestamp );
	}

	public function setLast_login_timestamp( $value = null)
	{
		parent::storeChange( Users::last_login_timestamp, $value );
	}

	public function last_failed_login()
	{
		return parent::changedValue( Users::last_failed_login, $this->last_failed_login );
	}

	public function setLast_failed_login( $value = null)
	{
		parent::storeChange( Users::last_failed_login, $value );
	}

	public function password_reset_timestamp()
	{
		return parent::changedValue( Users::password_reset_timestamp, $this->password_reset_timestamp );
	}

	public function setPassword_reset_timestamp( $value = null)
	{
		parent::storeChange( Users::password_reset_timestamp, $value );
	}


}

?>
