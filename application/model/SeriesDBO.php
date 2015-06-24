<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;

use db\Qualifier as Qualifier;

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
		return $this->desc;
	}

	public function publisher() {
		if ( isset($this->publisher_id) ) {
			$model = Model::Named('Publisher');
			return $model->objectForId($this->publisher_id);
		}
		return false;
	}

	public function setPublisher( model\PublisherDBO $pubObj )
	{
		if ( isset($pubObj, $pubObj->id) && (isset($this->publisher_id) == false || $pubObj->id != $this->publisher_id) ) {
			$updates = array();
			$updates[Series::publisher_id] = $pubObj->id;
			$this->model()->updateObject($this, $updates );
		}
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

	public function allPublications() {
		$model = Model::Named('Publication');
		return $model->allForSeries($this);
	}

	public function activePublications() {
		$model = Model::Named('Publication');
		return $model->activePublicationsForSeries($this);
	}

	public function publicationForExternal($issue_xid, $xsource) {
		$model = Model::Named('Publication');
		return $model->publicationForSeriesExternal($this, $issue_xid, $xsource);
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
		$select = \SQL::SelectJoin( Model::Named("Character") );
		$select->joinOn( Model::Named("Character"), Model::Named("Series_Character"), null,
			Qualifier::FK( Series_Character::series_id, $this)
		);
		$select->limit($limit);
		$select->orderBy( Model::Named("Character"), Character::popularity, "desc");
		return $select->fetchAll();
	}

	public function joinToCharacter(model\CharacterDBO $character) {
		$model = Model::Named('Series_Character');
		return $model->create($this, $character);
	}

	public function story_arcs($limit = null) {
		$select = \SQL::SelectJoin( Model::Named("Story_Arc") );
		$select->joinOn( Model::Named("Story_Arc"), Model::Named("Story_Arc_Series"), null,
			Qualifier::FK( Story_Arc_Series::series_id, $this)
		);
		$select->limit($limit);
		$select->orderBy( Model::Named("Story_Arc"), Story_Arc::name);
		return $select->fetchAll();
	}

	public function joinToStory_Arc(model\Story_ArcDBO $object) {
		$model = Model::Named('Story_Arc_Series');
		return $model->create($object, $this);
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
			$model = Model::Named('User_Series');
			$user = $usermodel->objectForId($userId);
			return $model->joinForUserAndSeries($user, $this);
		}
		return false;
	}
}

