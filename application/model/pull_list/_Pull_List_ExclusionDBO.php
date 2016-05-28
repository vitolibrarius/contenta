<?php

namespace model\pull_list;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\pull_list\Pull_List_Exclusion as Pull_List_Exclusion;

/* import related objects */
use \model\Endpoint as Endpoint;
use \model\EndpointDBO as EndpointDBO;

abstract class _Pull_List_ExclusionDBO extends DataObject
{
	public $pattern;
	public $type;
	public $created;
	public $endpoint_id;


	public function formattedDateTime_created() { return $this->formattedDate( Pull_List_Exclusion::created, "M d, Y H:i" ); }
	public function formattedDate_created() {return $this->formattedDate( Pull_List_Exclusion::created, "M d, Y" ); }


	// to-one relationship
	public function endpoint()
	{
		if ( isset( $this->endpoint_id ) ) {
			$model = Model::Named('Endpoint');
			return $model->objectForId($this->endpoint_id);
		}
		return false;
	}


	/** Attributes */
	public function pattern()
	{
		return parent::changedValue( Pull_List_Exclusion::pattern, $this->pattern );
	}

	public function setPattern( $value = null)
	{
		parent::storeChange( Pull_List_Exclusion::pattern, $value );
	}

	public function type()
	{
		return parent::changedValue( Pull_List_Exclusion::type, $this->type );
	}

	public function setType( $value = null)
	{
		parent::storeChange( Pull_List_Exclusion::type, $value );
	}

	public function created()
	{
		return parent::changedValue( Pull_List_Exclusion::created, $this->created );
	}

	public function setCreated( $value = null)
	{
		parent::storeChange( Pull_List_Exclusion::created, $value );
	}

	public function endpoint_id()
	{
		return parent::changedValue( Pull_List_Exclusion::endpoint_id, $this->endpoint_id );
	}

	public function setEndpoint_id( $value = null)
	{
		parent::storeChange( Pull_List_Exclusion::endpoint_id, $value );
	}


}

?>
