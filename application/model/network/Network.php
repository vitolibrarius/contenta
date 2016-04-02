<?php

namespace model\network;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use model\network\NetworkDBO as NetworkDBO;

/** Sample Creation script */
		/** NETWORK
		$sql = "CREATE TABLE IF NOT EXISTS network ( "
			. model\network\Network::id . " INTEGER PRIMARY KEY, "
			. model\network\Network::ip_address . " TEXT, "
			. model\network\Network::ip_hash . " TEXT, "
			. model\network\Network::created . " INTEGER, "
			. model\network\Network::disable . " INTEGER, "
			. ")";
		$this->sqlite_execute( "network", $sql, "Create table network" );

*/
class Network extends Model
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

	/** * * * * * * * * *
		Basic search functions
	 */
	public function allForIp_address($value)
	{
		return $this->allObjectsForKeyValue(Network::ip_address, $value);
	}

	public function allForIp_hash($value)
	{
		return $this->allObjectsForKeyValue(Network::ip_hash, $value);
	}



	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				default:
					break;
			}
		}
		return parent::joinAttributes( $joinModel );
	}

	public function create( $ip_address, $ip_hash, $disable)
	{
		$obj = false;
		if ( isset($ip_address) ) {
			$params = array(
				Network::ip_address => (isset($ip_address) ? $ip_address : null),
				Network::ip_hash => (isset($ip_hash) ? $ip_hash : ipToHex($ip_address)),
				Network::created => time(),
				Network::disable => (isset($disable) ? $disable : Model::TERTIARY_FALSE),
			);


			list( $obj, $errorList ) = $this->createObject($params);
			if ( is_array($errorList) ) {
				return $errorList;
			}
		}
		return $obj;
	}

	public function deleteObject( DataObject $object = null)
	{
		if ( $object instanceof Network )
		{
			// does not own User_Network
			return parent::deleteObject($object);
		}

		return false;
	}

}

?>
