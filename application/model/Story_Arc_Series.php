<?php

namespace model;

use \Session as Session;
use \DataObject as DataObject;
use \Model as Model;
use \Localized as Localized;

use db\Qualifier as Qualifier;

class Story_Arc_Series extends Model
{
	const TABLE =		'story_arc_series';
	const id =			'id';
	const story_arc_id =		'story_arc_id';
	const series_id =	'series_id';


	public function tableName() { return Story_Arc_Series::TABLE; }
	public function tablePK() { return Story_Arc_Series::id; }
	public function sortOrder() { return array(Story_Arc_Series::story_arc_id, Story_Arc_Series::series_id); }

	public function allColumnNames()
	{
		return array(Story_Arc_Series::id, Story_Arc_Series::story_arc_id, Story_Arc_Series::series_id);
	}

	public function joinForStory_ArcAndSeries($story_arc, $series)
	{
		if (isset($story_arc, $story_arc->id, $series, $series->id)) {
			$join = Qualifier::AndQualifier(
				Qualifier::FK( Story_Arc_Series::story_arc_id, $story_arc ),
				Qualifier::FK( Story_Arc_Series::series_id, $series )
			);
			return $this->singleObject( $join );
		}

		return false;
	}

	public function allForStory_Arc(model\Story_ArcDBO $obj)
	{
		return $this->allObjectsForFK(Story_Arc_Series::story_arc_id, $obj);
	}

	public function allForSeries(model\SeriesDBO $obj)
	{
		return $this->allObjectsForFK(Story_Arc_Series::series_id, $obj);
	}

	public function countForSeries( model\SeriesDBO $obj = null)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Story_Arc_Series::series_id, $obj );
		}
		return false;
	}

	public function create($story_arc, $series)
	{
		if (isset($story_arc, $story_arc->id, $series, $series->id)) {
			$join = $this->joinForStory_ArcAndSeries($story_arc, $series);
			if ($join == false) {
				$params = array(
					Story_Arc_Series::series_id => $series->id,
					Story_Arc_Series::story_arc_id => $story_arc->id
				);

				list( $join, $errorList ) = $this->createObject($params);
				if ( is_array($errorList) ) {
					return $errorList;
				}
			}

			return $join;
		}

		return false;
	}

	public function deleteAllForStory_Arc($obj)
	{
		$success = true;
		if ( $obj != false )
		{
			$array = $this->allForStory_Arc($obj);
			foreach ($array as $key => $value) {
				if ($this->deleteObject($value) == false) {
					$success = false;
					break;
				}
			}
		}
		return $success;
	}
}

?>
