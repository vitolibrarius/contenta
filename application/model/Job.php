<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;
use \Localized as Localized;
use \Logger as Logger;

use utilities\CronEvaluator as CronEvaluator;
use db\Qualifier as Qualifier;

class Job extends Model
{
	const TABLE =		'job';
	const id =			'id';
	const type_id =		'type_id';
	const endpoint_id =	'endpoint_id';
	const minute =		'minute';
	const hour =		'hour';
	const dayOfWeek =	'dayOfWeek';
	const one_shot =	'one_shot';
	const created =		'created';
	const next =		'next';
	const last_run =	'last_run';
	const parameter =	'parameter';
	const enabled =		'enabled';
	const elapsed =		'elapsed';


	public function tableName() { return Job::TABLE; }
	public function tablePK() { return Job::id; }
	public function sortOrder() { return array(Job::type_id, Job::next); }

	public function allColumnNames()
	{
		return array(
			Job::id, Job::type_id, Job::endpoint_id,
			Job::minute, Job::hour, Job::dayOfWeek, Job::parameter,
			Job::created, Job::next, Job::last_run, Job::one_shot, Job::enabled, Job::elapsed
		);
	}

	public function allForTypeCode($code = null)
	{
		if ( $code != null ) {
			$type_model = Model::Named('Job_Type');
			$type = $type_model->jobTypeForCode($code);
			if ( $type != false ) {
				return $this->allForType($type);
			}
		}
		return false;
	}

	public function allForType($obj)
	{
		return $this->allObjectsForFK(Job::type_id, $obj, array(Job::next));
	}

	public function create($typeObj, $endpointObj, $minute, $hour, $dayOfWeek, $one_shot = false, $parameter = null, $enabled = true)
	{
		if ( isset($typeObj, $name, $jobURL) ) {
			$params = array(
				Job::type_id => (is_a($typeObj, 'model\Job_Type') ? $typeObj->id : null),
				Job::endpoint_id => (is_a($endpointObj, 'model\Endpoint') ? $endpointObj->id : null),
				Job::minute => $minute,
				Job::hour => $hour,
				Job::dayOfWeek => $dayOfWeek,
				Job::parameter => $parameter,
				Job::created => time(),
				Job::next => null,
				Job::one_shot => ($one_shot)? 1 : 0,
				Job::enabled => ($enabled)? 1 : 0
			);

			$objectOrErrors = $this->createObject($params);
			if ( is_array($objectOrErrors) ) {
				return $objectOrErrors;
			}
			else if ($objectOrErrors != false) {
				return $this->objectForId( (string)$objectOrErrors);
			}
		}

		return false;
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

	public function jobsToRun()
	{
		$needsRun = Qualifier::OrQualifier(
			Qualifier::IsNull( Job::next ),
			Qualifier::LessThan( Job::next, time() )
		);
		$enabled = Qualifier::Equals( Job::enabled, Model::TERTIARY_TRUE );

		return $this->allObjectsForQualifier(Qualifier::AndQualifier( $needsRun, $enabled ));
	}

	/* EditableModelInterface */
	public function validateForSave($object = null, array &$values = array())
	{
		$validationErrors = parent::validateForSave($object, $values);
		return $validationErrors;
	}

	function validate_endpoint_id($object = null, $value)
	{
		if ( $object != null && $object->type() != null && $object->type()->requiresEndpoint() ) {
			if (isset($object->endpoint_id) == false && empty($value) ) {
				return Localized::ModelValidation($this->tableName(), Job::endpoint_id, "FIELD_EMPTY");
			}
		}

		return null;
	}

	function validate_minute($object = null, $value)
	{
		try {
			CronEvaluator::validateExpressionPart( CronEvaluator::MINUTE, $value );
		}
		catch ( \Exception $ve ) {
			return Localized::ModelValidation($this->tableName(), Job::minute, $ve->getMessage() );
		}
		return null;
	}

	function validate_hour($object = null, $value)
	{
		try {
			CronEvaluator::validateExpressionPart( CronEvaluator::HOUR, $value );
		}
		catch ( \Exception $ve ) {
			return Localized::ModelValidation($this->tableName(), Job::hour, $ve->getMessage() );
		}
		return null;
	}

	function validate_next($object = null, $value)
	{
		if (empty($value) ) {
			return Localized::ModelValidation($this->tableName(), Job::next, "FIELD_EMPTY");
		}

		return null;
	}

	function validate_dayOfWeek($object = null, $value)
	{
		try {
			CronEvaluator::validateExpressionPart( CronEvaluator::DAYOFWEEK, $value );
		}
		catch ( \Exception $ve ) {
			return Localized::ModelValidation($this->tableName(), Job::dayOfWeek, $ve->getMessage() );
		}
		return null;
	}

	public function attributesMandatory($object = null)
	{
		$attr = array(
			Job::dayOfWeek,
			Job::hour,
			Job::minute,
			Job::next
		);
		if ( $object != null && $object->type() != null && $object->type()->requiresEndpoint() ) {
			$attr[] = Job::endpoint_id;
		}
		return $attr;
	}

	public function attributesFor($object = null, $type = null ) {
		$attr = array();
		if ( $type != null && $type->requiresEndpoint() ) {
			$attr[Job::endpoint_id] = Model::TO_ONE_TYPE;
		}

		$attr[Job::parameter] = Model::TEXT_TYPE;
		$attr[Job::dayOfWeek] = Model::TEXT_TYPE;
		$attr[Job::minute] = Model::TEXT_TYPE;
		$attr[Job::hour] = Model::TEXT_TYPE;
		$attr[Job::enabled] = Model::FLAG_TYPE;
		$attr[Job::one_shot] = Model::FLAG_TYPE;

		return $attr;
	}

	public function attributeOptions($object = null, $type = null, $attr) {
		if ( $attr == Job::type_id ) {
			$type_model = Model::Named('Job_Type');
			return $type_model->allObjects();
		}
		else if ( $attr == Job::endpoint_id ) {
			$type_model = Model::Named('Endpoint');
			return $type_model->allObjects();
		}
		return null;
	}

	public function attributeIsEditable($object = null, $type = null, $attr) {
		if ( is_a($object, "model\\EndpointDBO" ) ) {
			if ( $attr == Job::type_id ) {
				return false;
			}
		}
		return true;
	}

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) == false || is_null($object) == true) {
			if ( isset($type) && is_a($type, "model\\Job_TypeDBO" ) ) {
				switch ($attr) {
// 					case Endpoint::base_url:
// 						return $type->api_url;
// 					case Endpoint::name:
// 						return $type->name;
					case Job::one_shot:
						return Model::TERTIARY_FALSE;
					case Job::enabled:
						return Model::TERTIARY_TRUE;
				}
			}
		}
		return parent::attributeDefaultValue($object, $type, $attr);
	}

	public function attributeRestrictionMessage($object = null, $type = null, $attr)
	{
// 		if ( $attr == Job::type_id ) {
// 			if ( isset($type, $type->comments) && is_a($type, "model\\Job_TypeDBO" ) ) {
// 				return $type->comments;
// 			}
// 		}

		return null;
	}
}

