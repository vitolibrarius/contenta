<?php

namespace model\jobs;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \db\Qualifier as Qualifier;

use \model\jobs\JobDBO as JobDBO;
use \utilities\CronEvaluator as CronEvaluator;

/* import related objects */
use \model\jobs\Job_Type as Job_Type;
use \model\jobs\Job_TypeDBO as Job_TypeDBO;
use \model\network\Endpoint as Endpoint;
use \model\network\EndpointDBO as EndpointDBO;

class Job extends _Job
{
	/**
	 *	Create/Update functions
	 */
	public function createObject(array $values = array()) {
			Logger::logInfo( "createObject " . var_export($values, true) );

		if ( isset( $values[Job::endpoint_id]) && intval($values[Job::endpoint_id]) <= 0 ) {
			unset($values[Job::endpoint_id]);
		}

		try {
			$cronEval = new CronEvaluator( $values[Job::minute], $values[Job::hour], $values[Job::dayOfWeek] );
			$nextRunDate = $cronEval->nextDate();
			$values[Job::next] = $nextRunDate->getTimestamp();
		}
		catch ( \Exception $ve ) {
			throw $ve;
		}

		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if ( $object instanceof JobDBO ) {
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

	public function attributesFor($object = null, $type = null) {
		$attrFor = array(
			Job::enabled,
			Job::one_shot,
			Job::minute,
			Job::hour,
			Job::dayOfWeek,
			Job::parameter
		);
		if ( is_null($type) || $type->isRequires_endpoint() ) {
			$attrFor[] = Job::endpoint_id;
		}
		return array_intersect_key($this->attributesMap(),array_flip($attrFor));
	}

	public function attributeIsEditable($object = null, $type = null, $attr)
	{
		if ( $object instanceof JobDBO ) {
			if ( $attr == Job::type_code ) {
				return false;
			}
		}
		return parent::attributeIsEditable($object, $type, $attr);
	}

	/*
	public function attributeRestrictionMessage($object = null, $type = null, $attr)	{ return null; }
	public function attributePlaceholder($object = null, $type = null, $attr)	{ return null; }
	*/

	/*
	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		return parent::attributeDefaultValue($object, $type, $attr);
	}
	*/

	/*
	public function attributeEditPattern($object = null, $type = null, $attr)
	{
		return null;
	}
	*/

	public function attributeOptions($object = null, $type = null, $attr)
	{
		if ( $attr == Job::type_code ) {
			$model = Model::Named('Job_Type');
			return $model->allObjects();
		}
		if ( $attr == Job::endpoint_id ) {
			$model = Model::Named('Endpoint');
			return $model->allObjects();
		}
		return null;
	}

	/** Validation */
/*
	function validate_type_code($object = null, $value)
	{
		return parent::validate_type_code($object, $value);
	}
*/

/*
	function validate_endpoint_id($object = null, $value)
	{
		return parent::validate_endpoint_id($object, $value);
	}
*/

/*
	function validate_enabled($object = null, $value)
	{
		return parent::validate_enabled($object, $value);
	}
*/

/*
	function validate_one_shot($object = null, $value)
	{
		return parent::validate_one_shot($object, $value);
	}
*/

/*
	function validate_fail_count($object = null, $value)
	{
		return parent::validate_fail_count($object, $value);
	}
*/

/*
	function validate_elapsed($object = null, $value)
	{
		return parent::validate_elapsed($object, $value);
	}
*/

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
		return parent::validate_minute($object, $value);
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
		return parent::validate_hour($object, $value);
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
		return parent::validate_dayOfWeek($object, $value);
	}

/*
	function validate_parameter($object = null, $value)
	{
		return parent::validate_parameter($object, $value);
	}
*/

/*
	function validate_next($object = null, $value)
	{
		return parent::validate_next($object, $value);
	}
*/

/*
	function validate_last_run($object = null, $value)
	{
		return parent::validate_last_run($object, $value);
	}
*/

/*
	function validate_last_fail($object = null, $value)
	{
		return parent::validate_last_fail($object, $value);
	}
*/

/*
	function validate_created($object = null, $value)
	{
		return parent::validate_created($object, $value);
	}
*/

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
}

?>
