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
		return null;
	}
	function validate_network_id($object = null, $value)
	{
		return null;
	}
}

?>
