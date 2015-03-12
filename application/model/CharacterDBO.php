<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;

class characterDBO extends DataObject
{
	public $publisher_id;
	public $name;
	public $desc;
	public $realname;
	public $gender;
	public $popularity;
	public $created;
	public $xurl;
	public $xsource;
	public $xid;
	public $xupdated;

	public function displayName() {
		return $this->name;
	}

	public function publisherName() {
		$publisherObj = $this->publisher();
		if ( $publisherObj != false ) {
			return $publisherObj->displayName();
		}
		return 'Unknown';
	}

	public function publisher() {
		if ( isset($this->publisher_id) ) {
			$model = Model::Named('Publisher');
			return $model->objectForId($this->publisher_id);
		}
		return false;
	}

	public function aliases() {
		$char_model = Model::Named('Character_Alias');
		return $char_model->allForCharacter($this);
	}

	public function addAlias($name = null) {
		if ( isset($name) ) {
			$alias_model = Model::Named('Character_Alias');
			return $alias_model->create($this, $name);
		}
		return false;
	}

	public function updatePopularity() {
		return Model::Named('Character')->updatePopularity($this);
	}
}
