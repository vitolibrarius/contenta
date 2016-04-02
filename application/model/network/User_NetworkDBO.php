<?php

namespace model\network;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use model\network\User_Network as User_Network;

class User_NetworkDBO extends DataObject
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

}

?>
