<?php

namespace model\network;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Validation as Validation;

use \model\network\User_NetworkDBO as User_NetworkDBO;

/* import related objects */
use \model\users\Users as Users;
use \model\users\UsersDBO as UsersDBO;
use \model\network\Network as Network;
use \model\network\NetworkDBO as NetworkDBO;

class User_Network extends _User_Network
{
	/**
	 *	Create/Update functions
	 */
	public function create( $user, $network)
	{
		return $this->base_create(
			$user,
			$network
		);
	}

	public function update( User_NetworkDBO $obj,
		$user, $network)
	{
		if ( isset( $obj ) && is_null($obj) == false ) {
			return $this->base_update(
				$obj,
				$user,
				$network
			);
		}
		return $obj;
	}


	public function attributesFor($object = null, $type = null) {
		return array(
			User_Network::user_id => Model::INT_TYPE,
			User_Network::network_id => Model::INT_TYPE
		);
	}


	public function attributeIsEditable($object = null, $type = null, $attr)
	{
		// add customization here
		return parent::attributeIsEditable($object, $type, $attr);
	}

	/*
	public function attributeRestrictionMessage($object = null, $type = null, $attr)	{ return null; }
	public function attributePlaceholder($object = null, $type = null, $attr)	{ return null; }
	*/

	public function attributeEditPattern($object = null, $type = null, $attr)
	{
		return null;
	}

	public function attributeOptions($object = null, $type = null, $attr)
	{
		if ( $attr = User_Network::user_id ) {
			$model = Model::Named('Users');
			return $model->allObjects();
		}
		if ( $attr = User_Network::network_id ) {
			$model = Model::Named('Network');
			return $model->allObjects();
		}
		return null;
	}

	/** Validation */
	function validate_user_id($object = null, $value)
	{
		if (isset($object->user_id) == false && empty($value) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				User_Network::user_id,
				"FIELD_EMPTY"
			);
		}
		return null;
	}
	function validate_network_id($object = null, $value)
	{
		if (isset($object->network_id) == false && empty($value) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				User_Network::network_id,
				"FIELD_EMPTY"
			);
		}
		return null;
	}
}

?>
