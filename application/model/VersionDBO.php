<?php

namespace model;

use \DataObject as DataObject;

class VersionDBO extends DataObject
{
	public $code;
	public $major;
	public $minor;
	public $patch;
	public $created;
	public $hash_code;
}

