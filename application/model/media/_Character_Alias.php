<?php

namespace model\media;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\media\Character_AliasDBO as Character_AliasDBO;

/* import related objects */
use \model\media\Character as Character;
use \model\media\CharacterDBO as CharacterDBO;

/** Generated class, do not edit.
 */
abstract class _Character_Alias extends Model
{
	const TABLE = 'character_alias';

	// attribute keys
	const id = 'id';
	const name = 'name';
	const character_id = 'character_id';

	// relationship keys
	const character = 'character';

	public function modelName()
	{
		return "Character_Alias";
	}

	public function dboName()
	{
		return '\model\media\Character_AliasDBO';
	}

	public function tableName() { return Character_Alias::TABLE; }
	public function tablePK() { return Character_Alias::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Character_Alias::name)
		);
	}

	public function allColumnNames()
	{
		return array(
			Character_Alias::id,
			Character_Alias::name,
			Character_Alias::character_id
		);
	}

	public function allAttributes()
	{
		return array(
			Character_Alias::name,
		);
	}

	public function allForeignKeys()
	{
		return array(Character_Alias::character_id);
	}

	public function allRelationshipNames()
	{
		return array(
			Character_Alias::character
		);
	}

	/**
	 *	Simple fetches
	 */

	public function allForName($value)
	{
		return $this->allObjectsForKeyValue(Character_Alias::name, $value);
	}




	/**
	 * Simple relationship fetches
	 */
	public function allForCharacter($obj)
	{
		return $this->allObjectsForFK(Character_Alias::character_id, $obj, $this->sortOrder(), 50);
	}

	public function countForCharacter($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Character_Alias::character_id, $obj );
		}
		return false;
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "character":
					return array( Character_Alias::character_id, "id"  );
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
			if ( isset($values['name']) == false ) {
				$default_name = $this->attributeDefaultValue( null, null, Character_Alias::name);
				if ( is_null( $default_name ) == false ) {
					$values['name'] = $default_name;
				}
			}

			// default conversion for relationships
			if ( isset($values['character']) ) {
				$local_character = $values['character'];
				if ( $local_character instanceof CharacterDBO) {
					$values[Character_Alias::character_id] = $local_character->id;
				}
				else if ( is_integer( $local_character) ) {
					$params[Character_Alias::character_id] = $local_character;
				}
			}
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Character_Alias ) {
			if ( isset($values['character']) ) {
				$local_character = $values['character'];
				if ( $local_character instanceof CharacterDBO) {
					$values[Character_Alias::character_id] = $local_character->id;
				}
				else if ( is_integer( $local_character) ) {
					$params[Character_Alias::character_id] = $values['character'];
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
		if ( $object instanceof Character_AliasDBO )
		{
			// does not own character Character
			return parent::deleteObject($object);
		}

		return false;
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
	public function objectForCharacterAndAlias(CharacterDBO $character, $name )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		$qualifiers[] = Qualifier::FK( 'character_id', $character);
		$qualifiers[] = Qualifier::Equals( 'name', $name);

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
				throw new \Exception( "objectForCharacterAndAlias expected 1 result, but fetched " . count($result) );
			}
		}

		return false;
	}


	/**
	 * Attribute editing
	 */
	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Character_Alias::name
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesMap() {
		return array(
			Character_Alias::name => Model::TEXT_TYPE,
			Character_Alias::character_id => Model::TO_ONE_TYPE
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
				case Character_Alias::character_id:
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
	function validate_name($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Character_Alias::name,
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
				Character_Alias::character_id,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
}

?>
