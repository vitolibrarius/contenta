<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;
use \Localized as Localized;
use \Logger as Logger;

use db\Qualifier as Qualifier;

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
			$join = Qualifier::AndQualifier(
				Qualifier::FK( Series_Character::series_id, $series ),
				Qualifier::FK( Series_Character::character_id, $character )
			);
			return $this->singleObject( $join );
		}

		return false;
	}

	public function allForSeries(model\SeriesDBO $obj)
	{
		return $this->allObjectsForFK(Series_Character::series_id, $obj);
	}

	public function allForCharacter(model\CharacterDBO $obj)
	{
		return $this->allObjectsForFK(Series_Character::character_id, $obj);
	}

	public function countForCharacter( model\CharacterDBO $obj = null)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Series_Character::character_id, $obj );
		}
		return false;
	}

	public function create($series, $character)
	{
		if (isset($series, $series->id, $character, $character->id)) {
			$join = $this->joinForSeriesAndCharacter($series, $character);
			if ($join == false) {
				$params = array(
					Series_Character::character_id => $character->id,
					Series_Character::series_id => $series->id
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

	public function deleteAllForSeries($obj)
	{
		$success = true;
		if ( $obj != false )
		{
			$array = $this->allForSeries($obj);
			while ( is_array($array) && count( $array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new exceptions\DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForSeries($obj);
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
