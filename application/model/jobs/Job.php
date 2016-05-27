<?php

namespace model\jobs;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \model\jobs\JobDBO as JobDBO;

/* import related objects */
use \model\jobs\Job_Type as Job_Type;
use \model\jobs\Job_TypeDBO as Job_TypeDBO;
use \model\Endpoint as Endpoint;
use \model\EndpointDBO as EndpointDBO;

class Job extends _Job
{
	/**
	 *	Create/Update functions
	 */
	public function create( $endpoint, $type_id, $enabled, $one_shot, $fail_count, $elapsed, $minute, $hour, $dayOfWeek, $parameter, $next, $last_run, $last_fail)
	{
		return $this->base_create(
			$endpoint,
			$type_id,
			$enabled,
			$one_shot,
			$fail_count,
			$elapsed,
			$minute,
			$hour,
			$dayOfWeek,
			$parameter,
			$next,
			$last_run,
			$last_fail
		);
	}

	public function update( JobDBO $obj,
		$endpoint, $type_id, $enabled, $one_shot, $fail_count, $elapsed, $minute, $hour, $dayOfWeek, $parameter, $next, $last_run, $last_fail)
	{
		if ( isset( $obj ) && is_null($obj) === false ) {
			return $this->base_update(
				$obj,
				$endpoint,
				$type_id,
				$enabled,
				$one_shot,
				$fail_count,
				$elapsed,
				$minute,
				$hour,
				$dayOfWeek,
				$parameter,
				$next,
				$last_run,
				$last_fail
			);
		}
		return $obj;
	}


	public function attributesFor($object = null, $type = null) {
		return array(
			Job::type_id => Model::INT_TYPE,
			Job::endpoint_id => Model::INT_TYPE,
			Job::enabled => Model::FLAG_TYPE,
			Job::one_shot => Model::FLAG_TYPE,
			Job::fail_count => Model::INT_TYPE,
			Job::elapsed => Model::INT_TYPE,
			Job::minute => Model::TEXT_TYPE,
			Job::hour => Model::TEXT_TYPE,
			Job::dayOfWeek => Model::TEXT_TYPE,
			Job::parameter => Model::TEXT_TYPE,
			Job::next => Model::DATE_TYPE,
			Job::last_run => Model::DATE_TYPE,
			Job::last_fail => Model::DATE_TYPE,
			Job::created => Model::DATE_TYPE
		);
	}

	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Job::minute,
				Job::hour,
				Job::dayOfWeek,
				Job::next
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
		if ( $attr = Job::job_type_id ) {
			$model = Model::Named('Job_Type');
			return $model->allObjects();
		}
		if ( $attr = Job::endpoint_id ) {
			$model = Model::Named('Endpoint');
			return $model->allObjects();
		}
		return null;
	}

	/** Validation */
	function validate_type_id($object = null, $value)
	{
		return parent::validate_type_id($object, $value);
	}

	function validate_endpoint_id($object = null, $value)
	{
		return parent::validate_endpoint_id($object, $value);
	}

	function validate_enabled($object = null, $value)
	{
		return parent::validate_enabled($object, $value);
	}

	function validate_one_shot($object = null, $value)
	{
		return parent::validate_one_shot($object, $value);
	}

	function validate_fail_count($object = null, $value)
	{
		return parent::validate_fail_count($object, $value);
	}

	function validate_elapsed($object = null, $value)
	{
		return parent::validate_elapsed($object, $value);
	}

	function validate_minute($object = null, $value)
	{
		return parent::validate_minute($object, $value);
	}

	function validate_hour($object = null, $value)
	{
		return parent::validate_hour($object, $value);
	}

	function validate_dayOfWeek($object = null, $value)
	{
		return parent::validate_dayOfWeek($object, $value);
	}

	function validate_parameter($object = null, $value)
	{
		return parent::validate_parameter($object, $value);
	}

	function validate_next($object = null, $value)
	{
		return parent::validate_next($object, $value);
	}

	function validate_last_run($object = null, $value)
	{
		return parent::validate_last_run($object, $value);
	}

	function validate_last_fail($object = null, $value)
	{
		return parent::validate_last_fail($object, $value);
	}

	function validate_created($object = null, $value)
	{
		return parent::validate_created($object, $value);
	}

}

?>
