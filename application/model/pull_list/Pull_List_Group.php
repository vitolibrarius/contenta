<?php

namespace model\pull_list;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \model\pull_list\Pull_List_GroupDBO as Pull_List_GroupDBO;

/* import related objects */
use \model\pull_list\Pull_List as Pull_List;
use \model\pull_list\Pull_ListDBO as Pull_ListDBO;
use \model\pull_list\Pull_List_Item as Pull_List_Item;
use \model\pull_list\Pull_List_ItemDBO as Pull_List_ItemDBO;

class Pull_List_Group extends _Pull_List_Group
{
	/**
	 *	Create/Update functions
	 */
	public function createObject( array $values = array())
	{
		if ( isset($values) ) {
			// massage values as necessary
			if ( isset($values[Pull_List_Group::data]) ) {
				$values[Pull_List_Group::name] = normalize($values[Pull_List_Group::data]);
				$existing = $this->objectForData($values[Pull_List_Group::data]);
				if ( $existing != false ) {
					return array( $existing, null);
				}
			}
		}

		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Pull_List_GroupDBO ) {
			// massage values as necessary
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		return array(
			Pull_List_Group::name => Model::TEXT_TYPE,
			Pull_List_Group::data => Model::TEXT_TYPE,
			Pull_List_Group::created => Model::DATE_TYPE
		);
	}

	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Pull_List_Group::name,
				Pull_List_Group::data
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

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) === false || is_null($object) == true) {
			switch ($attr) {
			}
		}
		return parent::attributeDefaultValue($object, $type, $attr);
	}

	public function attributeEditPattern($object = null, $type = null, $attr)
	{
		return null;
	}

	public function attributeOptions($object = null, $type = null, $attr)
	{
		if ( $attr == Pull_List_Group::pull_list_id ) {
			$model = Model::Named('Pull_List');
			return $model->allObjects();
		}
		return null;
	}

	/** Validation */
	function validate_name($object = null, $value)
	{
		return parent::validate_name($object, $value);
	}

	function validate_data($object = null, $value)
	{
		return parent::validate_data($object, $value);
	}

	function validate_created($object = null, $value)
	{
		return parent::validate_created($object, $value);
	}

}

?>
