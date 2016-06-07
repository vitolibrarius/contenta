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


	/** Attributes */
	public function type_id()
	{
		return parent::changedValue( Job::type_id, $this->type_id );
	}

	public function setType_id( $value = null)
	{
		parent::storeChange( Job::type_id, $value );
	}

	public function endpoint_id()
	{
		return parent::changedValue( Job::endpoint_id, $this->endpoint_id );
	}

	public function setEndpoint_id( $value = null)
	{
		parent::storeChange( Job::endpoint_id, $value );
	}

	public function enabled()
	{
		return parent::changedValue( Job::enabled, $this->enabled );
	}

	public function setEnabled( $value = null)
	{
		parent::storeChange( Job::enabled, $value );
	}

	public function one_shot()
	{
		return parent::changedValue( Job::one_shot, $this->one_shot );
	}

	public function setOne_shot( $value = null)
	{
		parent::storeChange( Job::one_shot, $value );
	}

	public function fail_count()
	{
		return parent::changedValue( Job::fail_count, $this->fail_count );
	}

	public function setFail_count( $value = null)
	{
		parent::storeChange( Job::fail_count, $value );
	}

	public function elapsed()
	{
		return parent::changedValue( Job::elapsed, $this->elapsed );
	}

	public function setElapsed( $value = null)
	{
		parent::storeChange( Job::elapsed, $value );
	}

	public function minute()
	{
		return parent::changedValue( Job::minute, $this->minute );
	}

	public function setMinute( $value = null)
	{
		parent::storeChange( Job::minute, $value );
	}

	public function hour()
	{
		return parent::changedValue( Job::hour, $this->hour );
	}

	public function setHour( $value = null)
	{
		parent::storeChange( Job::hour, $value );
	}

	public function dayOfWeek()
	{
		return parent::changedValue( Job::dayOfWeek, $this->dayOfWeek );
	}

	public function setDayOfWeek( $value = null)
	{
		parent::storeChange( Job::dayOfWeek, $value );
	}

	public function parameter()
	{
		return parent::changedValue( Job::parameter, $this->parameter );
	}

	public function setParameter( $value = null)
	{
		parent::storeChange( Job::parameter, $value );
	}

	public function next()
	{
		return parent::changedValue( Job::next, $this->next );
	}

	public function setNext( $value = null)
	{
		parent::storeChange( Job::next, $value );
	}

	public function last_run()
	{
		return parent::changedValue( Job::last_run, $this->last_run );
	}

	public function setLast_run( $value = null)
	{
		parent::storeChange( Job::last_run, $value );
	}

	public function last_fail()
	{
		return parent::changedValue( Job::last_fail, $this->last_fail );
	}

	public function setLast_fail( $value = null)
	{
		parent::storeChange( Job::last_fail, $value );
	}

	public function created()
	{
		return parent::changedValue( Job::created, $this->created );
	}

	public function setCreated( $value = null)
	{
		parent::storeChange( Job::created, $value );
	}


}

?>