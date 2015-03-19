<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;

class Job_RunningDBO extends DataObject
{
	public $job_type_id;
	public $job_id;
	public $trace;
	public $trace_id;
	public $context;
	public $context_id;
	public $created;
	public $pid;


	public function displayName() {
		$job = $this->job();
		return (empty($job) ? 'Unknown' : $job->displayName() );
	}

	public function job() {
		$type_model = Model::Named("Job");
		return $type_model->objectForId($this->job_id);
	}

	public function jobType() {
		$type_model = Model::Named("Job_Type");
		return $type_model->objectForId($this->job_type_id);
	}
}
