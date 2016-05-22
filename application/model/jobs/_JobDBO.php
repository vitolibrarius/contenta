<?php

namespace model\jobs;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\jobs\Job as Job;

/* import related objects */
use \model\jobs\Job_Type as Job_Type;
use \model\jobs\Job_TypeDBO as Job_TypeDBO;
use \model\Endpoint as Endpoint;
use \model\EndpointDBO as EndpointDBO;

abstract class _JobDBO extends DataObject
{
	public $type_id;
	public $endpoint_id;
	public $enabled;
	public $one_shot;
	public $fail_count;
	public $elapsed;
	public $minute;
	public $hour;
	public $dayOfWeek;
	public $parameter;
	public $next;
	public $last_run;
	public $last_fail;
	public $created;


	public function isEnabled() {
		return (isset($this->enabled) && $this->enabled == Model::TERTIARY_TRUE);
	}

	public function isOne_shot() {
		return (isset($this->one_shot) && $this->one_shot == Model::TERTIARY_TRUE);
	}

	public function formattedDateTime_next() { return $this->formattedDate( Job::next, "M d, Y H:i" ); }
	public function formattedDate_next() {return $this->formattedDate( Job::next, "M d, Y" ); }

	public function formattedDateTime_last_run() { return $this->formattedDate( Job::last_run, "M d, Y H:i" ); }
	public function formattedDate_last_run() {return $this->formattedDate( Job::last_run, "M d, Y" ); }

	public function formattedDateTime_last_fail() { return $this->formattedDate( Job::last_fail, "M d, Y H:i" ); }
	public function formattedDate_last_fail() {return $this->formattedDate( Job::last_fail, "M d, Y" ); }

	public function formattedDateTime_created() { return $this->formattedDate( Job::created, "M d, Y H:i" ); }
	public function formattedDate_created() {return $this->formattedDate( Job::created, "M d, Y" ); }


	// to-one relationship
	public function jobType()
	{
		if ( isset( $this->job_type_id ) ) {
			$model = Model::Named('Job_Type');
			return $model->objectForId($this->job_type_id);
		}
		return false;
	}

	// to-one relationship
	public function endpoint()
	{
		if ( isset( $this->endpoint_id ) ) {
			$model = Model::Named('Endpoint');
			return $model->objectForId($this->endpoint_id);
		}
		return false;
	}

}

?>
