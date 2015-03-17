<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;
use \Config as Config;

use model\Publisher as Publisher;
use model\Character as Character;
use model\Series as Series;

class PublicationDBO extends DataObject
{
	public $name;
	public $desc;
	public $pub_date;
	public $created;
	public $issue_num;
	public $series_id;

	public $xurl;
	public $xsource;
	public $xid;
	public $xupdated;

	public function displayName() {
		return $this->name;
	}

	public function displayDescription() {
		return $this->shortDescription();
	}

	public function publishedYear() {
		if (isset($this->pub_date) && ($this->pub_date > 0)) {
			$coverDate = getdate($this->pub_date);
			if (isset($coverDate['year'])) {
				return $coverDate['year'];
			}
		}
		return 0;
	}

	public function publisher() {
		$series = $this->series();
		if ( $series != false) {
			return $series->publisher();
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

	public function media() {
		$model = Model::Named('Media');
		return $model->allForPublication($this);
	}

	public function characters($limit = null) {
		$character_model = Model::Named("Character");
		$model = Model::Named("Publication_Character");
		$joins = $model->allForPublication($this);

		if ( $joins != false ) {
			return $this->model()->fetchAllJoin(
				Character::TABLE,
				$character_model->allColumns(),
				Character::id, Publication_Character::character_id, $joins, null,
					array("desc" => array(Character::popularity)),
				$limit
			);
		}
		return array();
	}

	public function joinToCharacter($character) {
		$model = Model::Named('Publication_Character');
		return $model->create($this, $character);
	}

	public function story_arcs($limit = null) {
		$story_arcs_model = Model::Named("Story_Arc");
		$model = Model::Named("Story_Arc_Publication");
		$joins = $model->allForPublication($this);

		if ( $joins != false ) {
			return $this->model()->fetchAllJoin(
				Story_Arc::TABLE,
				$story_arcs_model->allColumns(),
				Story_Arc::id, Story_Arc_Publication::story_arc_id, $joins, null,
					array(Story_Arc::name),
				$limit
			);
		}
		return array();
	}

	public function joinToStory_Arc($object) {
		$model = Model::Named('Story_Arc_Publication');
		return $model->create($object, $this);
	}

}

