<?php

namespace model;

use \Session as Session;
use \DataObject as DataObject;
use \Model as Model;
use \Localized as Localized;

class Story_Arc_Publication extends Model
{
	const TABLE =		'story_arc_publication';
	const id =			'id';
	const story_arc_id =		'story_arc_id';
	const publication_id =	'publication_id';


	public function tableName() { return Story_Arc_Publication::TABLE; }
	public function tablePK() { return Story_Arc_Publication::id; }
	public function sortOrder() { return array(Story_Arc_Publication::story_arc_id, Story_Arc_Publication::publication_id); }

	public function allColumnNames()
	{
		return array(Story_Arc_Publication::id, Story_Arc_Publication::story_arc_id, Story_Arc_Publication::publication_id);
	}

	public function joinForStory_ArcAndPublication($story_arc, $publication)
	{
		if (isset($story_arc, $story_arc->id, $publication, $publication->id)) {
			return $this->fetch(Story_Arc_Publication::TABLE,
				$this->allColumns(),
				array(
					Story_Arc_Publication::story_arc_id => $story_arc->id,
					Story_Arc_Publication::publication_id => $publication->id
				)
			);
		}

		return false;
	}

	public function allForStory_Arc($obj)
	{
		return $this->fetchAll(Story_Arc_Publication::TABLE,
			$this->allColumns(),
			array(Story_Arc_Publication::story_arc_id => $obj->id),
			array(Story_Arc_Publication::publication_id)
		);
	}

	public function allForPublication($obj)
	{
		return $this->fetchAll(Story_Arc_Publication::TABLE,
			$this->allColumns(),
			array(Story_Arc_Publication::publication_id => $obj->id),
			array(Story_Arc_Publication::story_arc_id)
		);
	}

	public function countForPublication( model\PublicationDBO $obj = null)
	{
		if ( is_null($obj) == false ) {
			return $this->countForQualifier(Story_Arc_Publication::TABLE, array(Story_Arc_Publication::publication_id => $obj->id) );
		}
		return false;
	}

	public function create($story_arc, $publication)
	{
		if (isset($story_arc, $story_arc->id, $publication, $publication->id)) {
			$join = $this->joinForStory_ArcAndPublication($story_arc, $publication);
			if ($join == false) {
				$newObjId = $this->createObj(Story_Arc_Publication::TABLE, array(
					Story_Arc_Publication::publication_id => $publication->id,
					Story_Arc_Publication::story_arc_id => $story_arc->id
					)
				);
				$join = ($newObjId != false ? $this->objectForId($newObjId) : false);
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

	public function deleteAllForPublication($obj)
	{
		$success = true;
		if ( $obj != false )
		{
			$array = $this->allForPublication($obj);
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
