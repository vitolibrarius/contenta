<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;

class Job_RunningDBO extends DataObject
{
	public $job_type_id;
	public $job_id;
	public $processor;
	public $guid;
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
		if ( isset($this->job_type_id)) {
			$type_model = Model::Named("Job_Type");
			return $type_model->objectForId($this->job_type_id);
		}
		else if ( isset($this->job_id) ) {
			$job = $this->job();
			if ( $job instanceof model\JobDBO ) {
				return $job->jobType();
			}
		}
		return null;
	}

	private function isRunning() {
		$shell = "ps " . ((PHP_OS === 'Darwin') ? ' ax ' : '') . "| awk '{print $1}'";
		$output = shell_exec(  $shell );
		$pids = explode(PHP_EOL, $output);
		return in_array($this->pid, $pids);
	}
}
