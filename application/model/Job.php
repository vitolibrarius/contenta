<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;

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
	const parameter =	'parameter';
	const enabled =		'enabled';


	public function tableName() { return Job::TABLE; }
	public function tablePK() { return Job::id; }
	public function sortOrder() { return array(Job::type_id, Job::next); }

	public function allColumnNames()
	{
		return array(
			Job::id, Job::type_id, Job::endpoint_id,
			Job::minute, Job::hour, Job::dayOfWeek, Job::parameter,
			Job::created, Job::next, Job::one_shot, Job::enabled
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
		return $this->fetchAll(Job::TABLE, $this->allColumns(), array(Job::type_id => $obj->id), array(Job::next));
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
}
?>
