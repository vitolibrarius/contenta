<?php

namespace model;

use \http\Session as Session;;
use \DataObject as DataObject;
use \Model as Model;
use \Localized as Localized;
use \Logger as Logger;

use \SQL as SQL;
use db\Qualifier as Qualifier;

class Story_Arc_Character extends Model
{
	const TABLE =		'story_arc_character';
	const id =			'id';
	const story_arc_id =	'story_arc_id';
	const character_id =	'character_id';


	public function tableName() { return Story_Arc_Character::TABLE; }
	public function tablePK() { return Story_Arc_Character::id; }
	public function sortOrder() { return array(Story_Arc_Character::story_arc_id, Story_Arc_Character::character_id); }

	public function allColumnNames()
	{
		return array(Story_Arc_Character::id, Story_Arc_Character::story_arc_id, Story_Arc_Character::character_id);
	}

	public function joinForStory_ArcAndCharacter($story_arc, $character)
	{
		if (isset($story_arc, $story_arc->id, $character, $character->id)) {
			$join = Qualifier::AndQualifier(
				Qualifier::FK( Story_Arc_Character::story_arc_id, $story_arc ),
				Qualifier::FK( Story_Arc_Character::character_id, $character )
			);
			return $this->singleObject( $join );
		}

		return false;
	}

	public function allForStory_Arc(model\Story_ArcDBO $obj)
	{
		return $this->allObjectsForFK(Story_Arc_Character::story_arc_id, $obj);
	}

	public function allForCharacter(model\CharacterDBO $obj)
	{
		return $this->allObjectsForFK(Story_Arc_Character::character_id, $obj);
	}

	public function countForCharacter( model\CharacterDBO $obj = null)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Story_Arc_Character::character_id, $obj );
		}
		return false;
	}

	public function storyArcIdForCharacterIdArray( array $obj = null)
	{
		if ( is_array($obj) && count($obj) > 0 ) {
			$select = SQL::Select($this, array( Story_Arc_Character::story_arc_id ));
			$select->where( Qualifier::IN( Story_Arc_Character::character_id, $obj ));
			$select->groupBy( array( Story_Arc_Character::story_arc_id ) );
			$select->having( array("count(" . Story_Arc_Character::story_arc_id. ") = " . count($obj)) );

			$idArray = $select->fetchAll();
			return array_map(function($stdClass) {return $stdClass->{Story_Arc_Character::story_arc_id}; }, $idArray);
		}
		return array();
	}

	public function create($story_arc, $character)
	{
		if (isset($story_arc, $story_arc->id, $character, $character->id)) {
			$join = $this->joinForStory_ArcAndCharacter($story_arc, $character);
			if ($join == false) {
				$params = array(
					Story_Arc_Character::character_id => $character->id,
					Story_Arc_Character::story_arc_id => $story_arc->id
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

	public function deleteAllForCharacter($obj)
	{
		$success = true;
		if ( $obj != false )
		{
			$array = $this->allForCharacter($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new exceptions\DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForCharacter($obj);
			}
		}
		return $success;
	}
}

?>
