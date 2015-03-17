<?php

namespace model;

use \Session as Session;
use \DataObject as DataObject;
use \Model as Model;
use \Localized as Localized;

class Series_Character extends Model
{
	const TABLE =		'series_character';
	const id =			'id';
	const series_id =		'series_id';
	const character_id =	'character_id';


	public function tableName() { return Series_Character::TABLE; }
	public function tablePK() { return Series_Character::id; }
	public function sortOrder() { return array(Series_Character::series_id, Series_Character::character_id); }

	public function allColumnNames()
	{
		return array(Series_Character::id, Series_Character::series_id, Series_Character::character_id);
	}

	public function joinForSeriesAndCharacter($series, $character)
	{
		if (isset($series, $series->id, $character, $character->id)) {
			return $this->fetch(Series_Character::TABLE,
				$this->allColumns(),
				array(
					Series_Character::series_id => $series->id,
					Series_Character::character_id => $character->id
				)
			);
		}

		return false;
	}

	public function allForSeries($obj)
	{
		return $this->fetchAll(Series_Character::TABLE,
			$this->allColumns(),
			array(Series_Character::series_id => $obj->id),
			array(Series_Character::character_id)
		);
	}

	public function allForCharacter($obj)
	{
		return $this->fetchAll(Series_Character::TABLE,
			$this->allColumns(),
			array(Series_Character::character_id => $obj->id),
			array(Series_Character::series_id)
		);
	}

	public function countForCharacter( model\CharacterDBO $obj = null)
	{
		if ( is_null($obj) == false ) {
			return $this->countForQualifier(Series_Character::TABLE, array(Series_Character::character_id => $obj->id) );
		}
		return false;
	}

	public function create($series, $character)
	{
		if (isset($series, $series->id, $character, $character->id)) {
			$join = $this->joinForSeriesAndCharacter($series, $character);
			if ($join == false) {
				$newObjId = $this->createObj(Series_Character::TABLE, array(
					Series_Character::character_id => $character->id,
					Series_Character::series_id => $series->id
					)
				);
				$join = ($newObjId != false ? $this->objectForId($newObjId) : false);
			}

			return $join;
		}

		return false;
	}

	public function deleteAllForSeries($obj)
	{
		$success = true;
		if ( $obj != false )
		{
			$array = $this->allForSeries($obj);
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
