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
	public $last_run;
	public $parameter;
	public $enabled;
	public $elapsed;

	public $uuid;

	public function elapsedFormatted()
	{
		return formattedTimeElapsed($this->elapsed);
	}

	public function nextDate() {
		return $this->formattedDate( Job::next, "M d, Y H:i" );
	}

	public function lastDate() {
		return $this->formattedDate( Job::last_run, "M d, Y H:i" );
	}

	public function isEnabled() {
		return ( (empty($this->enabled) == false) && ($this->enabled == Model::TERTIARY_TRUE) );
	}

	public function displayName() {
		$type = $this->jobType();
		return (empty($type) ? 'Unknown' : $type->name);
	}

	public function displayDescription()
	{
		return $this->{"jobType/name"}() . " " . $this->{"endpoint/name"}();
	}

	public function jobType() {
		$type_model = Model::Named("Job_Type");
		return (isset($this->type_id) ? $type_model->objectForId($this->type_id) : null);
	}

	public function jsonParameters() {
		$jsonData = array();
		$jobType = $this->jobType();
		if ( $jobType instanceof model\Job_TypeDBO ) {
			$jsonData = $jobType->jsonParameters();
		}

		if ( isset($this->parameter) ) {
			$override = json_decode($this->parameter, true);
			if ( json_last_error() != 0 ) {
				return jsonErrorString(json_last_error()) . "'" . $this->parameter . "'";
			}

			if (is_array($override) ) {
				foreach( $override as $key=>$value ) {
					$jsonData[$key] = $value;
				}
			}
		}
		return (isset($jsonData) ? $jsonData : array());
	}

	public function uuidParameter() {
		if ( isset($this->uuid) ) {
			return $this->uuid;
		}

		$jsonData = $this->jsonParameters();
		$this->uuid = (isset($jsonData["uuid"]) ? $jsonData["uuid"] : uuid());
		return $this->uuid;
	}

	public function endpoint() {
		if ( isset($this->endpoint_id) ) {
			$model = Model::Named('Endpoint');
			return $model->objectForId($this->endpoint_id);
		}
		return false;
	}
}
