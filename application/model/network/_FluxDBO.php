<?php

namespace model\network;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \model\network\Flux as Flux;

/* import related objects */
use \model\network\Endpoint as Endpoint;
use \model\network\EndpointDBO as EndpointDBO;

abstract class _FluxDBO extends DataObject
{
	public $created;
	public $name;
	public $flux_hash;
	public $flux_error;
	public $src_endpoint;
	public $src_guid;
	public $src_url;
	public $src_status;
	public $src_pub_date;
	public $dest_endpoint;
	public $dest_guid;
	public $dest_status;
	public $dest_submission;

	public function displayName()
	{
		return $this->name;
	}

	public function pkValue()
	{
		return $this->{Flux::id};
	}

	public function modelName()
	{
		return "Flux";
	}

	public function dboName()
	{
		return "\model\network\FluxDBO";
	}

	public function formattedDateTime_created() { return $this->formattedDate( Flux::created, "M d, Y H:i" ); }
	public function formattedDate_created() {return $this->formattedDate( Flux::created, "M d, Y" ); }

	public function isFlux_error() {
		return (isset($this->flux_error) && $this->flux_error == Model::TERTIARY_TRUE);
	}

	public function formattedDateTime_src_pub_date() { return $this->formattedDate( Flux::src_pub_date, "M d, Y H:i" ); }
	public function formattedDate_src_pub_date() {return $this->formattedDate( Flux::src_pub_date, "M d, Y" ); }

	public function formattedDateTime_dest_submission() { return $this->formattedDate( Flux::dest_submission, "M d, Y H:i" ); }
	public function formattedDate_dest_submission() {return $this->formattedDate( Flux::dest_submission, "M d, Y" ); }


	// to-one relationship
	public function source_endpoint()
	{
		if ( isset( $this->src_endpoint ) ) {
			$model = Model::Named('Endpoint');
			return $model->objectForId($this->src_endpoint);
		}
		return false;
	}

	public function setSource_endpoint(EndpointDBO $obj = null)
	{
		if ( isset($obj, $obj->id) && (isset($this->src_endpoint) == false || $obj->id != $this->src_endpoint) ) {
			parent::storeChange( Flux::src_endpoint, $obj->id );
			$this->saveChanges();
		}
	}

	// to-one relationship
	public function destination_endpoint()
	{
		if ( isset( $this->dest_endpoint ) ) {
			$model = Model::Named('Endpoint');
			return $model->objectForId($this->dest_endpoint);
		}
		return false;
	}

	public function setDestination_endpoint(EndpointDBO $obj = null)
	{
		if ( isset($obj, $obj->id) && (isset($this->dest_endpoint) == false || $obj->id != $this->dest_endpoint) ) {
			parent::storeChange( Flux::dest_endpoint, $obj->id );
			$this->saveChanges();
		}
	}


	/** Attributes */
	public function name()
	{
		return parent::changedValue( Flux::name, $this->name );
	}

	public function setName( $value = null)
	{
		parent::storeChange( Flux::name, $value );
	}

	public function flux_hash()
	{
		return parent::changedValue( Flux::flux_hash, $this->flux_hash );
	}

	public function setFlux_hash( $value = null)
	{
		parent::storeChange( Flux::flux_hash, $value );
	}

	public function flux_error()
	{
		return parent::changedValue( Flux::flux_error, $this->flux_error );
	}

	public function setFlux_error( $value = null)
	{
		parent::storeChange( Flux::flux_error, $value );
	}

	public function src_guid()
	{
		return parent::changedValue( Flux::src_guid, $this->src_guid );
	}

	public function setSrc_guid( $value = null)
	{
		parent::storeChange( Flux::src_guid, $value );
	}

	public function src_url()
	{
		return parent::changedValue( Flux::src_url, $this->src_url );
	}

	public function setSrc_url( $value = null)
	{
		parent::storeChange( Flux::src_url, $value );
	}

	public function src_status()
	{
		return parent::changedValue( Flux::src_status, $this->src_status );
	}

	public function setSrc_status( $value = null)
	{
		parent::storeChange( Flux::src_status, $value );
	}

	public function src_pub_date()
	{
		return parent::changedValue( Flux::src_pub_date, $this->src_pub_date );
	}

	public function setSrc_pub_date( $value = null)
	{
		parent::storeChange( Flux::src_pub_date, $value );
	}

	public function dest_guid()
	{
		return parent::changedValue( Flux::dest_guid, $this->dest_guid );
	}

	public function setDest_guid( $value = null)
	{
		parent::storeChange( Flux::dest_guid, $value );
	}

	public function dest_status()
	{
		return parent::changedValue( Flux::dest_status, $this->dest_status );
	}

	public function setDest_status( $value = null)
	{
		parent::storeChange( Flux::dest_status, $value );
	}

	public function dest_submission()
	{
		return parent::changedValue( Flux::dest_submission, $this->dest_submission );
	}

	public function setDest_submission( $value = null)
	{
		parent::storeChange( Flux::dest_submission, $value );
	}


}

?>
