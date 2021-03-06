<?php

namespace model\media;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\media\Story_Arc_CharacterDBO as Story_Arc_CharacterDBO;

/* import related objects */
use \model\media\Story_Arc as Story_Arc;
use \model\media\Story_ArcDBO as Story_ArcDBO;
use \model\media\Character as Character;
use \model\media\CharacterDBO as CharacterDBO;

/** Generated class, do not edit.
 */
abstract class _Story_Arc_Character extends Model
{
	const TABLE = 'story_arc_character';

	// attribute keys
	const id = 'id';
	const story_arc_id = 'story_arc_id';
	const character_id = 'character_id';

	// relationship keys
	const story_arc = 'story_arc';
	const character = 'character';

	public function modelName()
	{
		return "Story_Arc_Character";
	}

	public function dboName()
	{
		return '\model\media\Story_Arc_CharacterDBO';
	}

	public function tableName() { return Story_Arc_Character::TABLE; }
	public function tablePK() { return Story_Arc_Character::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Story_Arc_Character::story_arc_id)
		);
	}

	public function allColumnNames()
	{
		return array(
			Story_Arc_Character::id,
			Story_Arc_Character::story_arc_id,
			Story_Arc_Character::character_id
		);
	}

	public function allAttributes()
	{
		return array(
		);
	}

	public function allForeignKeys()
	{
		return array(Story_Arc_Character::story_arc_id,
			Story_Arc_Character::character_id);
	}

	public function allRelationshipNames()
	{
		return array(
			Story_Arc_Character::story_arc,
			Story_Arc_Character::character
		);
	}

	public function attributes()
	{
		return array(
		);
	}

	public function relationships()
	{
		return array(
			Story_Arc_Character::story_arc => array(
				'destination' => 'Story_Arc',
				'ownsDestination' => false,
				'isMandatory' => true,
				'isToMany' => false,
				'joins' => array( 'story_arc_id' => 'id')
			),
			Story_Arc_Character::character => array(
				'destination' => 'Character',
				'ownsDestination' => false,
				'isMandatory' => true,
				'isToMany' => false,
				'joins' => array( 'character_id' => 'id')
			)
		);
	}

	public function searchQualifiers( array $query )
	{
		$qualifiers = array();
		if ( is_array($query) ) {
			foreach( $query as $attr => $value ) {
				switch ($attr) {
			// Story_Arc_Character::id == INTEGER

			// Story_Arc_Character::story_arc_id == INTEGER
				case Story_Arc_Character::story_arc_id:
					if ( intval($value) > 0 ) {
						$qualifiers[Story_Arc_Character::story_arc_id] = Qualifier::Equals( Story_Arc_Character::story_arc_id, intval($value) );
					}
					break;

			// Story_Arc_Character::character_id == INTEGER
				case Story_Arc_Character::character_id:
					if ( intval($value) > 0 ) {
						$qualifiers[Story_Arc_Character::character_id] = Qualifier::Equals( Story_Arc_Character::character_id, intval($value) );
					}
					break;

				default:
					/* no type specified for Story_Arc_Character::character_id */
					break;
				}
			}
		}
		return $qualifiers;
	}

	/**
	 *	Simple fetches
	 */




	/**
	 * Simple relationship fetches
	 */
	public function allForStory_arc($obj, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForFK(Story_Arc_Character::story_arc_id, $obj, $this->sortOrder(), $limit);
	}

	public function countForStory_arc($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Story_Arc_Character::story_arc_id, $obj );
		}
		return false;
	}
	public function allForCharacter($obj, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForFK(Story_Arc_Character::character_id, $obj, $this->sortOrder(), $limit);
	}

	public function countForCharacter($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Story_Arc_Character::character_id, $obj );
		}
		return false;
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "story_arc":
					return array( Story_Arc_Character::story_arc_id, "id"  );
					break;
				case "character":
					return array( Story_Arc_Character::character_id, "id"  );
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
			if ( isset($values['story_arc']) ) {
				$local_story_arc = $values['story_arc'];
				if ( $local_story_arc instanceof Story_ArcDBO) {
					$values[Story_Arc_Character::story_arc_id] = $local_story_arc->id;
				}
				else if ( is_integer( $local_story_arc) ) {
					$params[Story_Arc_Character::story_arc_id] = $local_story_arc;
				}
			}
			if ( isset($values['character']) ) {
				$local_character = $values['character'];
				if ( $local_character instanceof CharacterDBO) {
					$values[Story_Arc_Character::character_id] = $local_character->id;
				}
				else if ( is_integer( $local_character) ) {
					$params[Story_Arc_Character::character_id] = $local_character;
				}
			}
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Story_Arc_Character ) {
			if ( isset($values['story_arc']) ) {
				$local_story_arc = $values['story_arc'];
				if ( $local_story_arc instanceof Story_ArcDBO) {
					$values[Story_Arc_Character::story_arc_id] = $local_story_arc->id;
				}
				else if ( is_integer( $local_story_arc) ) {
					$params[Story_Arc_Character::story_arc_id] = $values['story_arc'];
				}
			}
			if ( isset($values['character']) ) {
				$local_character = $values['character'];
				if ( $local_character instanceof CharacterDBO) {
					$values[Story_Arc_Character::character_id] = $local_character->id;
				}
				else if ( is_integer( $local_character) ) {
					$params[Story_Arc_Character::character_id] = $values['character'];
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
		if ( $object instanceof Story_Arc_CharacterDBO )
		{
			// does not own story_arc Story_Arc
			// does not own character Character
			return parent::deleteObject($object);
		}

		return false;
	}

	public function deleteAllForStory_arc(Story_ArcDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForStory_arc($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForStory_arc($obj);
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
	public function objectForStoryArcAndCharacter(Story_ArcDBO $story,CharacterDBO $char )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		$qualifiers[] = Qualifier::FK( 'story_arc_id', $story);
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
				throw new \Exception( "objectForStoryArcAndCharacter expected 1 result, but fetched " . count($result) );
			}
		}

		return false;
	}


	/**
	 * Attribute editing
	 */

	public function attributesMap() {
		return array(
			Story_Arc_Character::story_arc_id => Model::TO_ONE_TYPE,
			Story_Arc_Character::character_id => Model::TO_ONE_TYPE
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
				case Story_Arc_Character::story_arc_id:
					$story_arc_model = Model::Named('Story_Arc');
					$fkObject = $story_arc_model->objectForId( $value );
					break;
				case Story_Arc_Character::character_id:
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
	function validate_story_arc_id($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Story_Arc_Character::story_arc_id,
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
				Story_Arc_Character::character_id,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
}

?>
