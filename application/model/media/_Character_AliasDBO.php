<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

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


	// to-one relationship
	public function character()
	{
		if ( isset( $this->character_id ) ) {
			$model = Model::Named('Character');
			return $model->objectForId($this->character_id);
		}
		return false;
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
