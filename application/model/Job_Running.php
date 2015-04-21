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
	const processor =		'processor';
	const guid =			'guid';
	const created =			'created';
	const pid =				'pid';

	public function tableName() { return Job_Running::TABLE; }
	public function tablePK() { return Job_Running::id; }
	public function sortOrder() { return array(Job_Running::created); }

	public function allColumnNames()
	{
		return array(
			Job_Running::id, Job_Running::job_id, Job_Running::job_type_id,
			Job_Running::processor, Job_Running::guid,
			Job_Running::created, Job_Running::pid
		);
	}

	public function allForJob($job = null)
	{
		return $this->fetchAll(Job_Running::TABLE, $this->allColumns(), array(Job_Running::job_id => $job->id));
	}

	public function allForProcessorGUID($processorName = null, $guid = null)
	{
		$qualifiers = array();
		if ( is_null($processorName) == false ) {
			$qualifiers[Job_Running::processor] = $processorName;
		}
		if ( is_null($guid) == false ) {
			$qualifiers[Job_Running::guid] = $guid;
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

// 	public function runningJobs( $jobid = null, $jobtype = null, $processorName = null, $guid = null )
// 	{
// 		$allForGUID = $this->fetchAll(Job_Running::TABLE, $this->allColumns(), array(Job_Running:: => $obj->id));
//
// 	}
//
// 			$options['j'],
// 			$options['t'],
// 			$options['p'],
// 			$options['g']
// 		);
//
	public function createForCode($jobObj, $jobtype_code = null, $processorName, $guid, $pid)
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

		return $this->create($jobObj, $type, $processorName, $guid, $pid);
	}

	public function create($jobObj, $jobtypeObj, $processorName, $guid, $pid)
	{
		if ( isset($jobtypeObj, $pid) ) {
			$params = array(
				Job_Running::processor => $processorName,
				Job_Running::guid => $guid,
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

