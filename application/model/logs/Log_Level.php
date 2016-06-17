<?php

namespace model\logs;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \model\logs\Log_LevelDBO as Log_LevelDBO;

/* import related objects */

class Log_Level extends _Log_Level
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
		if (isset($object) && $object instanceof Log_LevelDBO ) {
			// massage values as necessary
		}

		return parent::updateObject($object, $values);
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
		return null;
	}

	/** Validation */
	function validate_code($object = null, $value)
	{
		return parent::validate_code($object, $value);
	}

	function validate_name($object = null, $value)
	{
		return parent::validate_name($object, $value);
	}

}

?>
