<?php

namespace model\pull_list;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\pull_list\Pull_List_Exclusion as Pull_List_Exclusion;

/* import related objects */
use \model\network\Endpoint_Type as Endpoint_Type;
use \model\network\Endpoint_TypeDBO as Endpoint_TypeDBO;

abstract class _Pull_List_ExclusionDBO extends DataObject
{
	public $pattern;
	public $type;
	public $created;
	public $endpoint_type_id;


	public function formattedDateTime_created() { return $this->formattedDate( Pull_List_Exclusion::created, "M d, Y H:i" ); }
	public function formattedDate_created() {return $this->formattedDate( Pull_List_Exclusion::created, "M d, Y" ); }


	// to-one relationship
	public function endpoint_type()
	{
		if ( isset( $this->endpoint_type_id ) ) {
			$model = Model::Named('Endpoint_Type');
			return $model->objectForId($this->endpoint_type_id);
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

	public function endpoint_type_id()
	{
		return parent::changedValue( Pull_List_Exclusion::endpoint_type_id, $this->endpoint_type_id );
	}

	public function setEndpoint_type_id( $value = null)
	{
		parent::storeChange( Pull_List_Exclusion::endpoint_type_id, $value );
	}


}

?>
