<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use model\Users as Users;
use model\Network as Network;

class User_NetworkDBO extends DataObject
{
	public $context;
	public $user_id;
	public $network_id;

	public function user() {
		if ( isset($this->user_id) ) {
			$model = Model::Named('Users');
			return $model->objectForId($this->user_id);
		}
		return false;
	}

	public function network() {
		if ( isset($this->network_id) ) {
			$model = Model::Named('Network');
			return $model->objectForId($this->network_id);
		}
		return false;
	}

	public function username() {
		$user = $this->user();
		return ($user == false) ? null : $user->name;
	}

	public function ipAddress() {
		$network = $this->network();
		return ($network == false) ? null : $network->ip_address;
	}

	public function ipHash() {
		$network = $this->network();
		return ($network == false) ? null : $network->ip_hash;
	}
}
