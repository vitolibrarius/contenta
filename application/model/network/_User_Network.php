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

/** Generated class, do not edit.
 */
abstract class _User_Network extends Model
{
	const TABLE = 'user_network';

	// attribute keys
	const id = 'id';
	const user_id = 'user_id';
	const network_id = 'network_id';

	// relationship keys
	const user = 'user';
	const network = 'network';

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

	public function allAttributes()
	{
		return array(
		);
	}

	public function allForeignKeys()
	{
		return array(User_Network::user_id,
			User_Network::network_id);
	}

	public function allRelationshipNames()
	{
		return array(
			User_Network::user,
			User_Network::network
		);
	}

	/**
	 *	Simple fetches
	 */




	/**
	 * Simple relationship fetches
	 */
	public function allForUser($obj)
	{
		return $this->allObjectsForFK(User_Network::user_id, $obj, $this->sortOrder(), 50);
	}

	public function countForUser($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( User_Network::user_id, $obj );
		}
		return false;
	}
	public function allForNetwork($obj)
	{
		return $this->allObjectsForFK(User_Network::network_id, $obj, $this->sortOrder(), 50);
	}

	public function countForNetwork($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( User_Network::network_id, $obj );
		}
		return false;
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

			// default values for attributes

			// default conversion for relationships
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
			// does not own user Users
			// does not own network Network
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
	 * Named fetches
	 */
	public function objectForUserAndNetwork(UsersDBO $user,NetworkDBO $network )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		$qualifiers[] = Qualifier::FK( 'user_id', $user);
		$qualifiers[] = Qualifier::FK( 'network_id', $network);

		if ( count($qualifiers) > 0 ) {
			$select->where( Qualifier::Combine( 'AND', $qualifiers ));
		}

		$result = $select->fetchAll();
		if ( is_array($result) ) {
			$result_size = count($result);
			if ( $result_size == 1 ) {
				return $result[0];
			}
			else if ($result_size > 1 ) {
				throw new \Exception( "objectForUserAndNetwork expected 1 result, but fetched " . count($result) );
			}
		}

		return false;
	}


	/**
	 * Attribute editing
	 */

	public function attributesMap() {
		return array(
			User_Network::user_id => Model::TO_ONE_TYPE,
			User_Network::network_id => Model::TO_ONE_TYPE
		);
	}

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) === false || is_null($object) == true) {
			switch ($attr) {
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
				case User_Network::user_id:
					$users_model = Model::Named('Users');
					$fkObject = $users_model->objectForId( $value );
					break;
				case User_Network::network_id:
					$network_model = Model::Named('Network');
					$fkObject = $network_model->objectForId( $value );
					break;
				default:
					break;
			}
		}
		return $fkObject;
	}

	/**
	 * Validation
	 */
	function validate_user_id($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
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
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
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
