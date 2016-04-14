<?php

namespace model\network;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \model\network\User_NetworkDBO as User_NetworkDBO;

/* import related objects */
use \model\users\Users as Users;
use \model\users\UsersDBO as UsersDBO;
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
	public function base_create( $user, $network)
	{
		$obj = false;
		if ( isset($user, $network) ) {
			$params = array(
			);

			if ( isset($user) ) {
				if ( $user instanceof UsersDBO) {
					$params[User_Network::user_id] = $user->id;
				}
				else if (  is_integer($user) ) {
					$params[User_Network::user_id] = $user;
				}
			}
			if ( isset($network) ) {
				if ( $network instanceof NetworkDBO) {
					$params[User_Network::network_id] = $network->id;
				}
				else if (  is_integer($network) ) {
					$params[User_Network::network_id] = $network;
				}
			}

			list( $obj, $errorList ) = $this->createObject($params);
			if ( is_array($errorList) ) {
				return $errorList;
			}
		}
		return $obj;
	}

	public function base_update( User_NetworkDBO $obj,
		$user, $network)
	{
		if ( isset( $obj ) && is_null($obj) == false ) {
			$updates = array();


			if ( isset($user) ) {
				if ( $user instanceof UsersDBO) {
					$updates[User_Network::user_id] = $user->id;
				}
				else if (  is_integer($user) ) {
					$updates[User_Network::user_id] = $user;
				}
			}
			if ( isset($network) ) {
				if ( $network instanceof NetworkDBO) {
					$updates[User_Network::network_id] = $network->id;
				}
				else if (  is_integer($network) ) {
					$updates[User_Network::network_id] = $network;
				}
			}

			if ( count($updates) > 0 ) {
				list($obj, $errorList) = $this->updateObject( $obj, $updates );
				if ( is_array($errorList) ) {
					return $errorList;
				}
			}
		}
		return $obj;
	}

	/**
	 *	Delete functions
	 */
	public function deleteObject( DataObject $object = null)
	{
		if ( $object instanceof User_Network )
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
			foreach ($array as $key => $value) {
				if ($this->deleteObject($value) == false) {
					$success = false;
					break;
				}
			}
		}
		return $success;
	}
	public function deleteAllForNetwork(NetworkDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForNetwork($obj);
			foreach ($array as $key => $value) {
				if ($this->deleteObject($value) == false) {
					$success = false;
					break;
				}
			}
		}
		return $success;
	}

	/**
	 *	Named fetches
	 */
}

?>
