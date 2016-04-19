<?php

namespace model\pull_list;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Validation as Validation;

use \model\pull_list\Pull_List_ExpansionDBO as Pull_List_ExpansionDBO;

/* import related objects */
use \model\Endpoint as Endpoint;
use \model\EndpointDBO as EndpointDBO;

class Pull_List_Expansion extends _Pull_List_Expansion
{
	/**
	 *	Create/Update functions
	 */
	public function create( $endpoint, $pattern, $replace)
	{
		return $this->base_create(
			$endpoint,
			$pattern,
			$replace
		);
	}

	public function update( Pull_List_ExpansionDBO $obj,
		$endpoint, $pattern, $replace)
	{
		if ( isset( $obj ) && is_null($obj) == false ) {
			return $this->base_update(
				$obj,
				$endpoint,
				$pattern,
				$replace
			);
		}
		return $obj;
	}


	public function attributesFor($object = null, $type = null) {
		return array(
			Pull_List_Expansion::pattern => Model::TEXT_TYPE,
			Pull_List_Expansion::replace => Model::TEXT_TYPE,
			Pull_List_Expansion::created => Model::DATE_TYPE,
			Pull_List_Expansion::endpoint_id => Model::INT_TYPE
		);
	}

	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Pull_List_Expansion::pattern
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributeIsEditable($object = null, $type = null, $attr)
	{
		// add customization here
		return parent::attributeIsEditable($object, $type, $attr);
	}

	/*
	public function attributeRestrictionMessage($object = null, $type = null, $attr)	{ return null; }
	public function attributePlaceholder($object = null, $type = null, $attr)	{ return null; }
	*/

	public function attributeEditPattern($object = null, $type = null, $attr)
	{
		return null;
	}

	public function attributeOptions($object = null, $type = null, $attr)
	{
		if ( $attr = Pull_List_Expansion::endpoint_id ) {
			$model = Model::Named('Endpoint');
			return $model->allObjects();
		}
		return null;
	}

	/** Validation */
	function validate_pattern($object = null, $value)
	{
		if (empty($value)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List_Expansion::pattern,
				"FIELD_EMPTY"
			);
		}
		return null;
	}
	function validate_replace($object = null, $value)
	{
		return null;
	}
	function validate_created($object = null, $value)
	{
		return null;
	}
	function validate_endpoint_id($object = null, $value)
	{
		if (isset($object->endpoint_id) == false && empty($value) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List_Expansion::endpoint_id,
				"FIELD_EMPTY"
			);
		}
		return null;
	}
}

?>
