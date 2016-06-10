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
	public function createObject(array $values = array())
	{
		if ( isset($values) ) {
			if ( isset($values[Network::ip_address]) ) {
				$ip_address = $values[Network::ip_address];
				$object = $this->objectForIp_address($ip_address);
				if ( $object != false) {
					return array($object, null);
				}

				$ip_hash = ipToHex($ip_address);
				if ( $ip_hash == false ) {
					$ip_hash = 'Invalid IP address ' . $ip_address;
				}
				$values[Network::ip_hash] = $ip_hash;
			}

			if ( isset($values[Network::disable]) ) {
				$values[Network::disable] = boolValue($values[Network::disable], false);
			}
			else {
				$values[Network::disable] = false;
			}
		}

		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = NULL, array $values = array()) {
		if (isset($object) && $object instanceof NetworkDBO ) {
			if ( isset($values[Network::disable]) == true) {
				$values[Network::disable] = boolValue($values[Network::disable], false);
			}
		}

		return parent::updateObject($object, $values);
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
		if ( is_null($object) || $attr == Network::disable) {
			return true;
		}
		return false;
	}

	/*
	public function attributeRestrictionMessage($object = null, $type = null, $attr)	{ return null; }
	public function attributePlaceholder($object = null, $type = null, $attr)	{ return null; }
	*/

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) === false || is_null($object) == true) {
			switch ($attr) {
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
		if ( $object instanceof NetworkDBO ) {
			if ( $object->ip_address != $value ) {
				return Localized::ModelValidation($this->tableName(), Network::ip_address, "IMMUTATABLE");
			}
		}
		return parent::validate_ip_address($object, $value);
	}

	function validate_ip_hash($object = null, $value)
	{
		if ( $object instanceof NetworkDBO ) {
			if ( $object->ip_hash != $value ) {
				return Localized::ModelValidation($this->tableName(), Network::ip_hash, "IMMUTATABLE");
			}
		}
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
