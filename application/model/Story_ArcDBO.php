<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;
use \Config as Config;

use model\Publisher as Publisher;
use model\Character as Character;
use model\Series as Series;

use db\Qualifier as Qualifier;

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
	public $pub_active;
	public $pub_cycle;
	public $pub_count;
	public $pub_available;
	public $pub_wanted;

	public function displayName() {
		return $this->name;
	}

	public function isActive() {
		return (isset($this->pub_active) && $this->pub_active == Model::TERTIARY_TRUE);
	}

	public function isWanted() {
		return (isset($this->pub_wanted) && $this->pub_wanted == Model::TERTIARY_TRUE);
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

	public function setPublisher( model\PublisherDBO $pubObj )
	{
		if ( isset($pubObj, $pubObj->id) && (isset($this->publisher_id) == false || $pubObj->id != $this->publisher_id) ) {
			$updates = array();
			$updates[Character::publisher_id] = $pubObj->id;
			$this->model()->updateObject($this, $updates );
		}
	}

	public function characters($limit = null) {
		$select = \SQL::SelectJoin( Model::Named("Character") );
		$select->joinOn( Model::Named("Character"), Model::Named("Story_Arc_Character"), null,
			Qualifier::FK( Story_Arc_Character::story_arc_id, $this)
		);
		$select->limit($limit);
		$select->orderBy( Model::Named("Character"), Character::popularity, "desc");
		return $select->fetchAll();
	}

	public function joinToCharacter(model\CharacterDBO $character) {
		$model = Model::Named('Story_Arc_Character');
		return $model->create($this, $character);
	}

	public function series($limit = null) {
		$select = \SQL::SelectJoin( Model::Named("Series") );
		$select->joinOn( Model::Named("Series"), Model::Named("Story_Arc_Series"), null,
			Qualifier::FK( Story_Arc_Series::story_arc_id, $this)
		);
		$select->limit($limit);
		$select->orderBy( Model::Named("Series"), Series::popularity, "desc");
		return $select->fetchAll();
	}

	public function joinToSeries(model\SeriesDBO $series) {
		$model = Model::Named('Story_Arc_Series');
		return $model->create($this, $series);
	}

	public function publications($limit = null) {
		$select = \SQL::SelectJoin( Model::Named("Publication") );
		$select->joinOn( Model::Named("Publication"), Model::Named("Story_Arc_Publication"), null,
			Qualifier::FK( Story_Arc_Publication::story_arc_id, $this)
		);
		$select->limit($limit);
		$select->orderBy( Model::Named("Publication"), Publication::name);
		return $select->fetchAll();
	}

	public function joinToPublication(model\PublicationDBO $publication) {
		$model = Model::Named('Story_Arc_Publication');
		return $model->create($this, $publication);
	}
}

