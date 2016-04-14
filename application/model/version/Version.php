<?php

namespace model\version;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Validation as Validation;

use \model\version\VersionDBO as VersionDBO;

/* import related objects */
use \model\version\Patch as Patch;
use \model\version\PatchDBO as PatchDBO;

class Version extends _Version
{
	public function attributesFor($object = null, $type = null) {
		return array(
			Version::code => Model::TEXT_TYPE,
			Version::major => Model::INT_TYPE,
			Version::minor => Model::INT_TYPE,
			Version::patch => Model::INT_TYPE,
			Version::created => Model::DATE_TYPE
		);
	}

	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Version::code
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
		return null;
	}

	/** Validation */
	function validate_code($object = null, $value)
	{
		if (empty($value)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Version::code,
				"FIELD_EMPTY"
			);
		}
		// make sure Code is unique
		$existing = $this->objectForCode($value);
		if ( is_null($object) == false && $existing != false && $existing->id != $object->id) {
			return Localized::ModelValidation(
				$this->tableName(),
				Version::code,
				"UNIQUE_FIELD_VALUE"
			);
		}
		return null;
	}
	function validate_major($object = null, $value)
	{
		return null;
	}
	function validate_minor($object = null, $value)
	{
		return null;
	}
	function validate_patch($object = null, $value)
	{
		return null;
	}
	function validate_created($object = null, $value)
	{
		return null;
	}
}

?>
