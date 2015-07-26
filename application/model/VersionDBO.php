<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

class VersionDBO extends DataObject
{
	public $code;
	public $major;
	public $minor;
	public $patch;
	public $created;
	public $hash_code;

	public function patches() {
		return Model::Named("Patch")->patchesForVersion($this);
	}

	public function displayName() {
		return $this->code;
	}
}

