<?php

namespace model\jobs;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\jobs\Job_Type as Job_Type;

/* import related objects */
use \model\jobs\Job_Running as Job_Running;
use \model\jobs\Job_RunningDBO as Job_RunningDBO;
use \model\jobs\Job as Job;
use \model\jobs\JobDBO as JobDBO;

abstract class _Job_TypeDBO extends DataObject
{
	public $code;
	public $name;
	public $desc;
	public $processor;
	public $parameter;
	public $scheduled;
	public $requires_endpoint;

	public function displayName()
	{
		return $this->name;
	}

	public function isScheduled() {
		return (isset($this->scheduled) && $this->scheduled == Model::TERTIARY_TRUE);
	}

	public function isRequires_endpoint() {
		return (isset($this->requires_endpoint) && $this->requires_endpoint == Model::TERTIARY_TRUE);
	}


	// to-many relationship
	public function jobsRunning()
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Job_Running');
			return $model->allObjectsForKeyValue( Job_Running::job_type_id, $this->id);
		}

		return false;
	}

	// to-many relationship
	public function jobs()
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Job');
			return $model->allObjectsForKeyValue( Job::job_type_id, $this->id);
		}

		return false;
	}

}

?>
