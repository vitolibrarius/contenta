<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;

class SeriesDBO extends DataObject
{
	public $publisher_id;
	public $parent_id;
	public $name;
	public $desc;
	public $created;
	public $start_year;
	public $issue_count;
	public $xurl;
	public $xsource;
	public $xid;

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

	public function seriesStartYearString() {
		$yearStr = '0000';
		if ( isset($this->start_year) ) {
			if ( is_integer($this->start_year) ) {
				$yearStr = sprintf( '%04d', $this->start_year);
			}
			else if (is_numeric($this->start_year)) {
				$yearStr = sprintf( '%04d', intval($this->start_year));
			}
		}
		return $yearStr;
	}

	public function shortDescription($maxLength = 100) {
		if ( isset($this->desc) && strlen($this->desc) > $maxLength ) {
			return substr($this->desc, 0 , $maxLength);
		}
		return $this->desc;
	}

	public function allPublications() {
		$model = Model::Named('Publication');
		return $model->allForSeries($this);
	}

	public function activePublications() {
		$model = Model::Named('Publication');
		return $model->activePublicationsForSeries($this);
	}

	public function publicationsForIssue($issue) {
		$model = Model::Named('Publication');
		return $model->activePublicationsForSeries($this, $issue);
	}

	public function aliases() {
		$alias_model = Model::Named('Series_Alias');
		return $alias_model->allForSeries($this);
	}

	public function addAlias($name = null) {
		if ( isset($name) ) {
			$alias_model = Model::Named('Series_Alias');
			return $alias_model->create($this, $name);
		}
		return false;
	}

	public function characters($limit = null) {
		$character_model = Model::Named("Character");
		$model = Model::Named("Series_Character");
		$joins = $model->allForSeries($this);

		if ( $joins != false ) {
			return $this->model()->fetchAllJoin(
				Character::TABLE,
				$character_model->allColumns(),
				Character::id, Series_Character::character_id, $joins, null,
					array("desc" => array(Character::popularity)),
				$limit
			);
		}
		return array();
	}

	public function joinToCharacter($character) {
		$model = Model::Named('Series_Character');
		return $model->create($this, $character);
	}

	public function userFavorite($userId = null) {
		$join = $this->userjoin($userId);
		if ( $join != false ) {
			return $join->favorite;
		}
		return false;
	}

	public function userJoin($userId = null) {
		if (isset($userId)) {
			$usermodel = Model::Named('Users');
			$model = Model::Named('UserSeriesJoin');
			$user = $usermodel->objectForId($userId);
			return $model->joinForUserAndSeries($user, $this);
		}
		return false;
	}
}

