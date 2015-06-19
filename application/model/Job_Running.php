<?php

namespace model;

use \Logger as Logger;
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

	public function allForJob(model\JobDBO $job = null)
	{
		return $this->allObjectsForFK(Job_Running::job_id, $job );
	}

	public function allForProcessorGUID($processorName = null, $guid = null)
	{
		if ( isset($processorName, $guid) ) {
			return SQL::Select( $this, null, db\Qualifier::AndQualifier(
					db\Qualifier::Equals( Job_Running::processor, $processorName),
					db\Qualifier::Equals( Job_Running::guid, $guid)
				)
			)->fetchAll();
		}

		return false;
	}

	public function allForJobTypeCode($code = null)
	{
		$type_model = Model::Named("Job_Type");
		$type = $type_model->jobTypeForCode($code);
		return $this->allForJobType($type);
	}

	public function allForJobType($obj = null)
	{
		return $this->allObjectsForFK(Job_Running::job_type_id, $obj );
	}

	public function createForJob($job_id = null, $processorName, $guid, $pid)
	{
		$jobObj = null;
		if ( is_integer($job_id) ) {
			$jobObj = Model::Named("Job")->objectForId( $job_id );
			if ( $jobObj instanceof model\JobBDO ) {
				return $this->create($jobObj, $jobObj->jobType(), $processorName, $guid, $pid);
			}
		}
		return $this->createForCode($jobtype_code, $processorName, $guid, $pid);
	}

	public function createForProcessor($processorName, $guid, $pid)
	{
		if ( isset($processorName, $guid, $pid) ) {
			return $this->create(null, null, $processorName, $guid, $pid);
		}
		else {
			Logger::LogError( "No values for $processorName, $guid, $pid" );
		}
		return false;
	}

	public function createForCode($jobtype_code = null, $processorName, $guid, $pid)
	{
		$type = false;
		if ( is_string($jobtype_code) ) {
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

			return $this->create(null, $type, $processorName, $guid, $pid);
		}

		return false;
	}

	public function create($jobObj, $jobtypeObj, $processorName, $guid, $pid)
	{
		if ( isset($pid) ) {
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
			Logger::LogError( "No Process ID" );
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

