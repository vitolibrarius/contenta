<?php

namespace model\logs;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Validation as Validation;

use model\logs\Log as Log;
use model\logs\LogDBO as LogDBO;

class Log_Validation extends Validation
{
	public function tableName() { return Log::TABLE; }

	public function attributesFor($object = null, $type = null) {
		return array(
			Log::trace => Validation::TEXT_TYPE,
			Log::trace_id => Validation::TEXT_TYPE,
			Log::context => Validation::TEXT_TYPE,
			Log::context_id => Validation::TEXT_TYPE,
			Log::message => Validation::TEXT_TYPE,
			Log::session => Validation::TEXT_TYPE,
			Log::level => Validation::TEXT_TYPE,
			Log::created => Validation::DATE_TYPE,
		);
	}

	public function attributesMandatory($object = null)				 			{ return array(); }
	public function attributeName($object = null, $type = null, $attr)			{ return $this->attributeId($attr); }
	public function attributeIsEditable($object = null, $type = null, $attr)	{ return true; }
	public function attributeRestrictionMessage($object = null, $type = null, $attr)	{ return null; }
	public function attributeEditPattern($object = null, $type = null, $attr)	{ return null; }
	public function attributePlaceholder($object = null, $type = null, $attr)	{ return null; }
	public function attributeOptions($object = null, $type = null, $attr)		{ return null; }

}

?>
