<?php
namespace model;

use \DataObject as DataObject;
use \Model as Model;

use model\Network as Network;
use model\Users as Users;

use db\Qualifier as Qualifier;

class User_Network extends Model
{
	const TABLE =		'user_network';
	const id =			'id';
	const user_id =		'user_id';
	const network_id =	'network_id';

	public function tableName() { return User_Network::TABLE; }
	public function tablePK() { return User_Network::id; }
	public function sortOrder() { return array(User_Network::user_id, User_Network::network_id); }

	public function allColumnNames()
	{
		return array(User_Network::id, User_Network::user_id, User_Network::network_id);
	}

	public function joinForUserAndNetwork($user, $network)
	{
		if (isset($user, $user->id, $network, $network->id)) {
			$join = Qualifier::AndQualifier(
				Qualifier::FK( User_Network::user_id, $user ),
				Qualifier::FK( User_Network::network_id, $network )
			);
			return $this->singleObject( $join );
		}

		return false;
	}

	public function allForUser(model\UsersDBO $obj)
	{
		return $this->allObjectsForFK(User_Network::user_id, $obj);
	}

	public function allForIP($ipAddress)
	{
		$net_model = Model::Named("Network");
		$network = $net_model->networkForAddress($ipAddress);
		if ( $network == false) {
			return false;
		}
		return $this->allObjectsForFK(User_Network::network_id, $network);
	}

	public function createForIP($user, $ipAddress)
	{
		$net_model = Model::Named("Network");
		$network = $net_model->networkForAddress($ipAddress);
		if ( $network == false ) {
			$network = $net_model->create($ipAddress);
		}
		return $this->create($user, $network);
	}

	public function create($user, $network)
	{
		if (isset($user, $user->id, $network, $network->id)) {
			$join = $this->joinForUserAndNetwork($user, $network);
			if ($join == false) {
				$join = $this->createObject(array(
					User_Network::network_id => $network->id,
					User_Network::user_id => $user->id
					)
				);
			}

			return $join;
		}

		return false;
	}

	public function deleteObject(\DataObject $object = null)
	{
		if ( $object instanceof model\User_NetworkDBO )
		{
			return parent::deleteObject($object );
		}

		return false;
	}

	public function deleteAllForUser($obj)
	{
		$success = true;
		if ( $obj != false )
		{
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
}

