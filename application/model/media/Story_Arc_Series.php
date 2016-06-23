<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \db\Qualifier as Qualifier;

use \model\media\Story_Arc_SeriesDBO as Story_Arc_SeriesDBO;

/* import related objects */
use \model\media\Story_Arc as Story_Arc;
use \model\media\Story_ArcDBO as Story_ArcDBO;
use \model\media\Series as Series;
use \model\media\SeriesDBO as SeriesDBO;

class Story_Arc_Series extends _Story_Arc_Series
{
	/**
	 *	Create/Update functions
	 */
	public function createObject( array $values = array())
	{
		if ( isset($values) ) {
			// massage values as necessary
		}

		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Story_Arc_SeriesDBO ) {
			// massage values as necessary
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		$attrFor = array(
			Story_Arc_Series::story_arc_id,
			Story_Arc_Series::series_id
		);
		return array_intersect_key($this->attributesMap(),array_flip($attrFor));
	}

	/*
	public function attributeIsEditable($object = null, $type = null, $attr)
	{
		// add customization here
		return parent::attributeIsEditable($object, $type, $attr);
	}
	*/

	/*
	public function attributeRestrictionMessage($object = null, $type = null, $attr)	{ return null; }
	public function attributePlaceholder($object = null, $type = null, $attr)	{ return null; }
	*/

	/*
	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		return parent::attributeDefaultValue($object, $type, $attr);
	}
	*/

	/*
	public function attributeEditPattern($object = null, $type = null, $attr)
	{
		return null;
	}
	*/

	public function attributeOptions($object = null, $type = null, $attr)
	{
		if ( Story_Arc_Series::story_arc_id == $attr ) {
			$model = Model::Named('Story_Arc');
			return $model->allObjects();
		}
		if ( Story_Arc_Series::series_id == $attr ) {
			$model = Model::Named('Series');
			return $model->allObjects();
		}
		return null;
	}

	/** Validation */
/*
	function validate_story_arc_id($object = null, $value)
	{
		return parent::validate_story_arc_id($object, $value);
	}
*/

/*
	function validate_series_id($object = null, $value)
	{
		return parent::validate_series_id($object, $value);
	}
*/

	public function createJoin( Story_ArcDBO $storyArcObj, SeriesDBO $seriesObj )
	{
		if (isset($storyArcObj, $storyArcObj->id, $seriesObj, $seriesObj->id)) {
			$join = $this->objectForStoryArcAndSeries($storyArcObj, $seriesObj);
			if ($join == false) {
				list( $join, $errorList ) = $this->createObject(array(
					"story_arc" => $storyArcObj,
					"series" => $seriesObj
					)
				);

				if ( is_array($errorList) ) {
					return $errorList;
				}
			}

			return $join;
		}

		return false;
	}

	public function storyArcIdForAnySeriesIdArray( array $obj = null)
	{
		if ( is_array($obj) && count($obj) > 0 ) {
			$select = SQL::Select($this, array( Story_Arc_Series::story_arc_id ));
			$select->where( Qualifier::IN( Story_Arc_Series::series_id, $obj ));
// 			$select->groupBy( array( Story_Arc_Series::story_arc_id ) );
// 			$select->having( array("count(" . Story_Arc_Series::story_arc_id. ") = " . count($obj)) );

			$idArray = $select->fetchAll();
			return array_map(function($stdClass) {return $stdClass->{Story_Arc_Series::story_arc_id}; }, $idArray);
		}
		return array();
	}

}

?>
