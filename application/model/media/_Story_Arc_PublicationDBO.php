<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\media\Story_Arc_Publication as Story_Arc_Publication;

/* import related objects */
use \model\media\Story_Arc as Story_Arc;
use \model\media\Story_ArcDBO as Story_ArcDBO;
use \model\media\Publication as Publication;
use \model\media\PublicationDBO as PublicationDBO;

abstract class _Story_Arc_PublicationDBO extends DataObject
{
	public $story_arc_id;
	public $publication_id;



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
	public function publication()
	{
		if ( isset( $this->publication_id ) ) {
			$model = Model::Named('Publication');
			return $model->objectForId($this->publication_id);
		}
		return false;
	}


	/** Attributes */

}

?>
