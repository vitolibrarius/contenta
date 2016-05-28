<?php

namespace model\pull_list;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\pull_list\Pull_List_Expansion as Pull_List_Expansion;

/* import related objects */
use \model\Endpoint as Endpoint;
use \model\EndpointDBO as EndpointDBO;

abstract class _Pull_List_ExpansionDBO extends DataObject
{
	public $pattern;
	public $replace;
	public $created;
	public $endpoint_id;


	public function formattedDateTime_created() { return $this->formattedDate( Pull_List_Expansion::created, "M d, Y H:i" ); }
	public function formattedDate_created() {return $this->formattedDate( Pull_List_Expansion::created, "M d, Y" ); }


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
		return parent::changedValue( Pull_List_Expansion::pattern, $this->pattern );
	}

	public function setPattern( $value = null)
	{
		parent::storeChange( Pull_List_Expansion::pattern, $value );
	}

	public function replace()
	{
		return parent::changedValue( Pull_List_Expansion::replace, $this->replace );
	}

	public function setReplace( $value = null)
	{
		parent::storeChange( Pull_List_Expansion::replace, $value );
	}

	public function created()
	{
		return parent::changedValue( Pull_List_Expansion::created, $this->created );
	}

	public function setCreated( $value = null)
	{
		parent::storeChange( Pull_List_Expansion::created, $value );
	}

	public function endpoint_id()
	{
		return parent::changedValue( Pull_List_Expansion::endpoint_id, $this->endpoint_id );
	}

	public function setEndpoint_id( $value = null)
	{
		parent::storeChange( Pull_List_Expansion::endpoint_id, $value );
	}


}

?>
