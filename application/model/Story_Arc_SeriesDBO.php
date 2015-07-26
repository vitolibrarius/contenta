<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

class Story_Arc_SeriesDBO extends DataObject
{
	public $story_arc_id;
	public $series_id;

	public function story_arc() {
		if ( isset($this->story_arc_id) ) {
			$model = Model::Named('Story_Arc');
			return $model->objectForId($this->story_arc_id);
		}
		return false;
	}

	public function series() {
		if ( isset($this->series_id) ) {
			$model = Model::Named('Series');
			return $model->objectForId($this->series_id);
		}
		return false;
	}
}
