<?php

namespace model;

use \Session as Session;
use \DataObject as DataObject;
use \Model as Model;
use \Localized as Localized;
use \Logger as Logger;

class Character_Alias extends Model
{
	const TABLE =			'character_alias';
	const id =				'id';
	const character_id =	'character_id';
	const name =			'name';


	public function tableName() { return Character_Alias::TABLE; }
	public function tablePK() { return Character_Alias::id; }
	public function sortOrder() { return array(Character_Alias::name); }

	public function allColumnNames()
	{
		return array(Character_Alias::id, Character_Alias::character_id, Character_Alias::name );
	}

	public function allForCharacter( model\CharacterDBO $obj )
	{
		return $this->allObjectsForFK(Character_Alias::character_id, $obj, $this->sortOrder());
	}

	public function allForName($name)
	{
		return $this->allObjectsForKeyValue(Character_Alias::name, $name);
	}

	public function forName( model\CharacterDBO $obj, $name)
	{
		return $this->allObjectsForFKWithValue(Character_Alias::character_id, $obj, Character_Alias::name, $name);
	}

	public function create( model\CharacterDBO $characterObj, $name)
	{
		if (isset($characterObj, $characterObj->id, $name)) {
			$alias = $this->forName($characterObj, $name);
			if ($alias == false) {
				list( $alias, $errorList ) = $this->createObject(array(
					Character_Alias::character_id => $characterObj->id,
					Character_Alias::name => $name
					)
				);

				if ( is_array($errorList) ) {
					return $errorList;
				}
			}

			return $alias;
		}

		return false;
	}

	public function deleteObject( \DataObject $obj = null)
	{
		if ( $obj != false && $obj instanceof model\Character_AliasDBO )
		{
			return parent::deleteObject($obj);
		}

		return false;
	}

	public function deleteAllForCharacter(model\CharacterDBO $obj)
	{
		$success = true;
		if ( $obj != false )
		{
			// default batch size is 50
			$array = $this->allForCharacter($obj);
			while ( count( $array) > 0 ) {
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
