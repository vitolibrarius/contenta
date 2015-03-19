<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;

class Job_Running extends Model
{
	const TABLE =			'job_running';
	const id =				'id';
	const job_id =			'job_id';
	const job_type_id =		'job_type_id';
	const trace =			'trace';
	const trace_id =		'trace_id';
	const context =			'context';
	const context_id =		'context_id';
	const created =			'created';
	const pid =				'pid';

	public function tableName() { return Job_Running::TABLE; }
	public function tablePK() { return Job_Running::id; }
	public function sortOrder() { return array(Job_Running::created); }

	public function allColumnNames()
	{
		return array(
			Job_Running::id, Job_Running::job_id, Job_Running::job_type_id,
			Job_Running::trace, Job_Running::trace_id,
			Job_Running::context, Job_Running::context_id,
			Job_Running::created, Job_Running::pid
		);
	}

	public function allForJob($job = null)
	{
		return $this->fetchAll(Job_Running::TABLE, $this->allColumns(), array(Job_Running::job_id => $job->id));
	}

	public function allForJobTypeCode($code = null)
	{
		$type_model = Model::Named("Job_Type");
		$type = $type_model->jobTypeForCode($code);
		return $this->allForJobType($type);
	}

	public function allForJobType($obj = null)
	{
		return $this->fetchAll(Job_Running::TABLE, $this->allColumns(), array(Job_Running::job_type_id => $obj->id));
	}

	public function createForCode($jobObj, $jobtype_code, $trace, $trace_id, $context, $context_id, $pid)
	{
		$type_model = loadModel("JobType");
		$type = $type_model->jobTypeForCode($jobtype_code);
		return $this->create($jobObj, $type, $trace, $trace_id, $context, $context_id, $pid);
	}

	public function create($jobObj, $jobtypeObj, $trace, $trace_id, $context, $context_id, $pid)
	{
		if ( isset($jobtypeObj, $pid) ) {
			$params = array(
				Job_Running::job_type_id => (is_a($jobtypeObj, 'model\Job_Type') ? $jobtypeObj->id : null),
				Job_Running::job_id => (is_a($jobObj, 'model\Job') ? $jobObj->id : null),
				Job_Running::trace => $trace,
				Job_Running::trace_id => $trace_id,
				Job_Running::context => $context,
				Job_Running::context_id => $context_id,
				Job_Running::created => time(),
				Job_Running::pid => $pid
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

