<?php

namespace model\logs;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Validation as Validation;

use \model\logs\LogDBO as LogDBO;

/* import related objects */
use \model\logs\Log_Level as Log_Level;
use \model\logs\Log_LevelDBO as Log_LevelDBO;

class Log extends _Log
{
	/**
	 *	Create/Update functions
	 */
	public function create( $logLevel, $trace, $trace_id, $context, $context_id, $message, $session)
	{
		return $this->base_create(
			$logLevel,
			$trace,
			$trace_id,
			$context,
			$context_id,
			$message,
			$session
		);
	}

	public function update( LogDBO $obj,
		$logLevel, $trace, $trace_id, $context, $context_id, $message, $session)
	{
		if ( isset( $obj ) && is_null($obj) == false ) {
			return $this->base_update(
				$obj,
				$logLevel,
				$trace,
				$trace_id,
				$context,
				$context_id,
				$message,
				$session
			);
		}
		return $obj;
	}


	public function attributesFor($object = null, $type = null) {
		return array(
			Log::trace => Model::TEXT_TYPE,
			Log::trace_id => Model::TEXT_TYPE,
			Log::context => Model::TEXT_TYPE,
			Log::context_id => Model::TEXT_TYPE,
			Log::message => Model::TEXT_TYPE,
			Log::session => Model::TEXT_TYPE,
			Log::level_code => Model::TEXT_TYPE,
			Log::created => Model::DATE_TYPE
		);
	}

	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Log::message
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
		if ( $attr = Log::level_code ) {
			$model = Model::Named('Log_Level');
			return $model->allObjects();
		}
		return null;
	}

	/** Validation */
	function validate_trace($object = null, $value)
	{
		return null;
	}
	function validate_trace_id($object = null, $value)
	{
		return null;
	}
	function validate_context($object = null, $value)
	{
		return null;
	}
	function validate_context_id($object = null, $value)
	{
		return null;
	}
	function validate_message($object = null, $value)
	{
		if (empty($value)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Log::message,
				"FIELD_EMPTY"
			);
		}
		return null;
	}
	function validate_session($object = null, $value)
	{
		return null;
	}
	function validate_level_code($object = null, $value)
	{
		if (isset($object->level_code) == false && empty($value) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Log::level_code,
				"FIELD_EMPTY"
			);
		}
		return null;
	}
	function validate_created($object = null, $value)
	{
		return null;
	}
}

?>
