<?php

namespace model\version;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Validation as Validation;

use \model\version\PatchDBO as PatchDBO;

/* import related objects */
use \model\version\Version as Version;
use \model\version\VersionDBO as VersionDBO;

class Patch extends _Patch
{
	/**
	 *	Create/Update functions
	 */
	public function create( $version, $name)
	{
		return $this->base_create(
			$version,
			$name
		);
	}

	public function update( PatchDBO $obj,
		$version, $name)
	{
		if ( isset( $obj ) && is_null($obj) == false ) {
			return $this->base_update(
				$obj,
				$version,
				$name
			);
		}
		return $obj;
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

	public function attributeEditPattern($object = null, $type = null, $attr)
	{
		return null;
	}

	public function attributeOptions($object = null, $type = null, $attr)
	{
		if ( $attr = Patch::version_id ) {
			$model = Model::Named('Version');
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
				Patch::name,
				"FIELD_EMPTY"
			);
		}
		// make sure Name is unique
		$existing = $this->objectForName($value);
		if ( is_null($object) == false && $existing != false && $existing->id != $object->id) {
			return Localized::ModelValidation(
				$this->tableName(),
				Patch::name,
				"UNIQUE_FIELD_VALUE"
			);
		}
		return null;
	}
	function validate_created($object = null, $value)
	{
		return null;
	}
	function validate_version_id($object = null, $value)
	{
		if (isset($object->version_id) == false && empty($value) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Patch::version_id,
				"FIELD_EMPTY"
			);
		}
		return null;
	}
}

?>
