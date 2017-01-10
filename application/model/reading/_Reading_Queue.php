<?php

namespace model\reading;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\reading\Reading_QueueDBO as Reading_QueueDBO;

/* import related objects */
use \model\user\Users as Users;
use \model\user\UsersDBO as UsersDBO;
use \model\media\Series as Series;
use \model\media\SeriesDBO as SeriesDBO;
use \model\media\Story_Arc as Story_Arc;
use \model\media\Story_ArcDBO as Story_ArcDBO;

/** Generated class, do not edit.
 */
abstract class _Reading_Queue extends Model
{
	const TABLE = 'reading_queue';

	// attribute keys
	const id = 'id';
	const user_id = 'user_id';
	const series_id = 'series_id';
	const story_arc_id = 'story_arc_id';
	const created = 'created';
	const title = 'title';
	const favorite = 'favorite';
	const pub_count = 'pub_count';
	const pub_read = 'pub_read';
	const queue_order = 'queue_order';

	// relationship keys
	const user = 'user';
	const series = 'series';
	const story_arc = 'story_arc';

	public function modelName()
	{
		return "Reading_Queue";
	}

	public function dboName()
	{
		return '\model\reading\Reading_QueueDBO';
	}

	public function tableName() { return Reading_Queue::TABLE; }
	public function tablePK() { return Reading_Queue::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Reading_Queue::queue_order),
			array( 'asc' => Reading_Queue::title)
		);
	}

	public function allColumnNames()
	{
		return array(
			Reading_Queue::id,
			Reading_Queue::user_id,
			Reading_Queue::series_id,
			Reading_Queue::story_arc_id,
			Reading_Queue::created,
			Reading_Queue::title,
			Reading_Queue::favorite,
			Reading_Queue::pub_count,
			Reading_Queue::pub_read,
			Reading_Queue::queue_order
		);
	}

	public function allAttributes()
	{
		return array(
			Reading_Queue::created,
			Reading_Queue::title,
			Reading_Queue::favorite,
			Reading_Queue::pub_count,
			Reading_Queue::pub_read,
			Reading_Queue::queue_order
		);
	}

	public function allForeignKeys()
	{
		return array(Reading_Queue::user_id,
			Reading_Queue::series_id,
			Reading_Queue::story_arc_id);
	}

	public function allRelationshipNames()
	{
		return array(
			Reading_Queue::user,
			Reading_Queue::series,
			Reading_Queue::story_arc
		);
	}

	public function searchQualifiers( array $query )
	{
		$qualifiers = array();
		if ( is_array($query) ) {
			foreach( $query as $attr => $value ) {
				switch ($attr) {
			// Reading_Queue::id == INTEGER

			// Reading_Queue::user_id == INTEGER
				case Reading_Queue::user_id:
					if ( intval($value) > 0 ) {
						$qualifiers[Reading_Queue::user_id] = Qualifier::Equals( Reading_Queue::user_id, intval($value) );
					}
					break;

			// Reading_Queue::series_id == INTEGER
				case Reading_Queue::series_id:
					if ( intval($value) > 0 ) {
						$qualifiers[Reading_Queue::series_id] = Qualifier::Equals( Reading_Queue::series_id, intval($value) );
					}
					break;

			// Reading_Queue::story_arc_id == INTEGER
				case Reading_Queue::story_arc_id:
					if ( intval($value) > 0 ) {
						$qualifiers[Reading_Queue::story_arc_id] = Qualifier::Equals( Reading_Queue::story_arc_id, intval($value) );
					}
					break;

			// Reading_Queue::created == DATE

			// Reading_Queue::title == TEXT
				case Reading_Queue::title:
					if (strlen($value) > 0) {
						$qualifiers[Reading_Queue::title] = Qualifier::Equals( Reading_Queue::title, $value );
					}
					break;

			// Reading_Queue::favorite == BOOLEAN
				case Reading_Queue::favorite:
					$v = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
					if (is_null($v) == false) {
						$qualifiers[Reading_Queue::favorite] = Qualifier::Equals( Reading_Queue::favorite, $v );
					}
					break;

			// Reading_Queue::pub_count == INTEGER
				case Reading_Queue::pub_count:
					if ( intval($value) > 0 ) {
						$qualifiers[Reading_Queue::pub_count] = Qualifier::Equals( Reading_Queue::pub_count, intval($value) );
					}
					break;

			// Reading_Queue::pub_read == INTEGER
				case Reading_Queue::pub_read:
					if ( intval($value) > 0 ) {
						$qualifiers[Reading_Queue::pub_read] = Qualifier::Equals( Reading_Queue::pub_read, intval($value) );
					}
					break;

			// Reading_Queue::queue_order == INTEGER
				case Reading_Queue::queue_order:
					if ( intval($value) > 0 ) {
						$qualifiers[Reading_Queue::queue_order] = Qualifier::Equals( Reading_Queue::queue_order, intval($value) );
					}
					break;

				default:
					/* no type specified for Reading_Queue::queue_order */
					break;
				}
			}
		}
		return $qualifiers;
	}

	/**
	 *	Simple fetches
	 */





	public function allForTitle($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Reading_Queue::title, $value, null, $limit);
	}



	public function allForPub_count($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Reading_Queue::pub_count, $value, null, $limit);
	}

	public function allForPub_read($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Reading_Queue::pub_read, $value, null, $limit);
	}

	public function allForQueue_order($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Reading_Queue::queue_order, $value, null, $limit);
	}


	/**
	 * Simple relationship fetches
	 */
	public function allForUser($obj, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForFK(Reading_Queue::user_id, $obj, $this->sortOrder(), $limit);
	}

	public function countForUser($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Reading_Queue::user_id, $obj );
		}
		return false;
	}
	public function allForSeries($obj, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForFK(Reading_Queue::series_id, $obj, $this->sortOrder(), $limit);
	}

	public function countForSeries($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Reading_Queue::series_id, $obj );
		}
		return false;
	}
	public function allForStory_arc($obj, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForFK(Reading_Queue::story_arc_id, $obj, $this->sortOrder(), $limit);
	}

	public function countForStory_arc($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Reading_Queue::story_arc_id, $obj );
		}
		return false;
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "users":
					return array( Reading_Queue::user_id, "id"  );
					break;
				case "series":
					return array( Reading_Queue::series_id, "id"  );
					break;
				case "story_arc":
					return array( Reading_Queue::story_arc_id, "id"  );
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
				$default_created = $this->attributeDefaultValue( null, null, Reading_Queue::created);
				if ( is_null( $default_created ) == false ) {
					$values['created'] = $default_created;
				}
			}
			if ( isset($values['title']) == false ) {
				$default_title = $this->attributeDefaultValue( null, null, Reading_Queue::title);
				if ( is_null( $default_title ) == false ) {
					$values['title'] = $default_title;
				}
			}
			if ( isset($values['favorite']) == false ) {
				$default_favorite = $this->attributeDefaultValue( null, null, Reading_Queue::favorite);
				if ( is_null( $default_favorite ) == false ) {
					$values['favorite'] = $default_favorite;
				}
			}
			if ( isset($values['pub_count']) == false ) {
				$default_pub_count = $this->attributeDefaultValue( null, null, Reading_Queue::pub_count);
				if ( is_null( $default_pub_count ) == false ) {
					$values['pub_count'] = $default_pub_count;
				}
			}
			if ( isset($values['pub_read']) == false ) {
				$default_pub_read = $this->attributeDefaultValue( null, null, Reading_Queue::pub_read);
				if ( is_null( $default_pub_read ) == false ) {
					$values['pub_read'] = $default_pub_read;
				}
			}
			if ( isset($values['queue_order']) == false ) {
				$default_queue_order = $this->attributeDefaultValue( null, null, Reading_Queue::queue_order);
				if ( is_null( $default_queue_order ) == false ) {
					$values['queue_order'] = $default_queue_order;
				}
			}

			// default conversion for relationships
			if ( isset($values['user']) ) {
				$local_user = $values['user'];
				if ( $local_user instanceof UsersDBO) {
					$values[Reading_Queue::user_id] = $local_user->id;
				}
				else if ( is_integer( $local_user) ) {
					$params[Reading_Queue::user_id] = $local_user;
				}
			}
			if ( isset($values['series']) ) {
				$local_series = $values['series'];
				if ( $local_series instanceof SeriesDBO) {
					$values[Reading_Queue::series_id] = $local_series->id;
				}
				else if ( is_integer( $local_series) ) {
					$params[Reading_Queue::series_id] = $local_series;
				}
			}
			if ( isset($values['story_arc']) ) {
				$local_story_arc = $values['story_arc'];
				if ( $local_story_arc instanceof Story_ArcDBO) {
					$values[Reading_Queue::story_arc_id] = $local_story_arc->id;
				}
				else if ( is_integer( $local_story_arc) ) {
					$params[Reading_Queue::story_arc_id] = $local_story_arc;
				}
			}
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Reading_Queue ) {
			if ( isset($values['user']) ) {
				$local_user = $values['user'];
				if ( $local_user instanceof UsersDBO) {
					$values[Reading_Queue::user_id] = $local_user->id;
				}
				else if ( is_integer( $local_user) ) {
					$params[Reading_Queue::user_id] = $values['user'];
				}
			}
			if ( isset($values['series']) ) {
				$local_series = $values['series'];
				if ( $local_series instanceof SeriesDBO) {
					$values[Reading_Queue::series_id] = $local_series->id;
				}
				else if ( is_integer( $local_series) ) {
					$params[Reading_Queue::series_id] = $values['series'];
				}
			}
			if ( isset($values['story_arc']) ) {
				$local_story_arc = $values['story_arc'];
				if ( $local_story_arc instanceof Story_ArcDBO) {
					$values[Reading_Queue::story_arc_id] = $local_story_arc->id;
				}
				else if ( is_integer( $local_story_arc) ) {
					$params[Reading_Queue::story_arc_id] = $values['story_arc'];
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
		if ( $object instanceof Reading_QueueDBO )
		{
			// does not own user Users
			// does not own series Series
			// does not own story_arc Story_Arc
			return parent::deleteObject($object);
		}

		return false;
	}

	public function deleteAllForUser(UsersDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForUser($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForUser($obj);
			}
		}
		return $success;
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

	/**
	 * Named fetches
	 */
	public function objectForUserAndSeries(UsersDBO $user,SeriesDBO $series )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		$qualifiers[] = Qualifier::FK( 'user_id', $user);
		$qualifiers[] = Qualifier::FK( 'series_id', $series);

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
				throw new \Exception( "objectForUserAndSeries expected 1 result, but fetched " . count($result) );
			}
		}

		return false;
	}

	public function objectForUserAndStoryArc(UsersDBO $user,Story_ArcDBO $story )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		$qualifiers[] = Qualifier::FK( 'user_id', $user);
		$qualifiers[] = Qualifier::FK( 'story_arc_id', $story);

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
				throw new \Exception( "objectForUserAndStoryArc expected 1 result, but fetched " . count($result) );
			}
		}

		return false;
	}

	public function allForUserFavorites(UsersDBO $user, $favorite, $read )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		$qualifiers[] = Qualifier::FK( 'user_id', $user);
		if ( isset($favorite)) {
			$qualifiers[] = Qualifier::Equals( 'favorite', $favorite);
		}
		if ( isset($read)) {
			$qualifiers[] = Qualifier::Equals( 'read', $read);
		}

		if ( count($qualifiers) > 0 ) {
			$select->where( Qualifier::Combine( 'AND', $qualifiers ));
		}

		$result = $select->fetchAll();
		return $result;
	}


	/**
	 * Attribute editing
	 */
	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Reading_Queue::title,
				Reading_Queue::queue_order
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesMap() {
		return array(
			Reading_Queue::user_id => Model::TO_ONE_TYPE,
			Reading_Queue::series_id => Model::TO_ONE_TYPE,
			Reading_Queue::story_arc_id => Model::TO_ONE_TYPE,
			Reading_Queue::created => Model::DATE_TYPE,
			Reading_Queue::title => Model::TEXT_TYPE,
			Reading_Queue::favorite => Model::FLAG_TYPE,
			Reading_Queue::pub_count => Model::INT_TYPE,
			Reading_Queue::pub_read => Model::INT_TYPE,
			Reading_Queue::queue_order => Model::INT_TYPE
		);
	}

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) === false || is_null($object) == true) {
			switch ($attr) {
				case Reading_Queue::favorite:
					return Model::TERTIARY_TRUE;
				case Reading_Queue::pub_count:
					return 0;
				case Reading_Queue::pub_read:
					return 0;
				case Reading_Queue::queue_order:
					return 1000;
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
				case Reading_Queue::user_id:
					$users_model = Model::Named('Users');
					$fkObject = $users_model->objectForId( $value );
					break;
				case Reading_Queue::series_id:
					$series_model = Model::Named('Series');
					$fkObject = $series_model->objectForId( $value );
					break;
				case Reading_Queue::story_arc_id:
					$story_arc_model = Model::Named('Story_Arc');
					$fkObject = $story_arc_model->objectForId( $value );
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
	function validate_user_id($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Reading_Queue::user_id,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_series_id($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_story_arc_id($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
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
				Reading_Queue::created,
				"IMMUTABLE"
			);
		}
		return null;
	}
	function validate_title($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Reading_Queue::title,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_favorite($object = null, $value)
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
				Reading_Queue::favorite,
				"FILTER_VALIDATE_BOOLEAN"
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
				Reading_Queue::pub_count,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
	function validate_pub_read($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Reading_Queue::pub_read,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
	function validate_queue_order($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Reading_Queue::queue_order,
				"FIELD_EMPTY"
			);
		}

		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Reading_Queue::queue_order,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
}

?>
