<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

class Job_TypeDBO extends DataObject
{
	public $name;
	public $code;
	public $description;
	public $scheduled;
	public $processor;
	public $parameter;
	public $requires_endpoint;

	public function displayName()
	{
		return $this->name;
	}

	public function requiresEndpoint() {
		return (isset($this->requires_endpoint) && $this->requires_endpoint == Model::TERTIARY_TRUE);
	}

	public function canBeScheduled() {
		return (isset($this->scheduled) && $this->scheduled == Model::TERTIARY_TRUE);
	}

	public function jsonParameters() {
		if ( isset($this->parameter) ) {
			$jsonData = json_decode($this->parameter, true);
			if ( json_last_error() != 0 ) {
				throw new \Exception( jsonErrorString(json_last_error()) . "'" . $this->parameter . "'" );
			}
		}
		return (isset($jsonData) ? $jsonData : array());
	}
}
