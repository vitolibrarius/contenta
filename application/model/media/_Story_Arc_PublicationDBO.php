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


	public function pkValue()
	{
		return $this->{Story_Arc_Publication::id};
	}

	public function modelName()
	{
		return "Story_Arc_Publication";
	}

	public function dboName()
	{
		return "\model\media\Story_Arc_PublicationDBO";
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
			parent::storeChange( Story_Arc_Publication::story_arc_id, $obj->id );
			$this->saveChanges();
		}
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

	public function setPublication(PublicationDBO $obj = null)
	{
		if ( isset($obj, $obj->id) && (isset($this->publication_id) == false || $obj->id != $this->publication_id) ) {
			parent::storeChange( Story_Arc_Publication::publication_id, $obj->id );
			$this->saveChanges();
		}
	}


	/** Attributes */

}

?>
