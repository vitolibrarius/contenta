<?php

namespace model\pull_list;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \model\pull_list\Pull_List_Exclusion as Pull_List_Exclusion;

/* import related objects */
use \model\network\Endpoint_Type as Endpoint_Type;
use \model\network\Endpoint_TypeDBO as Endpoint_TypeDBO;

abstract class _Pull_List_ExclusionDBO extends DataObject
{
	public $pattern;
	public $type;
	public $created;
	public $endpoint_type_code;


	public function pkValue()
	{
		return $this->{Pull_List_Exclusion::id};
	}

	public function modelName()
	{
		return "Pull_List_Exclusion";
	}

	public function dboName()
	{
		return "\model\pull_list\Pull_List_ExclusionDBO";
	}

	public function formattedDateTime_created() { return $this->formattedDate( Pull_List_Exclusion::created, "M d, Y H:i" ); }
	public function formattedDate_created() {return $this->formattedDate( Pull_List_Exclusion::created, "M d, Y" ); }


	// to-one relationship
	public function endpoint_type()
	{
		if ( isset( $this->endpoint_type_code ) ) {
			$model = Model::Named('Endpoint_Type');
			return $model->objectForCode($this->endpoint_type_code);
		}
		return false;
	}

	public function setEndpoint_type(Endpoint_TypeDBO $obj = null)
	{
		if ( isset($obj, $obj->code) && (isset($this->endpoint_type_code) == false || $obj->code != $this->endpoint_type_code) ) {
			parent::storeChange( Pull_List_Exclusion::endpoint_type_code, $obj->code );
			$this->saveChanges();
		}
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


}

?>
