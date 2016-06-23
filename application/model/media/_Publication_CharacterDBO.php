<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\media\Publication_Character as Publication_Character;

/* import related objects */
use \model\media\Publication as Publication;
use \model\media\PublicationDBO as PublicationDBO;
use \model\media\Character as Character;
use \model\media\CharacterDBO as CharacterDBO;

abstract class _Publication_CharacterDBO extends DataObject
{
	public $publication_id;
	public $character_id;



	// to-one relationship
	public function publication()
	{
		if ( isset( $this->publication_id ) ) {
			$model = Model::Named('Publication');
			return $model->objectForId($this->publication_id);
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
