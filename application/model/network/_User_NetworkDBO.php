<?php

namespace model\network;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\network\User_Network as User_Network;

/* import related objects */
use \model\user\Users as Users;
use \model\user\UsersDBO as UsersDBO;
use \model\network\Network as Network;
use \model\network\NetworkDBO as NetworkDBO;

abstract class _User_NetworkDBO extends DataObject
{
	public $user_id;
	public $network_id;



	// to-one relationship
	public function user()
	{
		if ( isset( $this->user_id ) ) {
			$model = Model::Named('Users');
			return $model->objectForId($this->user_id);
		}
		return false;
	}

	// to-one relationship
	public function network()
	{
		if ( isset( $this->network_id ) ) {
			$model = Model::Named('Network');
			return $model->objectForId($this->network_id);
		}
		return false;
	}


	/** Attributes */
	public function user_id()
	{
		return parent::changedValue( User_Network::user_id, $this->user_id );
	}

	public function setUser_id( $value = null)
	{
		parent::storeChange( User_Network::user_id, $value );
	}

	public function network_id()
	{
		return parent::changedValue( User_Network::network_id, $this->network_id );
	}

	public function setNetwork_id( $value = null)
	{
		parent::storeChange( User_Network::network_id, $value );
	}


}

?>