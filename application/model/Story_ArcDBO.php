<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;
use \Config as Config;

use model\Publisher as Publisher;
use model\Character as Character;
use model\Series as Series;

class Story_ArcDBO extends DataObject
{
	public $name;
	public $created;
	public $desc;
	public $xurl;
	public $xsource;
	public $xid;
	public $xupdated;
	public $publisher_id;

	public function displayName() {
		return $this->name;
	}

	public function displayDescription() {
		return $this->shortDescription();
	}

	public function publisher() {
		if ( isset($this->publisher_id) ) {
			$model = Model::Named('Publisher');
			return $model->objectForId($this->publisher_id);
		}
		return false;
	}

	public function characters($limit = null) {
		$character_model = Model::Named("Character");
		$model = Model::Named("Story_Arc_Character");
		$joins = $model->allForStory_Arc($this);

		if ( $joins != false ) {
			return $this->model()->fetchAllJoin(
				Character::TABLE,
				$character_model->allColumns(),
				Character::id, Story_Arc_Character::character_id, $joins, null,
					array("desc" => array(Character::popularity)),
				$limit
			);
		}
		return array();
	}

	public function joinToCharacter($character) {
		$model = Model::Named('Story_Arc_Character');
		return $model->create($this, $character);
	}

	public function series($limit = null) {
		$series_model = Model::Named("Series");
		$model = Model::Named("Story_Arc_Series");
		$joins = $model->allForStory_Arc($this);

		if ( $joins != false ) {
			return $this->model()->fetchAllJoin(
				Series::TABLE,
				$series_model->allColumns(),
				Series::id, Story_Arc_Series::series_id, $joins, null,
					array("desc" => array(Series::popularity)),
				$limit
			);
		}
		return array();
	}

	public function joinToSeries($series) {
		$model = Model::Named('Story_Arc_Series');
		return $model->create($this, $series);
	}
}

