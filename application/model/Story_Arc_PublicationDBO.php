<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

class Story_Arc_PublicationDBO extends DataObject
{
	public $story_arc_id;
	public $publication_id;

	public function story_arc() {
		if ( isset($this->story_arc_id) ) {
			$model = Model::Named('Story_Arc');
			return $model->objectForId($this->story_arc_id);
		}
		return false;
	}

	public function publication() {
		if ( isset($this->publication_id) ) {
			$model = Model::Named('Publication');
			return $model->objectForId($this->publication_id);
		}
		return false;
	}
}
