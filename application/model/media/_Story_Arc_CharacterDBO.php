<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\media\Story_Arc_Character as Story_Arc_Character;

/* import related objects */
use \model\media\Story_Arc as Story_Arc;
use \model\media\Story_ArcDBO as Story_ArcDBO;
use \model\media\Character as Character;
use \model\media\CharacterDBO as CharacterDBO;

abstract class _Story_Arc_CharacterDBO extends DataObject
{
	public $story_arc_id;
	public $character_id;


	public function pkValue()
	{
		return $this->{Story_Arc_Character::id};
	}

	public function modelName()
	{
		return "Story_Arc_Character";
	}

	public function dboName()
	{
		return "\model\media\Story_Arc_CharacterDBO";
	}


	// to-one relationship
	public function story_arc()
	{
		if ( isset( $this->story_arc_id ) ) {
			$model = Model::Named('Story_Arc');
			return $model->objectForId($this->story_arc_id);
		}
		return false;
	}

	public function setStory_arc(Story_ArcDBO $obj = null)
	{
		if ( isset($obj, $obj->id) && (isset($this->story_arc_id) == false || $obj->id != $this->story_arc_id) ) {
			parent::storeChange( Story_Arc_Character::story_arc_id, $obj->id );
			$this->saveChanges();
		}
	}

	// to-one relationship
	public function character()
	{
		if ( isset( $this->character_id ) ) {
			$model = Model::Named('Character');
			return $model->objectForId($this->character_id);
		}
		return false;
	}

	public function setCharacter(CharacterDBO $obj = null)
	{
		if ( isset($obj, $obj->id) && (isset($this->character_id) == false || $obj->id != $this->character_id) ) {
			parent::storeChange( Story_Arc_Character::character_id, $obj->id );
			$this->saveChanges();
		}
	}


	/** Attributes */

}

?>
