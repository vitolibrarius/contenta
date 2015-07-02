<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;

class PatchDBO extends DataObject
{
	public $name;
	public $created;
	public $version_id;

	public function displayName() {
		return $this->name;
	}

	public function version() {
		if ( isset($this->version_id) ) {
			$model = Model::Named('Version');
			return $model->objectForId($this->version_id);
		}
		return false;
	}

	public function createdDate() {
		return $this->formattedDate( Patch::created, "M d, Y H:i" );
	}
}

