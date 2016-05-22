<?php

namespace model\logs;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\logs\Log_LevelDBO as Log_LevelDBO;

/* import related objects */

class Log_Level extends _Log_Level
{
	/**
	 *	Create/Update functions
	 */
	public function create( $code, $name)
	{
		return $this->base_create(
			$code,
			$name
		);
	}

	public function update( Log_LevelDBO $obj,
		$code, $name)
	{
		if ( isset( $obj ) && is_null($obj) == false ) {
			return $this->base_update(
				$obj,
				$code,
				$name
			);
		}
		return $obj;
	}


	public function attributesFor($object = null, $type = null) {
		return array(
			Log_Level::code => Model::TEXT_TYPE,
			Log_Level::name => Model::TEXT_TYPE
		);
	}

	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Log_Level::code
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
				Log_Level::code,
				"FIELD_EMPTY"
			);
		}
		// make sure Code is unique
		$existing = $this->objectForCode($value);
		if ( is_null($object) == false && $existing != false && $existing->id != $object->id) {
			return Localized::ModelValidation(
				$this->tableName(),
				Log_Level::code,
				"UNIQUE_FIELD_VALUE"
			);
		}
		return null;
	}
	function validate_name($object = null, $value)
	{
		// make sure Name is unique
		$existing = $this->objectForName($value);
		if ( is_null($object) == false && $existing != false && $existing->id != $object->id) {
			return Localized::ModelValidation(
				$this->tableName(),
				Log_Level::name,
				"UNIQUE_FIELD_VALUE"
			);
		}
		return null;
	}
}

?>
