<?php

namespace model\pull_list;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \model\pull_list\Pull_List_ExclusionDBO as Pull_List_ExclusionDBO;

/* import related objects */
use \model\Endpoint_Type as Endpoint_Type;
use \model\Endpoint_TypeDBO as Endpoint_TypeDBO;

class Pull_List_Exclusion extends _Pull_List_Exclusion
{
	const GROUP_TYPE = "group";
	const ITEM_TYPE = "item";

	/**
	 *	Create/Update functions
	 */
	public function createObject( array $values = array())
	{
		if ( isset($values) ) {
			// massage values as necessary
		}

		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Pull_List_Exclusion ) {
			// massage values as necessary
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		return array(
			Pull_List_Exclusion::pattern => Model::TEXT_TYPE,
			Pull_List_Exclusion::type => Model::TEXT_TYPE,
			Pull_List_Exclusion::created => Model::DATE_TYPE,
			Pull_List_Exclusion::endpoint_type_id => Model::INT_TYPE
		);
	}

	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Pull_List_Exclusion::pattern
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
				case Pull_List_Exclusion::type:
					return 'item';
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
		if ( $attr == Pull_List_Exclusion::endpoint_type_id ) {
			$model = Model::Named('Endpoint_Type');
			return $model->allObjects();
		}
		return null;
	}

	/** Validation */
	function validate_pattern($object = null, $value)
	{
		return parent::validate_pattern($object, $value);
	}

	function validate_type($object = null, $value)
	{
		return parent::validate_type($object, $value);
	}

	function validate_created($object = null, $value)
	{
		return parent::validate_created($object, $value);
	}

	function validate_endpoint_type_id($object = null, $value)
	{
		return parent::validate_endpoint_type_id($object, $value);
	}

}

?>
