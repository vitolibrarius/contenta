<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;
use \Localized as Localized;
use \Logger as Logger;

use db\Qualifier as Qualifier;

class Story_Arc_Publication extends Model
{
	const TABLE =		'story_arc_publication';
	const id =			'id';
	const story_arc_id =	'story_arc_id';
	const publication_id =	'publication_id';


	public function tableName() { return Story_Arc_Publication::TABLE; }
	public function tablePK() { return Story_Arc_Publication::id; }
	public function sortOrder() { return array(Story_Arc_Publication::story_arc_id, Story_Arc_Publication::publication_id); }
	public function notifyKeypaths() { return array( "story_arc" ); }

	public function allColumnNames()
	{
		return array(Story_Arc_Publication::id, Story_Arc_Publication::story_arc_id, Story_Arc_Publication::publication_id);
	}

	public function joinForStory_ArcAndPublication($story_arc, $publication)
	{
		if (isset($story_arc, $story_arc->id, $publication, $publication->id)) {
			$join = Qualifier::AndQualifier(
				Qualifier::FK( Story_Arc_Publication::story_arc_id, $story_arc ),
				Qualifier::FK( Story_Arc_Publication::publication_id, $publication )
			);
			return $this->singleObject( $join );
		}

		return false;
	}

	public function allForStory_Arc(model\Story_ArcDBO $obj)
	{
		return $this->allObjectsForFK(Story_Arc_Publication::story_arc_id, $obj);
	}

	public function allForPublication(model\PublicationDBO $obj)
	{
		return $this->allObjectsForFK(Story_Arc_Publication::publication_id, $obj);
	}

	public function countForPublication( model\PublicationDBO $obj = null)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Story_Arc_Publication::publication_id, $obj );
		}
		return false;
	}

	public function create($story_arc, $publication)
	{
		if (isset($story_arc, $story_arc->id, $publication, $publication->id)) {
			$join = $this->joinForStory_ArcAndPublication($story_arc, $publication);
			if ($join == false) {
				$params = array(
					Story_Arc_Publication::publication_id => $publication->id,
					Story_Arc_Publication::story_arc_id => $story_arc->id
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
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new exceptions\DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForStory_Arc($obj);
			}
		}
		return $success;
	}

	public function deleteAllForPublication($obj)
	{
		$success = true;
		if ( $obj != false )
		{
			$array = $this->allForPublication($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new exceptions\DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForPublication($obj);
			}
		}
		return $success;
	}
}

?>
