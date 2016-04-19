<?php

namespace model\pull_list;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Validation as Validation;

use \model\pull_list\Pull_ListDBO as Pull_ListDBO;

/* import related objects */
use \model\Endpoint as Endpoint;
use \model\EndpointDBO as EndpointDBO;
use \model\pull_list\Pull_List_Item as Pull_List_Item;
use \model\pull_list\Pull_List_ItemDBO as Pull_List_ItemDBO;
use \model\pull_list\Pull_List_Exclusion as Pull_List_Exclusion;
use \model\pull_list\Pull_List_ExclusionDBO as Pull_List_ExclusionDBO;
use \model\pull_list\Pull_List_Expansion as Pull_List_Expansion;
use \model\pull_list\Pull_List_ExpansionDBO as Pull_List_ExpansionDBO;

class Pull_List extends _Pull_List
{
	/**
	 *	Create/Update functions
	 */
	public function create( $endpoint, $name, $etag, $published)
	{
		return $this->base_create(
			$endpoint,
			$name,
			$etag,
			$published
		);
	}

	public function update( Pull_ListDBO $obj,
		$endpoint, $name, $etag, $published)
	{
		if ( isset( $obj ) && is_null($obj) == false ) {
			return $this->base_update(
				$obj,
				$endpoint,
				$name,
				$etag,
				$published
			);
		}
		return $obj;
	}


	public function attributesFor($object = null, $type = null) {
		return array(
			Pull_List::name => Model::TEXT_TYPE,
			Pull_List::etag => Model::TEXT_TYPE,
			Pull_List::created => Model::DATE_TYPE,
			Pull_List::published => Model::DATE_TYPE,
			Pull_List::endpoint_id => Model::INT_TYPE
		);
	}

	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Pull_List::name
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
		if ( $attr = Pull_List::endpoint_id ) {
			$model = Model::Named('Endpoint');
			return $model->allObjects();
		}
		return null;
	}

	/** Validation */
	function validate_name($object = null, $value)
	{
		if (empty($value)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List::name,
				"FIELD_EMPTY"
			);
		}
		return null;
	}
	function validate_etag($object = null, $value)
	{
		// make sure Etag is unique
		$existing = $this->objectForEtag($value);
		if ( is_null($object) == false && $existing != false && $existing->id != $object->id) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List::etag,
				"UNIQUE_FIELD_VALUE"
			);
		}
		return null;
	}
	function validate_created($object = null, $value)
	{
		return null;
	}
	function validate_published($object = null, $value)
	{
		return null;
	}
	function validate_endpoint_id($object = null, $value)
	{
		if (isset($object->endpoint_id) == false && empty($value) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List::endpoint_id,
				"FIELD_EMPTY"
			);
		}
		return null;
	}
}

?>
