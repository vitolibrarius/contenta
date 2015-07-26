<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;
use \Config as Config;
use \Logger as Logger;

class Media_TypeDBO extends DataObject
{
	public $code;
	public $name;

	public function displayName()
	{
		return $this->name;
	}
}
