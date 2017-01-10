<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \model\media\Character_Alias as Character_Alias;

/* import related objects */
use \model\media\Character as Character;
use \model\media\CharacterDBO as CharacterDBO;

abstract class _Character_AliasDBO extends DataObject
{
	public $name;
	public $character_id;

	public function displayName()
	{
		return $this->name;
	}

	public function pkValue()
	{
		return $this->{Character_Alias::id};
	}

	public function modelName()
	{
		return "Character_Alias";
	}

	public function dboName()
	{
		return "\model\media\Character_AliasDBO";
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
			parent::storeChange( Character_Alias::character_id, $obj->id );
			$this->saveChanges();
		}
	}


	/** Attributes */
	public function name()
	{
		return parent::changedValue( Character_Alias::name, $this->name );
	}

	public function setName( $value = null)
	{
		parent::storeChange( Character_Alias::name, $value );
	}


}

?>
