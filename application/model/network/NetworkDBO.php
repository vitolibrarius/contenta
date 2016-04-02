<?php

namespace model\network;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use model\network\Network as Network;

class NetworkDBO extends DataObject
{
	public $ip_address;
	public $ip_hash;
	public $created;
	public $disable;


	public function formattedDateTimeCreated() { return $this->formattedDate( Network::created, "M d, Y H:i" ); }
	public function formattedDateCreated() {return $this->formattedDate( Network::created, "M d, Y" ); }

	public function isDisable() {
		return (isset($this->disable) && $this->disable == 1);
	}


	// to-many relationship
	public function user_network()
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('User_Network');
			return $model->allObjectsForKeyValue( User_Network::network_id, $this->id);
		}

		return false;
	}

}

?>
