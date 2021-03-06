<?php

namespace model\network;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\network\NetworkDBO as NetworkDBO;

/* import related objects */
use \model\network\User_Network as User_Network;
use \model\network\User_NetworkDBO as User_NetworkDBO;

/** Generated class, do not edit.
 */
abstract class _Network extends Model
{
	const TABLE = 'network';

	// attribute keys
	const id = 'id';
	const ip_address = 'ip_address';
	const ip_hash = 'ip_hash';
	const created = 'created';
	const disable = 'disable';

	// relationship keys
	const user_network = 'user_network';

	public function modelName()
	{
		return "Network";
	}

	public function dboName()
	{
		return '\model\network\NetworkDBO';
	}

	public function tableName() { return Network::TABLE; }
	public function tablePK() { return Network::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Network::ip_hash)
		);
	}

	public function allColumnNames()
	{
		return array(
			Network::id,
			Network::ip_address,
			Network::ip_hash,
			Network::created,
			Network::disable
		);
	}

	public function allAttributes()
	{
		return array(
			Network::ip_address,
			Network::ip_hash,
			Network::created,
			Network::disable
		);
	}

	public function allForeignKeys()
	{
		return array();
	}

	public function allRelationshipNames()
	{
		return array(
			Network::user_network
		);
	}

	public function attributes()
	{
		return array(
			Network::ip_address => array('length' => 256,'type' => 'TEXT'),
			Network::ip_hash => array('length' => 256,'type' => 'TEXT'),
			Network::created => array('type' => 'DATE'),
			Network::disable => array('type' => 'BOOLEAN')
		);
	}

	public function relationships()
	{
		return array(
			Network::user_network => array(
				'destination' => 'User_Network',
				'ownsDestination' => false,
				'isMandatory' => false,
				'isToMany' => true,
				'joins' => array( 'id' => 'network_id')
			)
		);
	}

	public function searchQualifiers( array $query )
	{
		$qualifiers = array();
		if ( is_array($query) ) {
			foreach( $query as $attr => $value ) {
				switch ($attr) {
			// Network::id == INTEGER

			// Network::ip_address == TEXT
				case Network::ip_address:
					if (strlen($value) > 0) {
						$qualifiers[Network::ip_address] = Qualifier::Equals( Network::ip_address, $value );
					}
					break;

			// Network::ip_hash == TEXT
				case Network::ip_hash:
					if (strlen($value) > 0) {
						$qualifiers[Network::ip_hash] = Qualifier::Equals( Network::ip_hash, $value );
					}
					break;

			// Network::created == DATE

			// Network::disable == BOOLEAN
				case Network::disable:
					$v = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
					if (is_null($v) == false) {
						$qualifiers[Network::disable] = Qualifier::Equals( Network::disable, $v );
					}
					break;

				default:
					/* no type specified for Network::disable */
					break;
				}
			}
		}
		return $qualifiers;
	}

	/**
	 *	Simple fetches
	 */

	public function objectForIp_address($value)
	{
		return $this->singleObjectForKeyValue(Network::ip_address, $value);
	}


	public function objectForIp_hash($value)
	{
		return $this->singleObjectForKeyValue(Network::ip_hash, $value);
	}





	/**
	 * Simple relationship fetches
	 */

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "user_network":
					return array( Network::id, "network_id"  );
					break;
				default:
					break;
			}
		}
		return parent::joinAttributes( $joinModel );
	}

	/**
	 *	Create/Update functions
	 */
	public function createObject( array $values = array() )
	{
		if ( isset($values) ) {

			// default values for attributes
			if ( isset($values['ip_address']) == false ) {
				$default_ip_address = $this->attributeDefaultValue( null, null, Network::ip_address);
				if ( is_null( $default_ip_address ) == false ) {
					$values['ip_address'] = $default_ip_address;
				}
			}
			if ( isset($values['ip_hash']) == false ) {
				$default_ip_hash = $this->attributeDefaultValue( null, null, Network::ip_hash);
				if ( is_null( $default_ip_hash ) == false ) {
					$values['ip_hash'] = $default_ip_hash;
				}
			}
			if ( isset($values['created']) == false ) {
				$default_created = $this->attributeDefaultValue( null, null, Network::created);
				if ( is_null( $default_created ) == false ) {
					$values['created'] = $default_created;
				}
			}
			if ( isset($values['disable']) == false ) {
				$default_disable = $this->attributeDefaultValue( null, null, Network::disable);
				if ( is_null( $default_disable ) == false ) {
					$values['disable'] = $default_disable;
				}
			}

			// default conversion for relationships
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Network ) {
		}

		return parent::updateObject($object, $values);
	}

	/**
	 *	Delete functions
	 */
	public function deleteObject( DataObject $object = null)
	{
		if ( $object instanceof NetworkDBO )
		{
			// does not own user_network User_Network
			return parent::deleteObject($object);
		}

		return false;
	}


	/**
	 * Named fetches
	 */

	/**
	 * Attribute editing
	 */
	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Network::ip_address
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesMap() {
		return array(
			Network::ip_address => Model::TEXT_TYPE,
			Network::ip_hash => Model::TEXT_TYPE,
			Network::created => Model::DATE_TYPE,
			Network::disable => Model::FLAG_TYPE
		);
	}

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

	/*
	 * return the foreign key object
	 */
	public function attributeObject($object = null, $type = null, $attr, $value)
	{
		$fkObject = false;
		if ( isset( $attr ) ) {
			switch ( $attr ) {
				default:
					break;
			}
		}
		return $fkObject;
	}

	/**
	 * Validation
	 */
	function validate_ip_address($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Network::ip_address,
				"FIELD_EMPTY"
			);
		}

		// make sure Ip_address is unique
		$existing = $this->objectForIp_address($value);
		if ( $existing != false && ( is_null($object) || $existing->id != $object->id)) {
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
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// make sure Ip_hash is unique
		$existing = $this->objectForIp_hash($value);
		if ( $existing != false && ( is_null($object) || $existing->id != $object->id)) {
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
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// created date is not changeable
		if ( isset($object, $object->created) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Network::created,
				"IMMUTABLE"
			);
		}
		return null;
	}
	function validate_disable($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false  ) {
			return null;
		}

		// boolean

		// Returns TRUE for "1", "true", "on" and "yes"
		// Returns FALSE for "0", "false", "off" and "no"
		// Returns NULL otherwise.
		$v = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
		if (is_null($v)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Network::disable,
				"FILTER_VALIDATE_BOOLEAN"
			);
		}
		return null;
	}
}

?>
