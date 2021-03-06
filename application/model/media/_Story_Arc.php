<?php

namespace model\media;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\media\Story_ArcDBO as Story_ArcDBO;

/* import related objects */
use \model\media\Publisher as Publisher;
use \model\media\PublisherDBO as PublisherDBO;
use \model\media\Story_Arc_Character as Story_Arc_Character;
use \model\media\Story_Arc_CharacterDBO as Story_Arc_CharacterDBO;
use \model\media\Story_Arc_Publication as Story_Arc_Publication;
use \model\media\Story_Arc_PublicationDBO as Story_Arc_PublicationDBO;
use \model\media\Story_Arc_Series as Story_Arc_Series;
use \model\media\Story_Arc_SeriesDBO as Story_Arc_SeriesDBO;
use \model\reading\Reading_Queue as Reading_Queue;
use \model\reading\Reading_QueueDBO as Reading_QueueDBO;

/** Generated class, do not edit.
 */
abstract class _Story_Arc extends Model
{
	const TABLE = 'story_arc';

	// attribute keys
	const id = 'id';
	const publisher_id = 'publisher_id';
	const created = 'created';
	const name = 'name';
	const desc = 'desc';
	const pub_active = 'pub_active';
	const pub_wanted = 'pub_wanted';
	const pub_cycle = 'pub_cycle';
	const pub_available = 'pub_available';
	const pub_count = 'pub_count';
	const xurl = 'xurl';
	const xsource = 'xsource';
	const xid = 'xid';
	const xupdated = 'xupdated';

	// relationship keys
	const publisher = 'publisher';
	const story_arc_characters = 'story_arc_characters';
	const story_arc_publication = 'story_arc_publication';
	const story_arc_series = 'story_arc_series';
	const reading_queues = 'reading_queues';

	public function modelName()
	{
		return "Story_Arc";
	}

	public function dboName()
	{
		return '\model\media\Story_ArcDBO';
	}

