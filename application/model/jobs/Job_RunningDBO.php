<?php

namespace model\jobs;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\jobs\Job_Running as Job_Running;

/* import related objects */
use \model\jobs\Job as Job;
use \model\jobs\JobDBO as JobDBO;
use \model\jobs\Job_Type as Job_Type;
use \model\jobs\Job_TypeDBO as Job_TypeDBO;

use \interfaces\ObjectProgress as ObjectProgress;
use \interfaces\ProcessStatusReporter as ProcessStatusReporter;

class Job_RunningDBO extends _Job_RunningDBO implements ProcessStatusReporter, ObjectProgress
{
	public function elapsedSeconds()
	{
		return time() - $this->created;
	}

	public function elapsedFormatted()
	{
		return formattedTimeElapsed($this->elapsedSeconds());
	}

	public function displayName() {
		$job = $this->job();
		return (empty($job) ? 'Unknown' : $job->displayName() );
	}

	public function displayDescription() {
		if ( isset( $this->desc) && empty($this->desc) == false ) {
			return $this->desc;
		}

		return $this->displayName();
	}

	public function jobType() {
		if ( isset($this->type_code)) {
			$type_model = Model::Named("Job_Type");
			return $type_model->objectForId($this->type_code);
		}
		else if ( isset($this->job_id) ) {
			$job = $this->job();
			if ( $job instanceof JobDBO ) {
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

	public function setProcessMaximum($value = 100)
	{
	}

	public function setProcessMinimum($value = 0)
	{
	}

	public function setProcessCurrent($value)
	{
	}

	public function progressMaximum()
	{
	}

	public function progressMinimum()
	{
	}

	public function progressCurrent()
	{
	}

	public function setProcessMessage($msg)
	{
		if ( isset($msg) && strlen((string)$msg) > 0 ) {
			$this->setDesc( (string)$msg );
			$this->saveChanges();
		}
	}
}

?>
