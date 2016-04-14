<?php

namespace model\pull_list;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Validation as Validation;

use \model\pull_list\Pull_List_ItemDBO as Pull_List_ItemDBO;

/* import related objects */
use \model\pull_list\Pull_List as Pull_List;
use \model\pull_list\Pull_ListDBO as Pull_ListDBO;

class Pull_List_Item extends _Pull_List_Item
{
	/**
	 *	Create/Update functions
	 */
	public function create( $pull_list, $group_name, $data, $name, $issue, $year)
	{
		return $this->base_create(
			$pull_list,
			$group_name,
			$data,
			$name,
			$issue,
			$year
		);
	}

	public function update( Pull_List_ItemDBO $obj,
		$pull_list, $group_name, $data, $name, $issue, $year)
	{
		if ( isset( $obj ) && is_null($obj) == false ) {
			return $this->base_update(
				$obj,
				$pull_list,
				$group_name,
				$data,
				$name,
				$issue,
				$year
			);
		}
		return $obj;
	}


	public function attributesFor($object = null, $type = null) {
		return array(
			Pull_List_Item::group_name => Model::TEXT_TYPE,
			Pull_List_Item::data => Model::TEXT_TYPE,
			Pull_List_Item::created => Model::DATE_TYPE,
			Pull_List_Item::name => Model::TEXT_TYPE,
			Pull_List_Item::issue => Model::TEXT_TYPE,
			Pull_List_Item::year => Model::INT_TYPE,
			Pull_List_Item::pull_list_id => Model::INT_TYPE
		);
	}

	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Pull_List_Item::data,
				Pull_List_Item::name
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
		if ( $attr = Pull_List_Item::pull_list_id ) {
			$model = Model::Named('Pull_List');
			return $model->allObjects();
		}
		return null;
	}

	/** Validation */
	function validate_group_name($object = null, $value)
	{
		return null;
	}
	function validate_data($object = null, $value)
	{
		if (empty($value)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List_Item::data,
				"FIELD_EMPTY"
			);
		}
		return null;
	}
	function validate_created($object = null, $value)
	{
		return null;
	}
	function validate_name($object = null, $value)
	{
		if (empty($value)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List_Item::name,
				"FIELD_EMPTY"
			);
		}
		return null;
	}
	function validate_issue($object = null, $value)
	{
		return null;
	}
	function validate_year($object = null, $value)
	{
		return null;
	}
	function validate_pull_list_id($object = null, $value)
	{
		return null;
	}
}

?>
