<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use db\Qualifier as Qualifier;

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

	public function setPublisher( model\PublisherDBO $pubObj )
	{
		if ( isset($pubObj, $pubObj->id) && (isset($this->publisher_id) == false || $pubObj->id != $this->publisher_id) ) {
			$updates = array();
			$updates[Character::publisher_id] = $pubObj->id;
			$this->model()->updateObject($this, $updates );
		}
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
		$select = \SQL::SelectJoin( Model::Named("Series") );
		$select->joinOn( Model::Named("Series"), Model::Named("Series_Character"), null, Qualifier::FK( Series_Character::character_id, $this));
		$select->limit = $limit;
		return $select->fetchAll();
	}

	public function joinToSeries(model\SeriesDBO $object) {
		$model = Model::Named('Series_Character');
		return $model->create($object, $this);
	}

	public function story_arcs($limit = null) {
		$select = \SQL::SelectJoin( Model::Named("Story_Arc") );
		$select->joinOn( Model::Named("Story_Arc"), Model::Named("Story_Arc_Character"), null,
			Qualifier::FK( Story_Arc_Character::character_id, $this)
		);
		$select->limit = $limit;
		return $select->fetchAll();
	}

	public function joinToStory_Arc(model\Story_ArcDBO $object) {
		$model = Model::Named('Story_Arc_Character');
		return $model->create($object, $this);
	}

	public function publications($limit = null) {
		$select = \SQL::SelectJoin( Model::Named("Publication") );
		$select->joinOn( Model::Named("Publication"), Model::Named("Publication_Character"), null,
			Qualifier::FK( Publication_Character::character_id, $this)
		);
		$select->limit = $limit;
		return $select->fetchAll();
	}

	public function joinToPublication(model\PublicationDBO $object) {
		$model = Model::Named('Publication_Character');
		return $model->create($object, $this);
	}

	public function updatePopularity() {
		return Model::Named('Character')->updatePopularity($this);
	}
}
