<?php

namespace model\network;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \model\network\NetworkDBO as NetworkDBO;

/* import related objects */
use \model\network\User_Network as User_Network;
use \model\network\User_NetworkDBO as User_NetworkDBO;

/** Sample Creation script */
		/** NETWORK */
/*
		$sql = "CREATE TABLE IF NOT EXISTS network ( "
			. Network::id . " INTEGER PRIMARY KEY, "
			. Network::ip_address . " TEXT, "
			. Network::ip_hash . " TEXT, "
			. Network::created . " INTEGER, "
			. Network::disable . " INTEGER "
		. ")";
		$this->sqlite_execute( "network", $sql, "Create table network" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS network_ip_address on network (ip_address)';
		$this->sqlite_execute( "network", $sql, "Index on network (ip_address)" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS network_ip_hash on network (ip_hash)';
		$this->sqlite_execute( "network", $sql, "Index on network (ip_hash)" );
*/
abstract class _Network extends Model
{
	const TABLE = 'network';
	const id = 'id';
	const ip_address = 'ip_address';
	const ip_hash = 'ip_hash';
	const created = 'created';
	const disable = 'disable';

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
			if ( isset($values['user_network']) ) {
				$local_user_network = $values['user_network'];
				if ( $local_user_network instanceof User_NetworkDBO) {
					$values[Network::id] = $local_user_network->network_id;
				}
				else if ( is_integer( $local_user_network) ) {
					$params[Network::id] = $local_user_network;
				}
			}
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Network ) {
			if ( isset($values['user_network']) ) {
				$local_user_network = $values['user_network'];
				if ( $local_user_network instanceof User_NetworkDBO) {
					$values[Network::id] = $local_user_network->network_id;
				}
				else if ( is_integer( $local_user_network) ) {
					$params[Network::id] = $values['user_network'];
				}
			}
		}

		return parent::updateObject($object, $values);
	}

	/**
	 *	Delete functions
	 */
	public function deleteObject( DataObject $object = null)
	{
		if ( $object instanceof Network )
		{
			// does not own User_Network
			return parent::deleteObject($object);
		}

		return false;
	}


	/**
	 *	Named fetches
	 */

	/** Set attributes */
	public function setIp_address( NetworkDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Network::ip_address => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setIp_hash( NetworkDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Network::ip_hash => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setCreated( NetworkDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Network::created => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setDisable( NetworkDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Network::disable => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}


	/** Validation */
	function validate_ip_address($object = null, $value)
	{
		$value = trim($value);
		if (empty($value)) {
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
		$value = trim($value);
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
		if ( is_null($value) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Network::disable,
				"FIELD_EMPTY"
			);
		}

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
