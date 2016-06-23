<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \db\Qualifier as Qualifier;

use \model\media\Character as Character;

/* import related objects */
use \model\media\Character_Alias as Character_Alias;
use \model\media\Character_AliasDBO as Character_AliasDBO;
use \model\media\Publisher as Publisher;
use \model\media\PublisherDBO as PublisherDBO;
use \model\media\Series_Character as Series_Character;
use \model\media\Story_Arc_Character as Story_Arc_Character;
use \model\media\Publication_Character as Publication_Character;

class CharacterDBO extends _CharacterDBO
{
	public function publisherName() {
		$publisherObj = $this->publisher();
		if ( $publisherObj != false ) {
			return $publisherObj->displayName();
		}
		return 'Unknown';
	}

	public function addAlias($name = null) {
		if ( isset($name) ) {
			$alias_model = Model::Named('Character_Alias');
			return $alias_model->createAlias($this, $name);
		}
		return false;
	}

	public function series($limit = null) {
		$select = \SQL::SelectJoin( Model::Named("Series") );
		$select->joinOn(
			Model::Named("Series"),
			Model::Named("Series_Character"),
			null,
			Qualifier::FK( Series_Character::character_id, $this)
		);
		$select->limit = $limit;
		return $select->fetchAll();
	}

	public function joinToSeries(\model\media\SeriesDBO $object) {
		$model = Model::Named('Series_Character');
		return $model->createJoin($object, $this);
	}

	public function story_arcs($limit = null) {
		$select = \SQL::SelectJoin( Model::Named("Story_Arc") );
		$select->joinOn( Model::Named("Story_Arc"), Model::Named("Story_Arc_Character"), null,
			Qualifier::FK( Story_Arc_Character::character_id, $this)
		);
		$select->limit = $limit;
		return $select->fetchAll();
	}

	public function joinToStory_Arc(\model\media\Story_ArcDBO $object) {
		$model = Model::Named('Story_Arc_Character');
		return $model->createJoin($object, $this);
	}

	public function publications($limit = null) {
		$select = \SQL::SelectJoin( Model::Named("Publication") );
		$select->joinOn( Model::Named("Publication"), Model::Named("Publication_Character"), null,
			Qualifier::FK( Publication_Character::character_id, $this)
		);
		$select->limit = $limit;
		return $select->fetchAll();
	}

	public function joinToPublication(\model\media\PublicationDBO $object) {
		$model = Model::Named('Publication_Character');
		return $model->createJoin($object, $this);
	}
}

?>
