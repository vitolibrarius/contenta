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
	public $type_id;
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
		if ( isset( $this->type_id ) ) {
			$model = Model::Named('Job_Type');
			return $model->objectForId($this->type_id);
		}
		return false;
	}


	/** Attributes */
	public function job_id()
	{
		return parent::changedValue( Job_Running::job_id, $this->job_id );
	}

	public function setJob_id( $value = null)
	{
		parent::storeChange( Job_Running::job_id, $value );
	}

	public function type_id()
	{
		return parent::changedValue( Job_Running::type_id, $this->type_id );
	}

	public function setType_id( $value = null)
	{
		parent::storeChange( Job_Running::type_id, $value );
	}

	public function processor()
	{
		return parent::changedValue( Job_Running::processor, $this->processor );
	}

	public function setProcessor( $value = null)
	{
		parent::storeChange( Job_Running::processor, $value );
	}

	public function guid()
	{
		return parent::changedValue( Job_Running::guid, $this->guid );
	}

	public function setGuid( $value = null)
	{
		parent::storeChange( Job_Running::guid, $value );
	}

	public function pid()
	{
		return parent::changedValue( Job_Running::pid, $this->pid );
	}

	public function setPid( $value = null)
	{
		parent::storeChange( Job_Running::pid, $value );
	}

	public function desc()
	{
		return parent::changedValue( Job_Running::desc, $this->desc );
	}

	public function setDesc( $value = null)
	{
		parent::storeChange( Job_Running::desc, $value );
	}


}

?>
