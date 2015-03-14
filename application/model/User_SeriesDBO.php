<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;

class User_SeriesDBO extends DataObject
{
	public $user_id;
	public $series_id;
	public $favorite;
	public $read;
	public $mislabeled;

	public function user() {
		if ( isset($this->user_id) ) {
			$model = Model::Named('User');
			return $model->objectForId($this->user_id);
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

	public function flag($read = null, $label = null) {
		$join_model = Model::Named('User_Series');
		return $join_model->flagJoin($this, $read, $label);
	}
}
