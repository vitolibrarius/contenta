<?php

namespace model\network;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\network\User_NetworkDBO as User_NetworkDBO;

/* import related objects */
use \model\user\Users as Users;
use \model\user\UsersDBO as UsersDBO;
use \model\network\Network as Network;
use \model\network\NetworkDBO as NetworkDBO;

/** Sample Creation script */
		/** USER_NETWORK */
/*
		$sql = "CREATE TABLE IF NOT EXISTS user_network ( "
			. User_Network::id . " INTEGER PRIMARY KEY, "
			. User_Network::user_id . " INTEGER, "
			. User_Network::network_id . " INTEGER, "
			. "FOREIGN KEY (". User_Network::user_id .") REFERENCES " . Users::TABLE . "(" . Users::id . "),"
			. "FOREIGN KEY (". User_Network::network_id .") REFERENCES " . Network::TABLE . "(" . Network::id . ")"
		. ")";
		$this->sqlite_execute( "user_network", $sql, "Create table user_network" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS user_network_user_idnetwork_id on user_network (user_id,network_id)';
		$this->sqlite_execute( "user_network", $sql, "Index on user_network (user_id,network_id)" );
*/
abstract class _User_Network extends Model
{
	const TABLE = 'user_network';
	const id = 'id';
	const user_id = 'user_id';
	const network_id = 'network_id';

	public function tableName() { return User_Network::TABLE; }
	public function tablePK() { return User_Network::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => User_Network::user_id)
		);
	}

	public function allColumnNames()
	{
		return array(
			User_Network::id,
			User_Network::user_id,
			User_Network::network_id
		);
	}

	/**
	 *	Simple fetches
	 */




	public function allForUser($obj)
	{
		return $this->allObjectsForFK(User_Network::user_id, $obj, $this->sortOrder(), 50);
	}
	public function allForNetwork($obj)
	{
		return $this->allObjectsForFK(User_Network::network_id, $obj, $this->sortOrder(), 50);
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "users":
					return array( User_Network::user_id, "id"  );
					break;
				case "network":
					return array( User_Network::network_id, "id"  );
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
			if ( isset($values['user']) ) {
				$local_user = $values['user'];
				if ( $local_user instanceof UsersDBO) {
					$values[User_Network::user_id] = $local_user->id;
				}
				else if ( is_integer( $local_user) ) {
					$params[User_Network::user_id] = $local_user;
				}
			}
			if ( isset($values['network']) ) {
				$local_network = $values['network'];
				if ( $local_network instanceof NetworkDBO) {
					$values[User_Network::network_id] = $local_network->id;
				}
				else if ( is_integer( $local_network) ) {
					$params[User_Network::network_id] = $local_network;
				}
			}
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof User_Network ) {
			if ( isset($values['user']) ) {
				$local_user = $values['user'];
				if ( $local_user instanceof UsersDBO) {
					$values[User_Network::user_id] = $local_user->id;
				}
				else if ( is_integer( $local_user) ) {
					$params[User_Network::user_id] = $values['user'];
				}
			}
			if ( isset($values['network']) ) {
				$local_network = $values['network'];
				if ( $local_network instanceof NetworkDBO) {
					$values[User_Network::network_id] = $local_network->id;
				}
				else if ( is_integer( $local_network) ) {
					$params[User_Network::network_id] = $values['network'];
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
		if ( $object instanceof User_NetworkDBO )
		{
			// does not own Users
			// does not own Network
			return parent::deleteObject($object);
		}

		return false;
	}

	public function deleteAllForUser(UsersDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForUser($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForUser($obj);
			}
		}
		return $success;
	}
	public function deleteAllForNetwork(NetworkDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForNetwork($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForNetwork($obj);
			}
		}
		return $success;
	}

	/**
	 *	Named fetches
	 */

	/** Set attributes */
	public function setUser_id( User_NetworkDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(User_Network::user_id => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setNetwork_id( User_NetworkDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(User_Network::network_id => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}


	/** Validation */
	function validate_user_id($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				User_Network::user_id,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
	function validate_network_id($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				User_Network::network_id,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
}

?>
