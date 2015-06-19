<?php

namespace model;

use \Session as Session;
use \DataObject as DataObject;
use \Model as Model;
use \Localized as Localized;

class Publication_Character extends Model
{
	const TABLE =		'publication_character';
	const id =			'id';
	const publication_id =		'publication_id';
	const character_id =	'character_id';


	public function tableName() { return Publication_Character::TABLE; }
	public function tablePK() { return Publication_Character::id; }
	public function sortOrder() { return array(Publication_Character::publication_id, Publication_Character::character_id); }

	public function allColumnNames()
	{
		return array(Publication_Character::id, Publication_Character::publication_id, Publication_Character::character_id);
	}

	public function joinForPublicationAndCharacter($publication, $character)
	{
		if (isset($publication, $publication->id, $character, $character->id)) {
			return $this->fetch(Publication_Character::TABLE,
				$this->allColumns(),
				array(
					Publication_Character::publication_id => $publication->id,
					Publication_Character::character_id => $character->id
				)
			);
		}

		return false;
	}

	public function allForPublication($obj)
	{
		return $this->allObjectsForFK(Publication_Character::publication_id, $obj);
	}

	public function allForCharacter($obj)
	{
		return $this->allObjectsForFK(Publication_Character::character_id, $obj);
	}

	public function countForCharacter( model\CharacterDBO $obj = null)
	{
		if ( is_null($obj) == false ) {
			return $this->countForQualifier(Publication_Character::TABLE, array(Publication_Character::character_id => $obj->id) );
		}
		return false;
	}

	public function create($publication, $character)
	{
		if (isset($publication, $publication->id, $character, $character->id)) {
			$join = $this->joinForPublicationAndCharacter($publication, $character);
			if ($join == false) {
				$newObjId = $this->createObj(Publication_Character::TABLE, array(
					Publication_Character::character_id => $character->id,
					Publication_Character::publication_id => $publication->id
					)
				);
				$join = ($newObjId != false ? $this->objectForId($newObjId) : false);
			}

			return $join;
		}

		return false;
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

	public function deleteAllForCharacter($obj)
	{
		$success = true;
		if ( $obj != false )
		{
			$array = $this->allForCharacter($obj);
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
