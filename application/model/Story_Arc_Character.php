<?php

namespace model;

use \Session as Session;
use \DataObject as DataObject;
use \Model as Model;
use \Localized as Localized;

class Story_Arc_Character extends Model
{
	const TABLE =		'story_arc_character';
	const id =			'id';
	const story_arc_id =		'story_arc_id';
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
			return $this->fetch(Story_Arc_Character::TABLE,
				$this->allColumns(),
				array(
					Story_Arc_Character::story_arc_id => $story_arc->id,
					Story_Arc_Character::character_id => $character->id
				)
			);
		}

		return false;
	}

	public function allForStory_Arc($obj)
	{
		return $this->fetchAll(Story_Arc_Character::TABLE,
			$this->allColumns(),
			array(Story_Arc_Character::story_arc_id => $obj->id),
			array(Story_Arc_Character::character_id)
		);
	}

	public function allForCharacter($obj)
	{
		return $this->fetchAll(Story_Arc_Character::TABLE,
			$this->allColumns(),
			array(Story_Arc_Character::character_id => $obj->id),
			array(Story_Arc_Character::story_arc_id)
		);
	}

	public function countForCharacter( model\CharacterDBO $obj = null)
	{
		if ( is_null($obj) == false ) {
			return $this->countForQualifier(Story_Arc_Character::TABLE, array(Story_Arc_Character::character_id => $obj->id) );
		}
		return false;
	}

	public function create($story_arc, $character)
	{
		if (isset($story_arc, $story_arc->id, $character, $character->id)) {
			$join = $this->joinForStory_ArcAndCharacter($story_arc, $character);
			if ($join == false) {
				$newObjId = $this->createObj(Story_Arc_Character::TABLE, array(
					Story_Arc_Character::character_id => $character->id,
					Story_Arc_Character::story_arc_id => $story_arc->id
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
}

?>
