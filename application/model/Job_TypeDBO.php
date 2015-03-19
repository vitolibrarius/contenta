<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;

class Job_TypeDBO extends DataObject
{
	public $name;
	public $code;
	public $description;
	public $scheduled;

	public function displayName()
	{
		return $this->name;
	}

	public function canBeScheduled() {
		return (isset($this->scheduled) && $this->scheduled == 1);
	}
}
