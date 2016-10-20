<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\media\Story_Arc_Series as Story_Arc_Series;

/* import related objects */
use \model\media\Story_Arc as Story_Arc;
use \model\media\Story_ArcDBO as Story_ArcDBO;
use \model\media\Series as Series;
use \model\media\SeriesDBO as SeriesDBO;

abstract class _Story_Arc_SeriesDBO extends DataObject
{
	public $story_arc_id;
	public $series_id;


	public function pkValue()
	{
		return $this->{Story_Arc_Series::id};
	}

	public function modelName()
	{
		return "Story_Arc_Series";
	}

	public function dboName()
	{
		return "\model\media\Story_Arc_SeriesDBO";
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
			parent::storeChange( Story_Arc_Series::story_arc_id, $obj->id );
			$this->saveChanges();
		}
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
			parent::storeChange( Story_Arc_Series::series_id, $obj->id );
			$this->saveChanges();
		}
	}


	/** Attributes */

}

?>
