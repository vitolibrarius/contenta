<?php

namespace model\jobs;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \model\jobs\Job_TypeDBO as Job_TypeDBO;

/* import related objects */
use \model\jobs\Job_Running as Job_Running;
use \model\jobs\Job_RunningDBO as Job_RunningDBO;
use \model\jobs\Job as Job;
use \model\jobs\JobDBO as JobDBO;

class Job_Type extends _Job_Type
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
		if (isset($object) && $object instanceof Job_TypeDBO ) {
			// massage values as necessary
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		return array(
			Job_Type::code => Model::TEXT_TYPE,
			Job_Type::name => Model::TEXT_TYPE,
			Job_Type::desc => Model::TEXT_TYPE,
			Job_Type::processor => Model::TEXT_TYPE,
			Job_Type::parameter => Model::TEXT_TYPE,
			Job_Type::scheduled => Model::FLAG_TYPE,
			Job_Type::requires_endpoint => Model::FLAG_TYPE
		);
	}

	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Job_Type::code
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
				case Job_Type::scheduled:
					return true;
				case Job_Type::requires_endpoint:
					return true;
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

	function validate_desc($object = null, $value)
	{
		return parent::validate_desc($object, $value);
	}

	function validate_processor($object = null, $value)
	{
		return parent::validate_processor($object, $value);
	}

	function validate_parameter($object = null, $value)
	{
		return parent::validate_parameter($object, $value);
	}

	function validate_scheduled($object = null, $value)
	{
		return parent::validate_scheduled($object, $value);
	}

	function validate_requires_endpoint($object = null, $value)
	{
		return parent::validate_requires_endpoint($object, $value);
	}

}

?>
