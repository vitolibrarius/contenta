<?php

namespace model\media;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\media\PublisherDBO as PublisherDBO;

/* import related objects */
use \model\media\Series as Series;
use \model\media\SeriesDBO as SeriesDBO;
use \model\media\Character as Character;
use \model\media\CharacterDBO as CharacterDBO;
use \model\media\Story_Arc as Story_Arc;
use \model\media\Story_ArcDBO as Story_ArcDBO;

/** Generated class, do not edit.
 */
abstract class _Publisher extends Model
{
	const TABLE = 'publisher';

	// attribute keys
	const id = 'id';
	const name = 'name';
	const created = 'created';
	const xurl = 'xurl';
	const xsource = 'xsource';
	const xid = 'xid';
	const xupdated = 'xupdated';

	// relationship keys
	const series = 'series';
	const characters = 'characters';
	const story_arcs = 'story_arcs';

	public function modelName()
	{
		return "Publisher";
	}

	public function dboName()
	{
		return '\model\media\PublisherDBO';
	}

	public function tableName() { return Publisher::TABLE; }
	public function tablePK() { return Publisher::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Publisher::name)
		);
	}

	public function allColumnNames()
	{
		return array(
			Publisher::id,
			Publisher::name,
			Publisher::created,
			Publisher::xurl,
			Publisher::xsource,
			Publisher::xid,
			Publisher::xupdated
		);
	}

	public function allAttributes()
	{
		return array(
			Publisher::name,
			Publisher::created,
			Publisher::xurl,
			Publisher::xsource,
			Publisher::xid,
			Publisher::xupdated
		);
	}

	public function allForeignKeys()
	{
		return array();
	}

	public function allRelationshipNames()
	{
		return array(
			Publisher::series,
			Publisher::characters,
			Publisher::story_arcs
		);
	}

	public function searchQualifiers( array $query )
	{
		$qualifiers = array();
		if ( is_array($query) ) {
			foreach( $query as $attr => $value ) {
				switch ($attr) {
			// Publisher::id == INTEGER

			// Publisher::name == TEXT
				case Publisher::name:
					if (strlen($value) > 0) {
						$qualifiers[Publisher::name] = Qualifier::Equals( Publisher::name, $value );
					}
					break;

			// Publisher::created == DATE

			// Publisher::xurl == TEXT
				case Publisher::xurl:
					if (strlen($value) > 0) {
						$qualifiers[Publisher::xurl] = Qualifier::Equals( Publisher::xurl, $value );
					}
					break;

			// Publisher::xsource == TEXT
				case Publisher::xsource:
					if (strlen($value) > 0) {
						$qualifiers[Publisher::xsource] = Qualifier::Equals( Publisher::xsource, $value );
					}
					break;

			// Publisher::xid == TEXT
				case Publisher::xid:
					if (strlen($value) > 0) {
						$qualifiers[Publisher::xid] = Qualifier::Equals( Publisher::xid, $value );
					}
					break;

			// Publisher::xupdated == DATE

				default:
					/* no type specified for Publisher::xupdated */
					break;
				}
			}
		}
		return $qualifiers;
	}

	/**
	 *	Simple fetches
	 */

	public function allForName($value)
	{
		return $this->allObjectsForKeyValue(Publisher::name, $value);
	}



	public function allForXurl($value)
	{
		return $this->allObjectsForKeyValue(Publisher::xurl, $value);
	}


	public function allForXsource($value)
	{
		return $this->allObjectsForKeyValue(Publisher::xsource, $value);
	}


	public function allForXid($value)
	{
		return $this->allObjectsForKeyValue(Publisher::xid, $value);
	}




	/**
	 * Simple relationship fetches
	 */

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "series":
					return array( Publisher::id, "publisher_id"  );
					break;
				case "character":
					return array( Publisher::id, "publisher_id"  );
					break;
				case "story_arc":
					return array( Publisher::id, "publisher_id"  );
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
				$default_name = $this->attributeDefaultValue( null, null, Publisher::name);
				if ( is_null( $default_name ) == false ) {
					$values['name'] = $default_name;
				}
			}
			if ( isset($values['created']) == false ) {
				$default_created = $this->attributeDefaultValue( null, null, Publisher::created);
				if ( is_null( $default_created ) == false ) {
					$values['created'] = $default_created;
				}
			}
			if ( isset($values['xurl']) == false ) {
				$default_xurl = $this->attributeDefaultValue( null, null, Publisher::xurl);
				if ( is_null( $default_xurl ) == false ) {
					$values['xurl'] = $default_xurl;
				}
			}
			if ( isset($values['xsource']) == false ) {
				$default_xsource = $this->attributeDefaultValue( null, null, Publisher::xsource);
				if ( is_null( $default_xsource ) == false ) {
					$values['xsource'] = $default_xsource;
				}
			}
			if ( isset($values['xid']) == false ) {
				$default_xid = $this->attributeDefaultValue( null, null, Publisher::xid);
				if ( is_null( $default_xid ) == false ) {
					$values['xid'] = $default_xid;
				}
			}
			if ( isset($values['xupdated']) == false ) {
				$default_xupdated = $this->attributeDefaultValue( null, null, Publisher::xupdated);
				if ( is_null( $default_xupdated ) == false ) {
					$values['xupdated'] = $default_xupdated;
				}
			}

			// default conversion for relationships
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Publisher ) {
		}

		return parent::updateObject($object, $values);
	}

	/**
	 *	Delete functions
	 */
	public function deleteObject( DataObject $object = null)
	{
		if ( $object instanceof PublisherDBO )
		{
			$series_model = Model::Named('Series');
			if ( $series_model->deleteAllForKeyValue(Series::publisher_id, $object->id) == false ) {
				return false;
			}
			$character_model = Model::Named('Character');
			if ( $character_model->deleteAllForKeyValue(Character::publisher_id, $object->id) == false ) {
				return false;
			}
			$story_arc_model = Model::Named('Story_Arc');
			if ( $story_arc_model->deleteAllForKeyValue(Story_Arc::publisher_id, $object->id) == false ) {
				return false;
			}
			return parent::deleteObject($object);
		}

		return false;
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
				Publisher::name
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesMap() {
		return array(
			Publisher::name => Model::TEXT_TYPE,
			Publisher::created => Model::DATE_TYPE,
			Publisher::xurl => Model::TEXTAREA_TYPE,
			Publisher::xsource => Model::TEXT_TYPE,
			Publisher::xid => Model::TEXT_TYPE,
			Publisher::xupdated => Model::DATE_TYPE
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
				Publisher::name,
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
				Publisher::created,
				"IMMUTABLE"
			);
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
