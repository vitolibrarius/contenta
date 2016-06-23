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



	// to-one relationship
	public function story_arc()
	{
		if ( isset( $this->story_arc_id ) ) {
			$model = Model::Named('Story_Arc');
			return $model->objectForId($this->story_arc_id);
		}
		return false;
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

}

?>
