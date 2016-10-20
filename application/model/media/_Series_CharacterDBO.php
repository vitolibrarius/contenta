<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\media\Series_Character as Series_Character;

/* import related objects */
use \model\media\Series as Series;
use \model\media\SeriesDBO as SeriesDBO;
use \model\media\Character as Character;
use \model\media\CharacterDBO as CharacterDBO;

abstract class _Series_CharacterDBO extends DataObject
{
	public $series_id;
	public $character_id;


	public function pkValue()
	{
		return $this->{Series_Character::id};
	}

	public function modelName()
	{
		return "Series_Character";
	}

	public function dboName()
	{
		return "\model\media\Series_CharacterDBO";
	}


	// to-one relationship
	public function series()
	{
		if ( isset( $this->series_id ) ) {
			$model = Model::Named('Series');
			return $model->objectForId($this->series_id);
		}
		return false;
	}

	public function setSeries(SeriesDBO $obj = null)
	{
		if ( isset($obj, $obj->id) && (isset($this->series_id) == false || $obj->id != $this->series_id) ) {
			parent::storeChange( Series_Character::series_id, $obj->id );
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
			parent::storeChange( Series_Character::character_id, $obj->id );
			$this->saveChanges();
		}
	}


	/** Attributes */

}

?>
