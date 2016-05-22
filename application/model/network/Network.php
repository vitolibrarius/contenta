<?php

namespace model\network;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\network\NetworkDBO as NetworkDBO;

/* import related objects */
use \model\network\User_Network as User_Network;
use \model\network\User_NetworkDBO as User_NetworkDBO;

class Network extends _Network
{
	/**
	 *	Create/Update functions
	 */
	function create( $ip_address )
	{
		$object = $this->objectForIp_address($ip_address);
		if ( $object == false) {
			$ip_hash = ipToHex($ip_address);
			if ( $ip_hash == false ) {
				$ip_hash = 'Invalid IP address ' . $ip_address;
			}

			return $this->base_create(
				$ip_address,
				$ip_hash,
				false
			);
		}

		return $object;
	}

	public function update( NetworkDBO $obj,
		$ip_address, $ip_hash, $disable)
	{
		if ( isset( $obj ) && is_null($obj) == false ) {
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
		if (empty($value)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Network::ip_address,
				"FIELD_EMPTY"
			);
		}
		// make sure Ip_address is unique
		$existing = $this->objectForIp_address($value);
		if ( is_null($object) == false && $existing != false && $existing->id != $object->id) {
			return Localized::ModelValidation(
				$this->tableName(),
				Network::ip_address,
				"UNIQUE_FIELD_VALUE"
			);
		}
		return null;
	}
	function validate_ip_hash($object = null, $value)
	{
		// make sure Ip_hash is unique
		$existing = $this->objectForIp_hash($value);
		if ( is_null($object) == false && $existing != false && $existing->id != $object->id) {
			return Localized::ModelValidation(
				$this->tableName(),
				Network::ip_hash,
				"UNIQUE_FIELD_VALUE"
			);
		}
		return null;
	}
	function validate_created($object = null, $value)
	{
		return null;
	}
	function validate_disable($object = null, $value)
	{
		return null;
	}
}

?>
