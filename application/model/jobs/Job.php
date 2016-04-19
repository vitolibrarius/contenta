<?php

namespace model\jobs;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Validation as Validation;

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
		if ( isset( $obj ) && is_null($obj) == false ) {
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

	public function createObject(array $values = array()) {
		if ( isset( $values[Job::endpoint_id]) && intval($values[Job::endpoint_id]) <= 0 ) {
			unset($values[Job::endpoint_id]);
		}

		try {
			$cronEval = new CronEvaluator( $values[Job::minute], $values[Job::hour], $values[Job::dayOfWeek] );
			$nextRunDate = $cronEval->nextDate();
			$values[Job::next] = $nextRunDate->getTimestamp();
		}
		catch ( \Exception $ve ) {
		}

		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if ( $object instanceof model\JobDBO ) {
			$m = (isset($values[Job::minute]) ? $values[Job::minute] : $object->minute);
			$h = (isset($values[Job::hour]) ? $values[Job::hour] : $object->hour);
			$d = (isset($values[Job::dayOfWeek]) ? $values[Job::dayOfWeek] : $object->dayOfWeek);

			try {
				$cronEval = new CronEvaluator( $m, $h, $d );
				$nextRunDate = $cronEval->nextDate();
				$values[Job::next] = $nextRunDate->getTimestamp();
			}
			catch ( \Exception $ve ) {
			}
		}

		if ( isset( $values[Job::endpoint_id]) && intval($values[Job::endpoint_id]) <= 0 ) {
			unset($values[Job::endpoint_id]);
		}

		return parent::updateObject($object, $values);
	}

	public function updateFailure( $job = null, $last = null )
	{
		if ( $job instanceof JobDBO) {
			$updates = array( Job::last_fail => $last );
			$count = 0;
			if ( null != $last ) {
				$count = 1;
				if (isset($job->fail_count) && is_int($job->fail_count)) {
				 	$count += $job->fail_count;
				 }
			}
			$updates[Job::fail_count] = $count;

			if ( $count > 5 ) {
				$updates[Job::enabled] = Model::TERTIARY_FALSE;
			}

			if ( $this->updateObject( $job, $updates) ) {
				return $this->refreshObject($job);
			}
		}
		return false;
	}

	public function jobsToRun()
	{
		$needsRun = Qualifier::OrQualifier(
			Qualifier::IsNull( Job::next ),
			Qualifier::LessThan( Job::next, time() )
		);
		$enabled = Qualifier::Equals( Job::enabled, Model::TERTIARY_TRUE );

		return $this->allObjectsForQualifier(Qualifier::AndQualifier( $needsRun, $enabled ));
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
		return null;
	}
	function validate_endpoint_id($object = null, $value)
	{
		if ( $object != null && $object->type() != null && $object->type()->requiresEndpoint() ) {
		if (isset($object->endpoint_id) == false && empty($value) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Job::endpoint_id,
				"FIELD_EMPTY"
			);
		}
		}
		return null;
	}
	function validate_enabled($object = null, $value)
	{
		return null;
	}
	function validate_one_shot($object = null, $value)
	{
		return null;
	}
	function validate_fail_count($object = null, $value)
	{
		return null;
	}
	function validate_elapsed($object = null, $value)
	{
		return null;
	}
	function validate_minute($object = null, $value)
	{
		$required = is_null($object) || (isset($object->minute) == false);
		try {
			CronEvaluator::validateExpressionPart( CronEvaluator::MINUTE, $value );
		}
		catch ( \Exception $ve ) {
			if ( $required ) {
				return Localized::ModelValidation($this->tableName(), Job::minute, $ve->getMessage() );
			}
		}
		return null;
	}
	function validate_hour($object = null, $value)
	{
		$required = is_null($object) || (isset($object->hour) == false);
		try {
			CronEvaluator::validateExpressionPart( CronEvaluator::HOUR, $value );
		}
		catch ( \Exception $ve ) {
			if ( $required ) {
				return Localized::ModelValidation($this->tableName(), Job::hour, $ve->getMessage() );
			}
		}
		return null;
	}
	function validate_dayOfWeek($object = null, $value)
	{
		$required = is_null($object) || (isset($object->dayOfWeek) == false);
		try {
			CronEvaluator::validateExpressionPart( CronEvaluator::DAYOFWEEK, $value );
		}
		catch ( \Exception $ve ) {
			if ( $required ) {
				return Localized::ModelValidation($this->tableName(), Job::dayOfWeek, $ve->getMessage() );
			}
		}
		return null;
	}
	function validate_parameter($object = null, $value)
	{
		return null;
	}
	function validate_next($object = null, $value)
	{
		if (empty($value) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Job::next,
				"FIELD_EMPTY"
			);
		}
		return null;
	}
	function validate_last_run($object = null, $value)
	{
		return null;
	}
	function validate_last_fail($object = null, $value)
	{
		return null;
	}
	function validate_created($object = null, $value)
	{
		return null;
	}
}

?>
