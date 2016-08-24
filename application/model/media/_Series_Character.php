<?php

namespace model\media;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\media\Series_CharacterDBO as Series_CharacterDBO;

/* import related objects */
use \model\media\Series as Series;
use \model\media\SeriesDBO as SeriesDBO;
use \model\media\Character as Character;
use \model\media\CharacterDBO as CharacterDBO;

/** Sample Creation script */
		/** SERIES_CHARACTER */
/*
		$sql = "CREATE TABLE IF NOT EXISTS series_character ( "
			. Series_Character::id . " INTEGER PRIMARY KEY, "
			. Series_Character::series_id . " INTEGER, "
			. Series_Character::character_id . " INTEGER, "
			. "FOREIGN KEY (". Series_Character::series_id .") REFERENCES " . Series::TABLE . "(" . Series::id . "),"
			. "FOREIGN KEY (". Series_Character::character_id .") REFERENCES " . Character::TABLE . "(" . Character::id . ")"
		. ")";
		$this->sqlite_execute( "series_character", $sql, "Create table series_character" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS series_character_series_idcharacter_id on series_character (series_id,character_id)';
		$this->sqlite_execute( "series_character", $sql, "Index on series_character (series_id,character_id)" );
*/
abstract class _Series_Character extends Model
{
	const TABLE = 'series_character';

	// attribute keys
	const id = 'id';
	const series_id = 'series_id';
	const character_id = 'character_id';

	// relationship keys
	const series = 'series';
	const character = 'character';

	public function tableName() { return Series_Character::TABLE; }
	public function tablePK() { return Series_Character::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Series_Character::series_id)
		);
	}

	public function allColumnNames()
	{
		return array(
			Series_Character::id,
			Series_Character::series_id,
			Series_Character::character_id
		);
	}

	public function allAttributes()
	{
		return array(
		);
	}

	public function allForeignKeys()
	{
		return array(Series_Character::series_id,
			Series_Character::character_id);
	}

	public function allRelationshipNames()
	{
		return array(
			Series_Character::series,
			Series_Character::character
		);
	}

	/**
	 *	Simple fetches
	 */




	/**
	 * Simple relationship fetches
	 */
	public function allForSeries($obj)
	{
		return $this->allObjectsForFK(Series_Character::series_id, $obj, $this->sortOrder(), 50);
	}

	public function countForSeries($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Series_Character::series_id, $obj );
		}
		return false;
	}
	public function allForCharacter($obj)
	{
		return $this->allObjectsForFK(Series_Character::character_id, $obj, $this->sortOrder(), 50);
	}

	public function countForCharacter($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Series_Character::character_id, $obj );
		}
		return false;
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "series":
					return array( Series_Character::series_id, "id"  );
					break;
				case "character":
					return array( Series_Character::character_id, "id"  );
					break;
				default:
					break;
			}
		}
		return parent::joinAttributes( $joinModel );
	}

	/**
	 *	Create/Update functions
	 */
	public function createObject( array $values = array() )
	{
		if ( isset($values) ) {

			// default values for attributes

			// default conversion for relationships
			if ( isset($values['series']) ) {
				$local_series = $values['series'];
				if ( $local_series instanceof SeriesDBO) {
					$values[Series_Character::series_id] = $local_series->id;
				}
				else if ( is_integer( $local_series) ) {
					$params[Series_Character::series_id] = $local_series;
				}
			}
			if ( isset($values['character']) ) {
				$local_character = $values['character'];
				if ( $local_character instanceof CharacterDBO) {
					$values[Series_Character::character_id] = $local_character->id;
				}
				else if ( is_integer( $local_character) ) {
					$params[Series_Character::character_id] = $local_character;
				}
			}
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Series_Character ) {
			if ( isset($values['series']) ) {
				$local_series = $values['series'];
				if ( $local_series instanceof SeriesDBO) {
					$values[Series_Character::series_id] = $local_series->id;
				}
				else if ( is_integer( $local_series) ) {
					$params[Series_Character::series_id] = $values['series'];
				}
			}
			if ( isset($values['character']) ) {
				$local_character = $values['character'];
				if ( $local_character instanceof CharacterDBO) {
					$values[Series_Character::character_id] = $local_character->id;
				}
				else if ( is_integer( $local_character) ) {
					$params[Series_Character::character_id] = $values['character'];
				}
			}
		}

		return parent::updateObject($object, $values);
	}

	/**
	 *	Delete functions
	 */
	public function deleteObject( DataObject $object = null)
	{
		if ( $object instanceof Series_CharacterDBO )
		{
			// does not own series Series
			// does not own character Character
			return parent::deleteObject($object);
		}

		return false;
	}

	public function deleteAllForSeries(SeriesDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForSeries($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForSeries($obj);
			}
		}
		return $success;
	}
	public function deleteAllForCharacter(CharacterDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForCharacter($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForCharacter($obj);
			}
		}
		return $success;
	}

	/**
	 * Named fetches
	 */
	public function objectForSeriesAndCharacter(SeriesDBO $series,CharacterDBO $char )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		$qualifiers[] = Qualifier::FK( 'series_id', $series);
		$qualifiers[] = Qualifier::FK( 'character_id', $char);

		if ( count($qualifiers) > 0 ) {
			$select->where( Qualifier::Combine( 'AND', $qualifiers ));
		}

		$result = $select->fetchAll();
		if ( is_array($result) ) {
			$result_size = count($result);
			if ( $result_size == 1 ) {
				return $result[0];
			}
			else if ($result_size > 1 ) {
				throw new \Exception( "objectForSeriesAndCharacter expected 1 result, but fetched " . count($result) );
			}
		}

		return false;
	}


	/**
	 * Attribute editing
	 */

	public function attributesMap() {
		return array(
			Series_Character::series_id => Model::TO_ONE_TYPE,
			Series_Character::character_id => Model::TO_ONE_TYPE
		);
	}

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) === false || is_null($object) == true) {
			switch ($attr) {
			}
		}
		return parent::attributeDefaultValue($object, $type, $attr);
	}

	/*
	 * return the foreign key object
	 */
	public function attributeObject($object = null, $type = null, $attr, $value)
	{
		$fkObject = false;
		if ( isset( $attr ) ) {
			switch ( $attr ) {
				case Series_Character::series_id:
					$series_model = Model::Named('Series');
					$fkObject = $series_model->objectForId( $value );
					break;
				case Series_Character::character_id:
					$character_model = Model::Named('Character');
					$fkObject = $character_model->objectForId( $value );
					break;
				default:
					break;
			}
		}
		return $fkObject;
	}

	/**
	 * Validation
	 */
	function validate_series_id($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Series_Character::series_id,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_character_id($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Series_Character::character_id,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
}

?>
