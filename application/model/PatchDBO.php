<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;

class PatchDBO extends DataObject
{
	public $name;
	public $created;
	public $version_id;
}

