<?php

namespace model\pull_list;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \model\pull_list\Pull_List_ExclDBO as Pull_List_ExclDBO;

/* import related objects */
use \model\network\Endpoint_Type as Endpoint_Type;
use \model\network\Endpoint_TypeDBO as Endpoint_TypeDBO;

class Pull_List_Excl extends _Pull_List_Excl
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
		if (isset($object) && $object instanceof Pull_List_ExclDBO ) {
			// massage values as necessary
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		$attrFor = array(
			Pull_List_Excl::pattern,
			Pull_List_Excl::type,
			Pull_List_Excl::created,
			Pull_List_Excl::endpoint_type_code
		);
		return array_intersect_key($this->attributesMap(),array_flip($attrFor));
	}

	/*
	public function attributeIsEditable($object = null, $type = null, $attr)
	{
		// add customization here
		return parent::attributeIsEditable($object, $type, $attr);
	}
	*/

	/*
	public function attributeRestrictionMessage($object = null, $type = null, $attr)	{ return null; }
	public function attributePlaceholder($object = null, $type = null, $attr)	{ return null; }
	*/

	/*
	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		return parent::attributeDefaultValue($object, $type, $attr);
	}
	*/

	/*
	public function attributeEditPattern($object = null, $type = null, $attr)
	{
		return null;
	}
	*/

	public function attributeOptions($object = null, $type = null, $attr)
	{
		if ( Pull_List_Excl::endpoint_type_code == $attr ) {
			$model = Model::Named('Endpoint_Type');
			return $model->allObjects();
		}
		return null;
	}

	/** Validation */
/*
	function validate_pattern($object = null, $value)
	{
		return parent::validate_pattern($object, $value);
	}
*/

/*
	function validate_type($object = null, $value)
	{
		return parent::validate_type($object, $value);
	}
*/

/*
	function validate_created($object = null, $value)
	{
		return parent::validate_created($object, $value);
	}
*/

/*
	function validate_endpoint_type_code($object = null, $value)
	{
		return parent::validate_endpoint_type_code($object, $value);
	}
*/

}

?>
