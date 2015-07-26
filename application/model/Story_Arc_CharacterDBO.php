<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

class Story_Arc_CharacterDBO extends DataObject
{
	public $story_arc_id;
	public $character_id;

	public function story_arc() {
		if ( isset($this->story_arc_id) ) {
			$model = Model::Named('Story_Arc');
			return $model->objectForId($this->story_arc_id);
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
