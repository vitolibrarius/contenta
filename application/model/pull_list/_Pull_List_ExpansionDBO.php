<?php

namespace model\pull_list;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\pull_list\Pull_List_Expansion as Pull_List_Expansion;

/* import related objects */
use \model\network\Endpoint_Type as Endpoint_Type;
use \model\network\Endpoint_TypeDBO as Endpoint_TypeDBO;

abstract class _Pull_List_ExpansionDBO extends DataObject
{
	public $pattern;
	public $replace;
	public $sequence;
	public $created;
	public $endpoint_type_code;


	public function pkValue()
	{
		return $this->{Pull_List_Expansion::id};
	}

	public function formattedDateTime_created() { return $this->formattedDate( Pull_List_Expansion::created, "M d, Y H:i" ); }
	public function formattedDate_created() {return $this->formattedDate( Pull_List_Expansion::created, "M d, Y" ); }


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
			parent::storeChange( Pull_List_Expansion::endpoint_type_code, $obj->code );
			$this->saveChanges();
		}
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

	public function sequence()
	{
		return parent::changedValue( Pull_List_Expansion::sequence, $this->sequence );
	}

	public function setSequence( $value = null)
	{
		parent::storeChange( Pull_List_Expansion::sequence, $value );
	}


}

?>
