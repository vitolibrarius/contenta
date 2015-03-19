<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;
use model\Job_Type as Job_Type;

class JobDBO extends DataObject
{
	public $type_id;
	public $endpoint_id;
	public $minute;
	public $hour;
	public $dayOfWeek;
	public $one_shot;
	public $created;
	public $next;
	public $parameter;
	public $enabled;

	public function isEnabled() {
		return ( (empty($this->enabled) == false) && ($this->enabled == Model::TERTIARY_TRUE) );
	}

	public function displayName() {
		$type = $this->jobType();
		return (empty($type) ? 'Unknown' : $type->name);
	}

	public function jobType() {
		$type_model = Model::Named("Job_Type");
		return $type_model->objectForId($this->type_id);
	}

	public function endpoint() {
		if ( isset($this->endpoint_id) ) {
			$model = Model::Named('Endpoint');
			return $model->objectForId($this->endpoint_id);
		}
		return false;
	}
}
