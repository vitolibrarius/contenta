<?php

namespace model\logs;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \model\logs\LogDBO as LogDBO;

/* import related objects */
use \model\logs\Log_Level as Log_Level;
use \model\logs\Log_LevelDBO as Log_LevelDBO;

class Log extends _Log
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
		if (isset($object) && $object instanceof LogDBO ) {
			// massage values as necessary
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		$attrFor = array(
			Log::trace,
			Log::trace_id,
			Log::context,
			Log::context_id,
			Log::message,
			Log::session,
			Log::level,
			Log::created
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

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) === false || is_null($object) == true) {
			switch ($attr) {
				case Log::session:
					return session_id();
				case Log::level:
					return 'warning';
			}
		}
		return parent::attributeDefaultValue($object, $type, $attr);
	}

	/*
	public function attributeEditPattern($object = null, $type = null, $attr)
	{
		return null;
	}
	*/

	public function attributeOptions($object = null, $type = null, $attr)
	{
		if ( Log::level == $attr ) {
			$model = Model::Named('Log_Level');
			return $model->allObjects();
		}
		return null;
	}

	/** Validation */
/*
	function validate_trace($object = null, $value)
	{
		return parent::validate_trace($object, $value);
	}
*/

/*
	function validate_trace_id($object = null, $value)
	{
		return parent::validate_trace_id($object, $value);
	}
*/

/*
	function validate_context($object = null, $value)
	{
		return parent::validate_context($object, $value);
	}
*/

/*
	function validate_context_id($object = null, $value)
	{
		return parent::validate_context_id($object, $value);
	}
*/

/*
	function validate_message($object = null, $value)
	{
		return parent::validate_message($object, $value);
	}
*/

/*
	function validate_session($object = null, $value)
	{
		return parent::validate_session($object, $value);
	}
*/

/*
	function validate_level($object = null, $value)
	{
		return parent::validate_level($object, $value);
	}
*/

/*
	function validate_created($object = null, $value)
	{
		return parent::validate_created($object, $value);
	}
*/

}

?>
