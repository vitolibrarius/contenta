<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

class Publication_CharacterDBO extends DataObject
{
	public $publication_id;
	public $character_id;

	public function publication() {
		if ( isset($this->publication_id) ) {
			$model = Model::Named('Publication');
			return $model->objectForId($this->publication_id);
		}
		return false;
	}

	public function character() {
		if ( isset($this->character_id) ) {
			$model = Model::Named('Character');
			return $model->objectForId($this->character_id);
		}
		return false;
	}
}
