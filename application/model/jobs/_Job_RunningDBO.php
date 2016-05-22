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

abstract class _Job_RunningDBO extends DataObject
{
	public $job_id;
	public $job_type_id;
	public $processor;
	public $guid;
	public $pid;
	public $desc;
	public $created;


	public function formattedDateTime_created() { return $this->formattedDate( Job_Running::created, "M d, Y H:i" ); }
	public function formattedDate_created() {return $this->formattedDate( Job_Running::created, "M d, Y" ); }


	// to-one relationship
	public function job()
	{
		if ( isset( $this->job_id ) ) {
			$model = Model::Named('Job');
			return $model->objectForId($this->job_id);
		}
		return false;
	}

	// to-one relationship
	public function jobType()
	{
		if ( isset( $this->job_type_id ) ) {
			$model = Model::Named('Job_Type');
			return $model->objectForId($this->job_type_id);
		}
		return false;
	}

}

?>
