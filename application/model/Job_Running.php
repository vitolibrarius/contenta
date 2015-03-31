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

	public function allForContext($context = null, $context_id = null)
	{
		$qualifiers = array();
		if ( is_null($context) == false ) {
			$qualifiers[Job_Running::context] = $context;
		}
		if ( is_null($context_id) == false ) {
			$qualifiers[Job_Running::context_id] = $context_id;
		}
		return $this->fetchAll(Job_Running::TABLE, $this->allColumns(), $qualifiers);
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

	public function createForCode($jobObj, $jobtype_code = null, $trace, $trace_id, $context, $context_id, $pid)
	{
		$type = false;
		if ( is_null($jobtype_code) == false) {
			$type_model = Model::Named("Job_Type");
			$type = $type_model->jobTypeForCode($jobtype_code);
			if ( $type == false ) {
				$param = array(
					Job_Type::name => $jobtype_code,
					Job_Type::code => $jobtype_code,
					Job_Type::desc => "Unknown",
					Job_Type::scheduled => 0
				);
				$newObjId = $type_model->createObject($param);
				$type = $type_model->objectForId($newObjId);
			}
		}

		return $this->create($jobObj, $type, $trace, $trace_id, $context, $context_id, $pid);
	}

	public function create($jobObj, $jobtypeObj, $trace, $trace_id, $context, $context_id, $pid)
	{
		if ( isset($jobtypeObj, $pid) ) {
			$params = array(
				Job_Running::trace => $trace,
				Job_Running::trace_id => $trace_id,
				Job_Running::context => $context,
				Job_Running::context_id => $context_id,
				Job_Running::created => time(),
				Job_Running::pid => $pid
			);

			if ( isset($jobObj)  && is_a($jobObj, 'model\JobDBO')) {
				$params[Job_Running::job_id] = $jobObj->id;
			}

			if ( isset($jobtypeObj)  && is_a($jobtypeObj, 'model\Job_TypeDBO')) {
				$params[Job_Running::job_type_id] = $jobtypeObj->id;
			}

			$objectOrErrors = $this->createObject($params);
			if ( is_array($objectOrErrors) ) {
				return $objectOrErrors;
			}
			else if ($objectOrErrors != false) {
				return $this->objectForId( (string)$objectOrErrors);
			}
		}
		else {
			Logger::LogError( var_export($jobtypeObj, true));
		}

		return false;
	}

	public function clearFinishedProcesses()
	{
		$allRunning = $this->allObjects();
		if ( is_array($allRunning) ) {
			$shell = "ps " . ((PHP_OS === 'Darwin') ? ' ax ' : '') . "| awk '{print $1}'";
			$output = shell_exec(  $shell );
			$pids = explode(PHP_EOL, $output);

			foreach ( $allRunning as $jobrunning ) {
				if ( in_array($jobrunning->pid, $pids) == false ) {
					// process is done
					$this->deleteObject($jobrunning);
				}
			}
		}
		return true;
	}
}

