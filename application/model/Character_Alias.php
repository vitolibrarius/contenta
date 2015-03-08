<?php

namespace model;

use \Session as Session;
use \DataObject as DataObject;
use \Model as Model;
use \Localized as Localized;

class Character_Alias extends Model
{
	const TABLE =			'character_alias';
	const id =				'id';
	const character_id =	'character_id';
	const name =			'name';


	public function tableName() { return Character_Alias::TABLE; }
	public function tablePK() { return Character_Alias::id; }
	public function sortOrder() { return array(Character_Alias::name); }

	public function dboClassName() { return 'model\\Character_AliasDBO'; }

	public function allColumnNames()
	{
		return array(Character_Alias::id, Character_Alias::character_id, Character_Alias::name );
	}

	public function allForCharacter($obj)
	{
		return $this->fetchAll(Character_Alias::TABLE, $this->allColumns(), array(Character_Alias::character_id => $obj->id), array(Character_Alias::name));
	}

	public function allForName($name)
	{
		return $this->fetchAll(Character_Alias::TABLE,
			$this->allColumns(),
			array(Character_Alias::name => $name),
			array(Character_Alias::character_id));
	}

	public function forName($obj, $name)
	{
		return $this->fetch(Character_Alias::TABLE,
			$this->allColumns(),
			array(Character_Alias::character_id => $obj->id, Character_Alias::name => $name));
	}

	public function create($characterObj, $name)
	{
		if (isset($characterObj, $characterObj->id, $name)) {
			$alias = $this->forName($characterObj, $name);
			if ($alias == false) {
				$newObjId = $this->createObj(Character_Alias::TABLE, array(
					Character_Alias::character_id => $characterObj->id,
					Character_Alias::name => $name
					)
				);
				$alias = ($newObjId != false ? $this->objectForId($newObjId) : false);
			}

			return $alias;
		}

		return false;
	}

	public function deleteObject($obj = null)
	{
		if ( $obj != false )
		{
			return parent::deleteObject($obj);
		}

		return false;
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
