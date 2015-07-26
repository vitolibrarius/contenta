<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

class Character_AliasDBO extends DataObject
{
	public $character_id;
	public $name;

	public function character() {
		if (isset($this->character_id)) {
			$char_model = Model::Named('Character');
			return $char_model->objectForId($this->character_id);
		}
		return false;
	}
}

