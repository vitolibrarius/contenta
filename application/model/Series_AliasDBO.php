<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

class Series_AliasDBO extends DataObject
{
	public $series_id;
	public $name;

	public function series() {
		if (isset($this->series_id)) {
			$model = Model::Named('Series');
			return $model->objectForId($this->series_id);
		}
		return false;
	}
}

