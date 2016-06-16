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
			return $model->allObjectsForKeyValue( Job_Running::type_id, $this->id);
		}

		return false;
	}

	// to-many relationship
	public function jobs()
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Job');
			return $model->allObjectsForKeyValue( Job::type_id, $this->id);
		}

		return false;
	}


	/** Attributes */
	public function code()
	{
		return parent::changedValue( Job_Type::code, $this->code );
	}

	public function setCode( $value = null)
	{
		parent::storeChange( Job_Type::code, $value );
	}

	public function name()
	{
		return parent::changedValue( Job_Type::name, $this->name );
	}

	public function setName( $value = null)
	{
		parent::storeChange( Job_Type::name, $value );
	}

	public function desc()
	{
		return parent::changedValue( Job_Type::desc, $this->desc );
	}

	public function setDesc( $value = null)
	{
		parent::storeChange( Job_Type::desc, $value );
	}

	public function processor()
	{
		return parent::changedValue( Job_Type::processor, $this->processor );
	}

	public function setProcessor( $value = null)
	{
		parent::storeChange( Job_Type::processor, $value );
	}

	public function parameter()
	{
		return parent::changedValue( Job_Type::parameter, $this->parameter );
	}

	public function setParameter( $value = null)
	{
		parent::storeChange( Job_Type::parameter, $value );
	}

	public function scheduled()
	{
		return parent::changedValue( Job_Type::scheduled, $this->scheduled );
	}

	public function setScheduled( $value = null)
	{
		parent::storeChange( Job_Type::scheduled, $value );
	}

	public function requires_endpoint()
	{
		return parent::changedValue( Job_Type::requires_endpoint, $this->requires_endpoint );
	}

	public function setRequires_endpoint( $value = null)
	{
		parent::storeChange( Job_Type::requires_endpoint, $value );
	}


}

?>
