<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;

class CharacterDBO extends DataObject
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

	public function series($limit = null) {
		$series_model = Model::Named("Series");
		$model = Model::Named("Series_Character");
		$joins = $model->allForCharacter($this);

		if ( $joins != false ) {
			return $this->model()->fetchAllJoin(
				Series::TABLE,
				$series_model->allColumns(),
				Series::id, Series_Character::series_id, $joins, null,
					array(Series::name),
				$limit
			);
		}
		return array();
	}

	public function joinToSeries($object) {
		$model = Model::Named('Series_Character');
		return $model->create($object, $this);
	}

	public function story_arcs($limit = null) {
		$story_arcs_model = Model::Named("Story_Arc");
		$model = Model::Named("Story_Arc_Character");
		$joins = $model->allForCharacter($this);

		if ( $joins != false ) {
			return $this->model()->fetchAllJoin(
				Story_Arc::TABLE,
				$story_arcs_model->allColumns(),
				Story_Arc::id, Story_Arc_Character::story_arc_id, $joins, null,
					array(Story_Arc::name),
				$limit
			);
		}
		return array();
	}

	public function joinToStory_Arc($object) {
		$model = Model::Named('Story_Arc_Character');
		return $model->create($object, $this);
	}

	public function publications($limit = null) {
		$publications_model = Model::Named("Publication");
		$model = Model::Named("Publication_Character");
		$joins = $model->allForCharacter($this);

		if ( $joins != false ) {
			return $this->model()->fetchAllJoin(
				Publication::TABLE,
				$publications_model->allColumns(),
				Publication::id, Publication_Character::publication_id, $joins, null,
					array(Publication::name),
				$limit
			);
		}
		return array();
	}

	public function joinToPublication($object) {
		$model = Model::Named('Publication_Character');
		return $model->create($object, $this);
	}

	public function updatePopularity() {
		return Model::Named('Character')->updatePopularity($this);
	}
}
