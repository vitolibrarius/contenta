<?php

namespace model\network;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \model\network\Network as Network;

/* import related objects */
use \model\network\User_Network as User_Network;
use \model\network\User_NetworkDBO as User_NetworkDBO;

abstract class _NetworkDBO extends DataObject
{
	public $ip_address;
	public $ip_hash;
	public $created;
	public $disable;


	public function pkValue()
	{
		return $this->{Network::id};
	}

	public function modelName()
	{
		return "Network";
	}

	public function dboName()
	{
		return "\model\network\NetworkDBO";
	}

	public function formattedDateTime_created() { return $this->formattedDate( Network::created, "M d, Y H:i" ); }
	public function formattedDate_created() {return $this->formattedDate( Network::created, "M d, Y" ); }

	public function isDisable() {
		return (isset($this->disable) && $this->disable == Model::TERTIARY_TRUE);
	}


	// to-many relationship
	public function user_network($limit = SQL::SQL_DEFAULT_LIMIT)
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('User_Network');
			return $model->allObjectsForKeyValue(
				User_Network::network_id,
				$this->id,
				null,
				$limit
			);
		}

		return false;
	}


	/** Attributes */
	public function ip_address()
	{
		return parent::changedValue( Network::ip_address, $this->ip_address );
	}

	public function setIp_address( $value = null)
	{
		parent::storeChange( Network::ip_address, $value );
	}

	public function ip_hash()
	{
		return parent::changedValue( Network::ip_hash, $this->ip_hash );
	}

	public function setIp_hash( $value = null)
	{
		parent::storeChange( Network::ip_hash, $value );
	}

	public function disable()
	{
		return parent::changedValue( Network::disable, $this->disable );
	}

	public function setDisable( $value = null)
	{
		parent::storeChange( Network::disable, $value );
	}


}

?>