	public function tableName() { return Story_Arc::TABLE; }
	public function tablePK() { return Story_Arc::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Story_Arc::name)
		);
	}

	public function allColumnNames()
	{
		return array(
			Story_Arc::id,
			Story_Arc::publisher_id,
			Story_Arc::created,
			Story_Arc::name,
			Story_Arc::desc,
			Story_Arc::pub_active,
			Story_Arc::pub_wanted,
			Story_Arc::pub_cycle,
			Story_Arc::pub_available,
			Story_Arc::pub_count,
			Story_Arc::xurl,
			Story_Arc::xsource,
			Story_Arc::xid,
			Story_Arc::xupdated
		);
	}

	public function allAttributes()
	{
		return array(
			Story_Arc::created,
			Story_Arc::name,
			Story_Arc::desc,
			Story_Arc::pub_active,
			Story_Arc::pub_wanted,
			Story_Arc::pub_cycle,
			Story_Arc::pub_available,
			Story_Arc::pub_count,
			Story_Arc::xurl,
			Story_Arc::xsource,
			Story_Arc::xid,
			Story_Arc::xupdated
		);
	}

	public function allForeignKeys()
	{
		return array(Story_Arc::publisher_id);
	}

	public function allRelationshipNames()
	{
		return array(
			Story_Arc::publisher,
			Story_Arc::story_arc_characters,
			Story_Arc::story_arc_publication,
			Story_Arc::story_arc_series,
			Story_Arc::reading_queues
		);
	}

	public function attributes()
	{
		return array(
			Story_Arc::created => array('type' => 'DATE'),
			Story_Arc::name => array('length' => 256,'type' => 'TEXT'),
			Story_Arc::desc => array('length' => 4096,'type' => 'TEXT'),
			Story_Arc::pub_active => array('type' => 'BOOLEAN'),
			Story_Arc::pub_wanted => array('type' => 'BOOLEAN'),
			Story_Arc::pub_cycle => array('type' => 'INTEGER'),
			Story_Arc::pub_available => array('type' => 'INTEGER'),
			Story_Arc::pub_count => array('type' => 'INTEGER'),
			Story_Arc::xurl => array('length' => 1024,'type' => 'TEXT'),
			Story_Arc::xsource => array('length' => 256,'type' => 'TEXT'),
			Story_Arc::xid => array('length' => 256,'type' => 'TEXT'),
			Story_Arc::xupdated => array('type' => 'DATE')
		);
	}

	public function relationships()
	{
		return array(
			Story_Arc::publisher => array(
				'destination' => 'Publisher',
				'ownsDestination' => false,
				'isMandatory' => true,
				'isToMany' => false,
				'joins' => array( 'publisher_id' => 'id')
			),
			Story_Arc::story_arc_characters => array(
				'destination' => 'Story_Arc_Character',
				'ownsDestination' => true,
				'isMandatory' => false,
				'isToMany' => true,
				'joins' => array( 'id' => 'story_arc_id')
			),
			Story_Arc::story_arc_publication => array(
				'destination' => 'Story_Arc_Publication',
				'ownsDestination' => true,
				'isMandatory' => false,
				'isToMany' => true,
				'joins' => array( 'id' => 'story_arc_id')
			),
			Story_Arc::story_arc_series => array(
				'destination' => 'Story_Arc_Series',
				'ownsDestination' => true,
				'isMandatory' => false,
				'isToMany' => true,
				'joins' => array( 'id' => 'story_arc_id')
			),
			Story_Arc::reading_queues => array(
				'destination' => 'Reading_Queue',
				'ownsDestination' => true,
				'isMandatory' => false,
				'isToMany' => true,
				'joins' => array( 'id' => 'story_arc_id')
			)
		);
	}

	public function searchQualifiers( array $query )
	{
		$qualifiers = array();
		if ( is_array($query) ) {
			foreach( $query as $attr => $value ) {
				switch ($attr) {
			// Story_Arc::id == INTEGER

			// Story_Arc::publisher_id == INTEGER
				case Story_Arc::publisher_id:
					if ( intval($value) > 0 ) {
						$qualifiers[Story_Arc::publisher_id] = Qualifier::Equals( Story_Arc::publisher_id, intval($value) );
					}
					break;

			// Story_Arc::created == DATE

			// Story_Arc::name == TEXT
				case Story_Arc::name:
					if (strlen($value) > 0) {
						$qualifiers[Story_Arc::name] = Qualifier::Equals( Story_Arc::name, $value );
					}
					break;

			// Story_Arc::desc == TEXT
				case Story_Arc::desc:
					if (strlen($value) > 0) {
						$qualifiers[Story_Arc::desc] = Qualifier::Equals( Story_Arc::desc, $value );
					}
					break;

			// Story_Arc::pub_active == BOOLEAN
				case Story_Arc::pub_active:
					$v = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
					if (is_null($v) == false) {
						$qualifiers[Story_Arc::pub_active] = Qualifier::Equals( Story_Arc::pub_active, $v );
					}
					break;

			// Story_Arc::pub_wanted == BOOLEAN
				case Story_Arc::pub_wanted:
					$v = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
					if (is_null($v) == false) {
						$qualifiers[Story_Arc::pub_wanted] = Qualifier::Equals( Story_Arc::pub_wanted, $v );
					}
					break;

			// Story_Arc::pub_cycle == INTEGER
				case Story_Arc::pub_cycle:
					if ( intval($value) > 0 ) {
						$qualifiers[Story_Arc::pub_cycle] = Qualifier::Equals( Story_Arc::pub_cycle, intval($value) );
					}
					break;

			// Story_Arc::pub_available == INTEGER
				case Story_Arc::pub_available:
					if ( intval($value) > 0 ) {
						$qualifiers[Story_Arc::pub_available] = Qualifier::Equals( Story_Arc::pub_available, intval($value) );
					}
					break;

			// Story_Arc::pub_count == INTEGER
				case Story_Arc::pub_count:
					if ( intval($value) > 0 ) {
						$qualifiers[Story_Arc::pub_count] = Qualifier::Equals( Story_Arc::pub_count, intval($value) );
					}
					break;

			// Story_Arc::xurl == TEXT
				case Story_Arc::xurl:
					if (strlen($value) > 0) {
						$qualifiers[Story_Arc::xurl] = Qualifier::Equals( Story_Arc::xurl, $value );
					}
					break;

			// Story_Arc::xsource == TEXT
				case Story_Arc::xsource:
					if (strlen($value) > 0) {
						$qualifiers[Story_Arc::xsource] = Qualifier::Equals( Story_Arc::xsource, $value );
					}
					break;

			// Story_Arc::xid == TEXT
				case Story_Arc::xid:
					if (strlen($value) > 0) {
						$qualifiers[Story_Arc::xid] = Qualifier::Equals( Story_Arc::xid, $value );
					}
					break;

			// Story_Arc::xupdated == DATE

				default:
					/* no type specified for Story_Arc::xupdated */
					break;
				}
			}
		}
		return $qualifiers;
	}

	/**
	 *	Simple fetches
	 */



	public function allForName($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Story_Arc::name, $value, null, $limit);
	}


	public function allForDesc($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Story_Arc::desc, $value, null, $limit);
	}




	public function allForPub_cycle($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Story_Arc::pub_cycle, $value, null, $limit);
	}

	public function allForPub_available($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Story_Arc::pub_available, $value, null, $limit);
	}

	public function allForPub_count($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Story_Arc::pub_count, $value, null, $limit);
	}

	public function allForXurl($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Story_Arc::xurl, $value, null, $limit);
	}


	public function allForXsource($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Story_Arc::xsource, $value, null, $limit);
	}


	public function allForXid($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Story_Arc::xid, $value, null, $limit);
	}




	/**
	 * Simple relationship fetches
	 */
	public function allForPublisher($obj, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForFK(Story_Arc::publisher_id, $obj, $this->sortOrder(), $limit);
	}

	public function countForPublisher($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Story_Arc::publisher_id, $obj );
		}
		return false;
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "publisher":
					return array( Story_Arc::publisher_id, "id"  );
					break;
				case "story_arc_character":
					return array( Story_Arc::id, "story_arc_id"  );
					break;
				case "story_arc_publication":
					return array( Story_Arc::id, "story_arc_id"  );
					break;
				case "story_arc_series":
					return array( Story_Arc::id, "story_arc_id"  );
					break;
				case "reading_queue":
					return array( Story_Arc::id, "story_arc_id"  );
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
				$default_created = $this->attributeDefaultValue( null, null, Story_Arc::created);
				if ( is_null( $default_created ) == false ) {
					$values['created'] = $default_created;
				}
			}
			if ( isset($values['name']) == false ) {
				$default_name = $this->attributeDefaultValue( null, null, Story_Arc::name);
				if ( is_null( $default_name ) == false ) {
					$values['name'] = $default_name;
				}
			}
			if ( isset($values['desc']) == false ) {
				$default_desc = $this->attributeDefaultValue( null, null, Story_Arc::desc);
				if ( is_null( $default_desc ) == false ) {
					$values['desc'] = $default_desc;
				}
			}
			if ( isset($values['pub_active']) == false ) {
				$default_pub_active = $this->attributeDefaultValue( null, null, Story_Arc::pub_active);
				if ( is_null( $default_pub_active ) == false ) {
					$values['pub_active'] = $default_pub_active;
				}
			}
			if ( isset($values['pub_wanted']) == false ) {
				$default_pub_wanted = $this->attributeDefaultValue( null, null, Story_Arc::pub_wanted);
				if ( is_null( $default_pub_wanted ) == false ) {
					$values['pub_wanted'] = $default_pub_wanted;
				}
			}
			if ( isset($values['pub_cycle']) == false ) {
				$default_pub_cycle = $this->attributeDefaultValue( null, null, Story_Arc::pub_cycle);
				if ( is_null( $default_pub_cycle ) == false ) {
					$values['pub_cycle'] = $default_pub_cycle;
				}
			}
			if ( isset($values['pub_available']) == false ) {
				$default_pub_available = $this->attributeDefaultValue( null, null, Story_Arc::pub_available);
				if ( is_null( $default_pub_available ) == false ) {
					$values['pub_available'] = $default_pub_available;
				}
			}
			if ( isset($values['pub_count']) == false ) {
				$default_pub_count = $this->attributeDefaultValue( null, null, Story_Arc::pub_count);
				if ( is_null( $default_pub_count ) == false ) {
					$values['pub_count'] = $default_pub_count;
				}
			}
			if ( isset($values['xurl']) == false ) {
				$default_xurl = $this->attributeDefaultValue( null, null, Story_Arc::xurl);
				if ( is_null( $default_xurl ) == false ) {
					$values['xurl'] = $default_xurl;
				}
			}
			if ( isset($values['xsource']) == false ) {
				$default_xsource = $this->attributeDefaultValue( null, null, Story_Arc::xsource);
				if ( is_null( $default_xsource ) == false ) {
					$values['xsource'] = $default_xsource;
				}
			}
			if ( isset($values['xid']) == false ) {
				$default_xid = $this->attributeDefaultValue( null, null, Story_Arc::xid);
				if ( is_null( $default_xid ) == false ) {
					$values['xid'] = $default_xid;
				}
			}
			if ( isset($values['xupdated']) == false ) {
				$default_xupdated = $this->attributeDefaultValue( null, null, Story_Arc::xupdated);
				if ( is_null( $default_xupdated ) == false ) {
					$values['xupdated'] = $default_xupdated;
				}
			}

			// default conversion for relationships
			if ( isset($values['publisher']) ) {
				$local_publisher = $values['publisher'];
				if ( $local_publisher instanceof PublisherDBO) {
					$values[Story_Arc::publisher_id] = $local_publisher->id;
				}
				else if ( is_integer( $local_publisher) ) {
					$params[Story_Arc::publisher_id] = $local_publisher;
				}
			}
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Story_Arc ) {
			if ( isset($values['publisher']) ) {
				$local_publisher = $values['publisher'];
				if ( $local_publisher instanceof PublisherDBO) {
					$values[Story_Arc::publisher_id] = $local_publisher->id;
				}
				else if ( is_integer( $local_publisher) ) {
					$params[Story_Arc::publisher_id] = $values['publisher'];
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
		if ( $object instanceof Story_ArcDBO )
		{
			// does not own publisher Publisher
			$story_arc_character_model = Model::Named('Story_Arc_Character');
			if ( $story_arc_character_model->deleteAllForKeyValue(Story_Arc_Character::story_arc_id, $object->id) == false ) {
				return false;
			}
			$story_arc_publication_model = Model::Named('Story_Arc_Publication');
			if ( $story_arc_publication_model->deleteAllForKeyValue(Story_Arc_Publication::story_arc_id, $object->id) == false ) {
				return false;
			}
			$story_arc_series_model = Model::Named('Story_Arc_Series');
			if ( $story_arc_series_model->deleteAllForKeyValue(Story_Arc_Series::story_arc_id, $object->id) == false ) {
				return false;
			}
			$reading_queue_model = Model::Named('Reading_Queue');
			if ( $reading_queue_model->deleteAllForKeyValue(Reading_Queue::story_arc_id, $object->id) == false ) {
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
	public function storyArcLike( $name )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		$qualifiers[] = Qualifier::Like( 'name', $name, SQL::SQL_LIKE_BOTH);

		if ( count($qualifiers) > 0 ) {
			$select->where( Qualifier::Combine( 'OR', $qualifiers ));
		}

		$result = $select->fetchAll();
		return $result;
	}

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
				Story_Arc::name
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesMap() {
		return array(
			Story_Arc::publisher_id => Model::TO_ONE_TYPE,
			Story_Arc::created => Model::DATE_TYPE,
			Story_Arc::name => Model::TEXT_TYPE,
			Story_Arc::desc => Model::TEXTAREA_TYPE,
			Story_Arc::pub_active => Model::FLAG_TYPE,
			Story_Arc::pub_wanted => Model::FLAG_TYPE,
			Story_Arc::pub_cycle => Model::INT_TYPE,
			Story_Arc::pub_available => Model::INT_TYPE,
			Story_Arc::pub_count => Model::INT_TYPE,
			Story_Arc::xurl => Model::TEXTAREA_TYPE,
			Story_Arc::xsource => Model::TEXT_TYPE,
			Story_Arc::xid => Model::TEXT_TYPE,
			Story_Arc::xupdated => Model::DATE_TYPE
		);
	}

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) === false || is_null($object) == true) {
			switch ($attr) {
				case Story_Arc::pub_active:
					return Model::TERTIARY_TRUE;
				case Story_Arc::pub_wanted:
					return Model::TERTIARY_FALSE;
				case Story_Arc::pub_cycle:
					return 0;
				case Story_Arc::pub_available:
					return 0;
				case Story_Arc::pub_count:
					return 0;
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
				case Story_Arc::publisher_id:
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
				Story_Arc::publisher_id,
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
				Story_Arc::created,
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
				Story_Arc::name,
				"FIELD_EMPTY"
			);
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
	function validate_pub_active($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false  ) {
			return null;
		}

		// boolean

		// Returns TRUE for "1", "true", "on" and "yes"
		// Returns FALSE for "0", "false", "off" and "no"
		// Returns NULL otherwise.
		$v = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
		if (is_null($v)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Story_Arc::pub_active,
				"FILTER_VALIDATE_BOOLEAN"
			);
		}
		return null;
	}
	function validate_pub_wanted($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false  ) {
			return null;
		}

		// boolean

		// Returns TRUE for "1", "true", "on" and "yes"
		// Returns FALSE for "0", "false", "off" and "no"
		// Returns NULL otherwise.
		$v = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
		if (is_null($v)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Story_Arc::pub_wanted,
				"FILTER_VALIDATE_BOOLEAN"
			);
		}
		return null;
	}
	function validate_pub_cycle($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Story_Arc::pub_cycle,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
	function validate_pub_available($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Story_Arc::pub_available,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
	function validate_pub_count($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Story_Arc::pub_count,
				"FILTER_VALIDATE_INT"
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
