<?php

namespace model\version;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \model\version\PatchDBO as PatchDBO;

/* import related objects */
use \model\version\Version as Version;
use \model\version\VersionDBO as VersionDBO;

class Patch extends _Patch
{
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
		if (isset($object) && $object instanceof PatchDBO ) {
			// massage values as necessary
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		return array(
			Patch::name => Model::TEXT_TYPE,
			Patch::created => Model::DATE_TYPE,
			Patch::version_id => Model::INT_TYPE
		);
	}

	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Patch::name
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
		if ( Patch::version_id == $attr) {
			$model = Model::Named('Version');
			return $model->allObjects();
		}
		return null;
	}

	/** Validation */
	function validate_name($object = null, $value)
	{
		return parent::validate_name($object, $value);
	}

	function validate_created($object = null, $value)
	{
		return parent::validate_created($object, $value);
	}

	function validate_version_id($object = null, $value)
	{
		return parent::validate_version_id($object, $value);
	}

}

?>
