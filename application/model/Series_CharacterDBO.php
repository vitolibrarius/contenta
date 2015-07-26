<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

class Series_CharacterDBO extends DataObject
{
	public $series_id;
	public $character_id;

	public function series() {
		if ( isset($this->series_id) ) {
			$model = Model::Named('Series');
			return $model->objectForId($this->series_id);
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
