<?php

namespace model\jobs;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

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
	public function create( $code, $name, $desc, $processor, $parameter, $scheduled, $requires_endpoint)
	{
		return $this->base_create(
			$code,
			$name,
			$desc,
			$processor,
			$parameter,
			$scheduled,
			$requires_endpoint
		);
	}

	public function update( Job_TypeDBO $obj,
		$code, $name, $desc, $processor, $parameter, $scheduled, $requires_endpoint)
	{
		if ( isset( $obj ) && is_null($obj) == false ) {
			return $this->base_update(
				$obj,
				$code,
				$name,
				$desc,
				$processor,
				$parameter,
				$scheduled,
				$requires_endpoint
			);
		}
		return $obj;
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
				Job_Type::code,
				"FIELD_EMPTY"
			);
		}
		// make sure Code is unique
		$existing = $this->objectForCode($value);
		if ( is_null($object) == false && $existing != false && $existing->id != $object->id) {
			return Localized::ModelValidation(
				$this->tableName(),
				Job_Type::code,
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
				Job_Type::name,
				"UNIQUE_FIELD_VALUE"
			);
		}
		return null;
	}
	function validate_desc($object = null, $value)
	{
		return null;
	}
	function validate_processor($object = null, $value)
	{
		return null;
	}
	function validate_parameter($object = null, $value)
	{
		return null;
	}
	function validate_scheduled($object = null, $value)
	{
		return null;
	}
	function validate_requires_endpoint($object = null, $value)
	{
		return null;
	}
}

?>
