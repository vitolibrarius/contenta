<?php

namespace model\media;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\media\CharacterDBO as CharacterDBO;

/* import related objects */
use \model\media\Character_Alias as Character_Alias;
use \model\media\Character_AliasDBO as Character_AliasDBO;
use \model\media\Publisher as Publisher;
use \model\media\PublisherDBO as PublisherDBO;
use \model\media\Publication_Character as Publication_Character;
use \model\media\Publication_CharacterDBO as Publication_CharacterDBO;
use \model\media\Series_Character as Series_Character;
use \model\media\Series_CharacterDBO as Series_CharacterDBO;
use \model\media\Story_Arc_Character as Story_Arc_Character;
use \model\media\Story_Arc_CharacterDBO as Story_Arc_CharacterDBO;

/** Generated class, do not edit.
 */
abstract class _Character extends Model
{
	const TABLE = 'character';

	// attribute keys
	const id = 'id';
	const publisher_id = 'publisher_id';
	const created = 'created';
	const name = 'name';
	const realname = 'realname';
	const desc = 'desc';
	const popularity = 'popularity';
	const gender = 'gender';
	const xurl = 'xurl';
	const xsource = 'xsource';
	const xid = 'xid';
	const xupdated = 'xupdated';

	// relationship keys
	const aliases = 'aliases';
	const publisher = 'publisher';
	const publication_characters = 'publication_characters';
	const series_characters = 'series_characters';
	const story_arc_characters = 'story_arc_characters';

	public function modelName()
	{
		return "Character";
	}

	public function dboName()
	{
		return '\model\media\CharacterDBO';
	}

