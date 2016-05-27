<?php

namespace model\network;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \model\network\NetworkDBO as NetworkDBO;

/* import related objects */
use \model\network\User_Network as User_Network;
use \model\network\User_NetworkDBO as User_NetworkDBO;

class Network extends _Network
{
	/**
	 *	Create/Update functions
	 */
	public function create( $ip_address, $ip_hash, $disable)
	{
		return $this->base_create(
			$ip_address,
			$ip_hash,
			$disable
		);
	}

	public function update( NetworkDBO $obj,
		$ip_address, $ip_hash, $disable)
	{
		if ( isset( $obj ) && is_null($obj) === false ) {
			return $this->base_update(
				$obj,
				$ip_address,
				$ip_hash,
				$disable
			);
		}
		return $obj;
	}


	public function attributesFor($object = null, $type = null) {
		return array(
			Network::ip_address => Model::TEXT_TYPE,
			Network::ip_hash => Model::TEXT_TYPE,
			Network::created => Model::DATE_TYPE,
			Network::disable => Model::FLAG_TYPE
		);
	}

	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Network::ip_address
			);
		}
		return parent::attributesMandatory($object);
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

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) === false || is_null($object) == true) {
			switch ($attr) {
				case Network::ip_hash:
					return ipToHex($ip_address);
				case Network::disable:
					return Model::TERTIARY_FALSE;
			}
		}
		return parent::attributeDefaultValue($object, $type, $attr);
	}

	public function attributeEditPattern($object = null, $type = null, $attr)
	{
		return null;
	}

	public function attributeOptions($object = null, $type = null, $attr)
	{
		return null;
	}

	/** Validation */
	function validate_ip_address($object = null, $value)
	{
		return parent::validate_ip_address($object, $value);
	}

	function validate_ip_hash($object = null, $value)
	{
		return parent::validate_ip_hash($object, $value);
	}

	function validate_created($object = null, $value)
	{
		return parent::validate_created($object, $value);
	}

	function validate_disable($object = null, $value)
	{
		return parent::validate_disable($object, $value);
	}

}

?>
