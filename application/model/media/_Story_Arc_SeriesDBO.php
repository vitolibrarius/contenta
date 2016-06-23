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
	public function series()
	{
		if ( isset( $this->series_id ) ) {
			$model = Model::Named('Series');
			return $model->objectForId($this->series_id);
		}
		return false;
	}


	/** Attributes */

}

?>