	public function tableName() { return Character::TABLE; }
	public function tablePK() { return Character::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Character::name),
			array( 'desc' => Character::popularity)
		);
	}

	public function allColumnNames()
	{
		return array(
			Character::id,
			Character::publisher_id,
			Character::created,
			Character::name,
			Character::realname,
			Character::desc,
			Character::popularity,
			Character::gender,
			Character::xurl,
			Character::xsource,
			Character::xid,
			Character::xupdated
		);
	}

	public function allAttributes()
	{
		return array(
			Character::created,
			Character::name,
			Character::realname,
			Character::desc,
			Character::popularity,
			Character::gender,
			Character::xurl,
			Character::xsource,
			Character::xid,
			Character::xupdated
		);
	}

	public function allForeignKeys()
	{
		return array(Character::publisher_id);
	}

	public function allRelationshipNames()
	{
		return array(
			Character::aliases,
			Character::publisher,
			Character::publication_characters,
			Character::series_characters,
			Character::story_arc_characters
		);
	}

	/**
	 *	Simple fetches
	 */



	public function allForName($value)
	{
		return $this->allObjectsForKeyValue(Character::name, $value);
	}


	public function allForRealname($value)
	{
		return $this->allObjectsForKeyValue(Character::realname, $value);
	}


	public function allForDesc($value)
	{
		return $this->allObjectsForKeyValue(Character::desc, $value);
	}


	public function allForPopularity($value)
	{
		return $this->allObjectsForKeyValue(Character::popularity, $value);
	}

	public function allForGender($value)
	{
		return $this->allObjectsForKeyValue(Character::gender, $value);
	}


	public function allForXurl($value)
	{
		return $this->allObjectsForKeyValue(Character::xurl, $value);
	}


	public function allForXsource($value)
	{
		return $this->allObjectsForKeyValue(Character::xsource, $value);
	}


	public function allForXid($value)
	{
		return $this->allObjectsForKeyValue(Character::xid, $value);
	}




	/**
	 * Simple relationship fetches
	 */
	public function allForPublisher($obj)
	{
		return $this->allObjectsForFK(Character::publisher_id, $obj, $this->sortOrder(), 50);
	}

	public function countForPublisher($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Character::publisher_id, $obj );
		}
		return false;
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "character_alias":
					return array( Character::id, "character_id"  );
					break;
				case "publisher":
					return array( Character::publisher_id, "id"  );
					break;
				case "publication_character":
					return array( Character::id, "character_id"  );
					break;
				case "series_character":
					return array( Character::id, "character_id"  );
					break;
				case "story_arc_character":
					return array( Character::id, "character_id"  );
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
			if ( isset($values['created']) == false ) {
				$default_created = $this->attributeDefaultValue( null, null, Character::created);
				if ( is_null( $default_created ) == false ) {
					$values['created'] = $default_created;
				}
			}
			if ( isset($values['name']) == false ) {
				$default_name = $this->attributeDefaultValue( null, null, Character::name);
				if ( is_null( $default_name ) == false ) {
					$values['name'] = $default_name;
				}
			}
			if ( isset($values['realname']) == false ) {
				$default_realname = $this->attributeDefaultValue( null, null, Character::realname);
				if ( is_null( $default_realname ) == false ) {
					$values['realname'] = $default_realname;
				}
			}
			if ( isset($values['desc']) == false ) {
				$default_desc = $this->attributeDefaultValue( null, null, Character::desc);
				if ( is_null( $default_desc ) == false ) {
					$values['desc'] = $default_desc;
				}
			}
			if ( isset($values['popularity']) == false ) {
				$default_popularity = $this->attributeDefaultValue( null, null, Character::popularity);
				if ( is_null( $default_popularity ) == false ) {
					$values['popularity'] = $default_popularity;
				}
			}
			if ( isset($values['gender']) == false ) {
				$default_gender = $this->attributeDefaultValue( null, null, Character::gender);
				if ( is_null( $default_gender ) == false ) {
					$values['gender'] = $default_gender;
				}
			}
			if ( isset($values['xurl']) == false ) {
				$default_xurl = $this->attributeDefaultValue( null, null, Character::xurl);
				if ( is_null( $default_xurl ) == false ) {
					$values['xurl'] = $default_xurl;
				}
			}
			if ( isset($values['xsource']) == false ) {
				$default_xsource = $this->attributeDefaultValue( null, null, Character::xsource);
				if ( is_null( $default_xsource ) == false ) {
					$values['xsource'] = $default_xsource;
				}
			}
			if ( isset($values['xid']) == false ) {
				$default_xid = $this->attributeDefaultValue( null, null, Character::xid);
				if ( is_null( $default_xid ) == false ) {
					$values['xid'] = $default_xid;
				}
			}
			if ( isset($values['xupdated']) == false ) {
				$default_xupdated = $this->attributeDefaultValue( null, null, Character::xupdated);
				if ( is_null( $default_xupdated ) == false ) {
					$values['xupdated'] = $default_xupdated;
				}
			}

			// default conversion for relationships
			if ( isset($values['publisher']) ) {
				$local_publisher = $values['publisher'];
				if ( $local_publisher instanceof PublisherDBO) {
					$values[Character::publisher_id] = $local_publisher->id;
				}
				else if ( is_integer( $local_publisher) ) {
					$params[Character::publisher_id] = $local_publisher;
				}
			}
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Character ) {
			if ( isset($values['publisher']) ) {
				$local_publisher = $values['publisher'];
				if ( $local_publisher instanceof PublisherDBO) {
					$values[Character::publisher_id] = $local_publisher->id;
				}
				else if ( is_integer( $local_publisher) ) {
					$params[Character::publisher_id] = $values['publisher'];
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
		if ( $object instanceof CharacterDBO )
		{
			$character_alias_model = Model::Named('Character_Alias');
			if ( $character_alias_model->deleteAllForKeyValue(Character_Alias::character_id, $object->id) == false ) {
				return false;
			}
			// does not own publisher Publisher
			$publication_character_model = Model::Named('Publication_Character');
			if ( $publication_character_model->deleteAllForKeyValue(Publication_Character::character_id, $object->id) == false ) {
				return false;
			}
			$series_character_model = Model::Named('Series_Character');
			if ( $series_character_model->deleteAllForKeyValue(Series_Character::character_id, $object->id) == false ) {
				return false;
			}
			$story_arc_character_model = Model::Named('Story_Arc_Character');
			if ( $story_arc_character_model->deleteAllForKeyValue(Story_Arc_Character::character_id, $object->id) == false ) {
				return false;
			}
			return parent::deleteObject($object);
		}

		return false;
	}

	public function deleteAllForPublisher(PublisherDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForPublisher($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForPublisher($obj);
			}
		}
		return $success;
	}

	/**
	 * Named fetches
	 */
	public function objectForExternal( $xid, $xsrc )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		$qualifiers[] = Qualifier::Equals( 'xid', $xid);
		$qualifiers[] = Qualifier::Equals( 'xsource', $xsrc);

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
				throw new \Exception( "objectForExternal expected 1 result, but fetched " . count($result) );
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
				Character::name
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesMap() {
		return array(
			Character::publisher_id => Model::TO_ONE_TYPE,
			Character::created => Model::DATE_TYPE,
			Character::name => Model::TEXT_TYPE,
			Character::realname => Model::TEXT_TYPE,
			Character::desc => Model::TEXTAREA_TYPE,
			Character::popularity => Model::INT_TYPE,
			Character::gender => Model::TEXT_TYPE,
			Character::xurl => Model::TEXTAREA_TYPE,
			Character::xsource => Model::TEXT_TYPE,
			Character::xid => Model::TEXT_TYPE,
			Character::xupdated => Model::DATE_TYPE
		);
	}

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) === false || is_null($object) == true) {
			switch ($attr) {
				case Character::popularity:
					return 0;
				case Character::gender:
					return 'unknown';
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
				case Character::publisher_id:
					$publisher_model = Model::Named('Publisher');
					$fkObject = $publisher_model->objectForId( $value );
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
	function validate_publisher_id($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Character::publisher_id,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_created($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// created date is not changeable
		if ( isset($object, $object->created) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Character::created,
				"IMMUTABLE"
			);
		}
		return null;
	}
	function validate_name($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Character::name,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_realname($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_desc($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_popularity($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Character::popularity,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
	function validate_gender($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_xurl($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_xsource($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_xid($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_xupdated($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
}

?>
