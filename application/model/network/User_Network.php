<?php

namespace model\network;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \model\network\User_NetworkDBO as User_NetworkDBO;

/* import related objects */
use \model\user\Users as Users;
use \model\user\UsersDBO as UsersDBO;
use \model\network\Network as Network;
use \model\network\NetworkDBO as NetworkDBO;

class User_Network extends _User_Network
{
	/**
	 *	Create/Update functions
	 */
	public function createForIp_address($user, $ipAddress)
	{
		$net_model = Model::Named("Network");
		$network = $net_model->objectForIp_address($ipAddress);
		if ( $network == false ) {
			list($network, $errors) = $net_model->createObject( array( "ip_address" => $ipAddress, "disable" => false));
		}
		return $this->createObject( array( "user" => $user, "network" => $network) );
	}

	public function createObject( array $values = array())
	{
		if ( isset($values) ) {
			// massage values as necessary
		}

		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof User_NetworkDBO ) {
			// massage values as necessary
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		$attrFor = array(
			User_Network::user_id,
			User_Network::network_id
		);
		return array_intersect_key($this->attributesMap(),array_flip($attrFor));
	}


	/*
	public function attributeIsEditable($object = null, $type = null, $attr)
	{
		// add customization here
		return parent::attributeIsEditable($object, $type, $attr);
	}
	*/

	/*
	public function attributeRestrictionMessage($object = null, $type = null, $attr)	{ return null; }
	public function attributePlaceholder($object = null, $type = null, $attr)	{ return null; }
	*/

	/*
	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		return parent::attributeDefaultValue($object, $type, $attr);
	}
	*/

	/*
	public function attributeEditPattern($object = null, $type = null, $attr)
	{
		return null;
	}
	*/

	public function attributeOptions($object = null, $type = null, $attr)
	{
		return null;
	}

	/** Validation */
/*
	function validate_user_id($object = null, $value)
	{
		return parent::validate_user_id($object, $value);
	}
*/

/*
	function validate_network_id($object = null, $value)
	{
		return parent::validate_network_id($object, $value);
	}
*/

}

?>
