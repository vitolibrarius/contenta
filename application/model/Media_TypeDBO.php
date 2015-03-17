<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;
use \Config as Config;

class Media_TypeDBO extends DataObject
{
	public $code;
	public $name;

	public function displayName()
	{
		return $this->name;
	}
}
