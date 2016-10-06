<?php

namespace model\media;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\media\Publication_CharacterDBO as Publication_CharacterDBO;

/* import related objects */
use \model\media\Publication as Publication;
use \model\media\PublicationDBO as PublicationDBO;
use \model\media\Character as Character;
use \model\media\CharacterDBO as CharacterDBO;

/** Sample Creation script */
		/** PUBLICATION_CHARACTER */
/*
		$sql = "CREATE TABLE IF NOT EXISTS publication_character ( "
			. Publication_Character::id . " INTEGER PRIMARY KEY, "
			. Publication_Character::publication_id . " INTEGER, "
			. Publication_Character::character_id . " INTEGER, "
			. "FOREIGN KEY (". Publication_Character::publication_id .") REFERENCES " . Publication::TABLE . "(" . Publication::id . "),"
			. "FOREIGN KEY (". Publication_Character::character_id .") REFERENCES " . Character::TABLE . "(" . Character::id . ")"
		. ")";
		$this->sqlite_execute( "publication_character", $sql, "Create table publication_character" );

		$sql = 'CREATE INDEX IF NOT EXISTS publication_characterPublication_fk on publication_character (publication_id)';
		$this->sqlite_execute( "publication_character", $sql, "FK Index on publication_character (publication_id)" );
		$sql = 'CREATE INDEX IF NOT EXISTS publication_characterCharacter_fk on publication_character (character_id)';
		$this->sqlite_execute( "publication_character", $sql, "FK Index on publication_character (character_id)" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS publication_character_publication_idcharacter_id on publication_character (publication_id,character_id)';
		$this->sqlite_execute( "publication_character", $sql, "Index on publication_character (publication_id,character_id)" );
*/
abstract class _Publication_Character extends Model
{
	const TABLE = 'publication_character';

	// attribute keys
	const id = 'id';
	const publication_id = 'publication_id';
	const character_id = 'character_id';

	// relationship keys
	const publication = 'publication';
	const character = 'character';

	public function tableName() { return Publication_Character::TABLE; }
	public function tablePK() { return Publication_Character::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Publication_Character::publication_id)
		);
	}

	public function allColumnNames()
	{
		return array(
			Publication_Character::id,
			Publication_Character::publication_id,
			Publication_Character::character_id
		);
	}

	public function allAttributes()
	{
		return array(
		);
	}

	public function allForeignKeys()
	{
		return array(Publication_Character::publication_id,
			Publication_Character::character_id);
	}

	public function allRelationshipNames()
	{
		return array(
			Publication_Character::publication,
			Publication_Character::character
		);
	}

	/**
	 *	Simple fetches
	 */




	/**
	 * Simple relationship fetches
	 */
	public function allForPublication($obj)
	{
		return $this->allObjectsForFK(Publication_Character::publication_id, $obj, $this->sortOrder(), 50);
	}

	public function countForPublication($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Publication_Character::publication_id, $obj );
		}
		return false;
	}
	public function allForCharacter($obj)
	{
		return $this->allObjectsForFK(Publication_Character::character_id, $obj, $this->sortOrder(), 50);
	}

	public function countForCharacter($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Publication_Character::character_id, $obj );
		}
		return false;
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "publication":
					return array( Publication_Character::publication_id, "id"  );
					break;
				case "character":
					return array( Publication_Character::character_id, "id"  );
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
			if ( isset($values['publication']) ) {
				$local_publication = $values['publication'];
				if ( $local_publication instanceof PublicationDBO) {
					$values[Publication_Character::publication_id] = $local_publication->id;
				}
				else if ( is_integer( $local_publication) ) {
					$params[Publication_Character::publication_id] = $local_publication;
				}
			}
			if ( isset($values['character']) ) {
				$local_character = $values['character'];
				if ( $local_character instanceof CharacterDBO) {
					$values[Publication_Character::character_id] = $local_character->id;
				}
				else if ( is_integer( $local_character) ) {
					$params[Publication_Character::character_id] = $local_character;
				}
			}
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Publication_Character ) {
			if ( isset($values['publication']) ) {
				$local_publication = $values['publication'];
				if ( $local_publication instanceof PublicationDBO) {
					$values[Publication_Character::publication_id] = $local_publication->id;
				}
				else if ( is_integer( $local_publication) ) {
					$params[Publication_Character::publication_id] = $values['publication'];
				}
			}
			if ( isset($values['character']) ) {
				$local_character = $values['character'];
				if ( $local_character instanceof CharacterDBO) {
					$values[Publication_Character::character_id] = $local_character->id;
				}
				else if ( is_integer( $local_character) ) {
					$params[Publication_Character::character_id] = $values['character'];
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
		if ( $object instanceof Publication_CharacterDBO )
		{
			// does not own publication Publication
			// does not own character Character
			return parent::deleteObject($object);
		}

		return false;
	}

	public function deleteAllForPublication(PublicationDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForPublication($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForPublication($obj);
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
	public function objectForPublicationAndCharacter(PublicationDBO $pub,CharacterDBO $char )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		$qualifiers[] = Qualifier::FK( 'publication_id', $pub);
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
				throw new \Exception( "objectForPublicationAndCharacter expected 1 result, but fetched " . count($result) );
			}
		}

		return false;
	}


	/**
	 * Attribute editing
	 */

	public function attributesMap() {
		return array(
			Publication_Character::publication_id => Model::TO_ONE_TYPE,
			Publication_Character::character_id => Model::TO_ONE_TYPE
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
				case Publication_Character::publication_id:
					$publication_model = Model::Named('Publication');
					$fkObject = $publication_model->objectForId( $value );
					break;
				case Publication_Character::character_id:
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
	function validate_publication_id($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Publication_Character::publication_id,
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
				Publication_Character::character_id,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
}

?>
