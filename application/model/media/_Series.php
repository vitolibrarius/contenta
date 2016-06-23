<?php

namespace model\media;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\media\SeriesDBO as SeriesDBO;

/* import related objects */
use \model\media\Series_Alias as Series_Alias;
use \model\media\Series_AliasDBO as Series_AliasDBO;
use \model\media\Publisher as Publisher;
use \model\media\PublisherDBO as PublisherDBO;
use \model\media\Publication as Publication;
use \model\media\PublicationDBO as PublicationDBO;
use \model\media\Series_Characters as Series_Characters;
use \model\media\Series_CharactersDBO as Series_CharactersDBO;
use \model\media\Story_Arc_Series as Story_Arc_Series;
use \model\media\Story_Arc_SeriesDBO as Story_Arc_SeriesDBO;
use \model\media\User_Series as User_Series;
use \model\media\User_SeriesDBO as User_SeriesDBO;

/** Sample Creation script */
		/** SERIES */
/*
		$sql = "CREATE TABLE IF NOT EXISTS series ( "
			. Series::id . " INTEGER PRIMARY KEY, "
			. Series::publisher_id . " INTEGER, "
			. Series::parent_id . " INTEGER, "
			. Series::created . " INTEGER, "
			. Series::name . " TEXT, "
			. Series::search_name . " TEXT, "
			. Series::desc . " TEXT, "
			. Series::start_year . " INTEGER, "
			. Series::issue_count . " INTEGER, "
			. Series::pub_active . " INTEGER, "
			. Series::pub_wanted . " INTEGER, "
			. Series::pub_available . " INTEGER, "
			. Series::pub_cycle . " INTEGER, "
			. Series::pub_count . " INTEGER, "
			. Series::xurl . " TEXT, "
			. Series::xsource . " TEXT, "
			. Series::xid . " TEXT, "
			. Series::xupdated . " INTEGER, "
			. "FOREIGN KEY (". Series::publisher_id .") REFERENCES " . Publisher::TABLE . "(" . Publisher::id . ")"
		. ")";
		$this->sqlite_execute( "series", $sql, "Create table series" );

		$sql = 'CREATE  INDEX IF NOT EXISTS series_name on series (name)';
		$this->sqlite_execute( "series", $sql, "Index on series (name)" );
		$sql = 'CREATE  INDEX IF NOT EXISTS series_search_name on series (search_name)';
		$this->sqlite_execute( "series", $sql, "Index on series (search_name)" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS series_xidxsource on series (xid,xsource)';
		$this->sqlite_execute( "series", $sql, "Index on series (xid,xsource)" );
*/
abstract class _Series extends Model
{
	const TABLE = 'series';
	const id = 'id';
	const publisher_id = 'publisher_id';
	const parent_id = 'parent_id';
	const created = 'created';
	const name = 'name';
	const search_name = 'search_name';
	const desc = 'desc';
	const start_year = 'start_year';
	const issue_count = 'issue_count';
	const pub_active = 'pub_active';
	const pub_wanted = 'pub_wanted';
	const pub_available = 'pub_available';
	const pub_cycle = 'pub_cycle';
	const pub_count = 'pub_count';
	const xurl = 'xurl';
	const xsource = 'xsource';
	const xid = 'xid';
	const xupdated = 'xupdated';

	public function tableName() { return Series::TABLE; }
	public function tablePK() { return Series::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Series::name),
			array( 'asc' => Series::start_year)
		);
	}

	public function allColumnNames()
	{
		return array(
			Series::id,
			Series::publisher_id,
			Series::parent_id,
			Series::created,
			Series::name,
			Series::search_name,
			Series::desc,
			Series::start_year,
			Series::issue_count,
			Series::pub_active,
			Series::pub_wanted,
			Series::pub_available,
			Series::pub_cycle,
			Series::pub_count,
			Series::xurl,
			Series::xsource,
			Series::xid,
			Series::xupdated
		);
	}

	/**
	 *	Simple fetches
	 */


	public function allForParent_id($value)
	{
		return $this->allObjectsForKeyValue(Series::parent_id, $value);
	}


	public function allForName($value)
	{
		return $this->allObjectsForKeyValue(Series::name, $value);
	}


	public function allForSearch_name($value)
	{
		return $this->allObjectsForKeyValue(Series::search_name, $value);
	}


	public function allForDesc($value)
	{
		return $this->allObjectsForKeyValue(Series::desc, $value);
	}


	public function allForStart_year($value)
	{
		return $this->allObjectsForKeyValue(Series::start_year, $value);
	}

	public function allForIssue_count($value)
	{
		return $this->allObjectsForKeyValue(Series::issue_count, $value);
	}



	public function allForPub_available($value)
	{
		return $this->allObjectsForKeyValue(Series::pub_available, $value);
	}

	public function allForPub_cycle($value)
	{
		return $this->allObjectsForKeyValue(Series::pub_cycle, $value);
	}

	public function allForPub_count($value)
	{
		return $this->allObjectsForKeyValue(Series::pub_count, $value);
	}

	public function allForXurl($value)
	{
		return $this->allObjectsForKeyValue(Series::xurl, $value);
	}


	public function allForXsource($value)
	{
		return $this->allObjectsForKeyValue(Series::xsource, $value);
	}


	public function allForXid($value)
	{
		return $this->allObjectsForKeyValue(Series::xid, $value);
	}




	public function allForPublisher($obj)
	{
		return $this->allObjectsForFK(Series::publisher_id, $obj, $this->sortOrder(), 50);
	}

	public function countForPublisher($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Series::publisher_id, $obj );
		}
		return false;
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "series_alias":
					return array( Series::id, "series_id"  );
					break;
				case "publisher":
					return array( Series::publisher_id, "id"  );
					break;
				case "publication":
					return array( Series::id, "series_id"  );
					break;
				case "series_characters":
					return array( Series::id, "series_id"  );
					break;
				case "story_arc_series":
					return array( Series::id, "series_id"  );
					break;
				case "user_series":
					return array( Series::id, "series_id"  );
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
			if ( isset($values['parent_id']) == false ) {
				$default_parent_id = $this->attributeDefaultValue( null, null, Series::parent_id);
				if ( is_null( $default_parent_id ) == false ) {
					$values['parent_id'] = $default_parent_id;
				}
			}
			if ( isset($values['created']) == false ) {
				$default_created = $this->attributeDefaultValue( null, null, Series::created);
				if ( is_null( $default_created ) == false ) {
					$values['created'] = $default_created;
				}
			}
			if ( isset($values['name']) == false ) {
				$default_name = $this->attributeDefaultValue( null, null, Series::name);
				if ( is_null( $default_name ) == false ) {
					$values['name'] = $default_name;
				}
			}
			if ( isset($values['search_name']) == false ) {
				$default_search_name = $this->attributeDefaultValue( null, null, Series::search_name);
				if ( is_null( $default_search_name ) == false ) {
					$values['search_name'] = $default_search_name;
				}
			}
			if ( isset($values['desc']) == false ) {
				$default_desc = $this->attributeDefaultValue( null, null, Series::desc);
				if ( is_null( $default_desc ) == false ) {
					$values['desc'] = $default_desc;
				}
			}
			if ( isset($values['start_year']) == false ) {
				$default_start_year = $this->attributeDefaultValue( null, null, Series::start_year);
				if ( is_null( $default_start_year ) == false ) {
					$values['start_year'] = $default_start_year;
				}
			}
			if ( isset($values['issue_count']) == false ) {
				$default_issue_count = $this->attributeDefaultValue( null, null, Series::issue_count);
				if ( is_null( $default_issue_count ) == false ) {
					$values['issue_count'] = $default_issue_count;
				}
			}
			if ( isset($values['pub_active']) == false ) {
				$default_pub_active = $this->attributeDefaultValue( null, null, Series::pub_active);
				if ( is_null( $default_pub_active ) == false ) {
					$values['pub_active'] = $default_pub_active;
				}
			}
			if ( isset($values['pub_wanted']) == false ) {
				$default_pub_wanted = $this->attributeDefaultValue( null, null, Series::pub_wanted);
				if ( is_null( $default_pub_wanted ) == false ) {
					$values['pub_wanted'] = $default_pub_wanted;
				}
			}
			if ( isset($values['pub_available']) == false ) {
				$default_pub_available = $this->attributeDefaultValue( null, null, Series::pub_available);
				if ( is_null( $default_pub_available ) == false ) {
					$values['pub_available'] = $default_pub_available;
				}
			}
			if ( isset($values['pub_cycle']) == false ) {
				$default_pub_cycle = $this->attributeDefaultValue( null, null, Series::pub_cycle);
				if ( is_null( $default_pub_cycle ) == false ) {
					$values['pub_cycle'] = $default_pub_cycle;
				}
			}
			if ( isset($values['pub_count']) == false ) {
				$default_pub_count = $this->attributeDefaultValue( null, null, Series::pub_count);
				if ( is_null( $default_pub_count ) == false ) {
					$values['pub_count'] = $default_pub_count;
				}
			}
			if ( isset($values['xurl']) == false ) {
				$default_xurl = $this->attributeDefaultValue( null, null, Series::xurl);
				if ( is_null( $default_xurl ) == false ) {
					$values['xurl'] = $default_xurl;
				}
			}
			if ( isset($values['xsource']) == false ) {
				$default_xsource = $this->attributeDefaultValue( null, null, Series::xsource);
				if ( is_null( $default_xsource ) == false ) {
					$values['xsource'] = $default_xsource;
				}
			}
			if ( isset($values['xid']) == false ) {
				$default_xid = $this->attributeDefaultValue( null, null, Series::xid);
				if ( is_null( $default_xid ) == false ) {
					$values['xid'] = $default_xid;
				}
			}
			if ( isset($values['xupdated']) == false ) {
				$default_xupdated = $this->attributeDefaultValue( null, null, Series::xupdated);
				if ( is_null( $default_xupdated ) == false ) {
					$values['xupdated'] = $default_xupdated;
				}
			}

			// default conversion for relationships
			if ( isset($values['publisher']) ) {
				$local_publisher = $values['publisher'];
				if ( $local_publisher instanceof PublisherDBO) {
					$values[Series::publisher_id] = $local_publisher->id;
				}
				else if ( is_integer( $local_publisher) ) {
					$params[Series::publisher_id] = $local_publisher;
				}
			}
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Series ) {
			if ( isset($values['publisher']) ) {
				$local_publisher = $values['publisher'];
				if ( $local_publisher instanceof PublisherDBO) {
					$values[Series::publisher_id] = $local_publisher->id;
				}
				else if ( is_integer( $local_publisher) ) {
					$params[Series::publisher_id] = $values['publisher'];
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
		if ( $object instanceof SeriesDBO )
		{
			$series_alias_model = Model::Named('Series_Alias');
			if ( $series_alias_model->deleteAllForKeyValue(Series_Alias::series_id, $this->id) == false ) {
				return false;
			}
			// does not own publisher Publisher
			$publication_model = Model::Named('Publication');
			if ( $publication_model->deleteAllForKeyValue(Publication::series_id, $this->id) == false ) {
				return false;
			}
			$series_characters_model = Model::Named('Series_Characters');
			if ( $series_characters_model->deleteAllForKeyValue(Series_Characters::series_id, $this->id) == false ) {
				return false;
			}
			$story_arc_series_model = Model::Named('Story_Arc_Series');
			if ( $story_arc_series_model->deleteAllForKeyValue(Story_Arc_Series::series_id, $this->id) == false ) {
				return false;
			}
			$user_series_model = Model::Named('User_Series');
			if ( $user_series_model->deleteAllForKeyValue(User_Series::series_id, $this->id) == false ) {
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
	public function seriesLike( $name )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		$qualifiers[] = Qualifier::Like( 'search_name', $name, SQL::SQL_LIKE_BOTH);
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
				Series::name
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesMap() {
		return array(
			Series::publisher_id => Model::TO_ONE_TYPE,
			Series::parent_id => Model::INT_TYPE,
			Series::created => Model::DATE_TYPE,
			Series::name => Model::TEXT_TYPE,
			Series::search_name => Model::TEXT_TYPE,
			Series::desc => Model::TEXT_TYPE,
			Series::start_year => Model::INT_TYPE,
			Series::issue_count => Model::INT_TYPE,
			Series::pub_active => Model::FLAG_TYPE,
			Series::pub_wanted => Model::FLAG_TYPE,
			Series::pub_available => Model::INT_TYPE,
			Series::pub_cycle => Model::INT_TYPE,
			Series::pub_count => Model::INT_TYPE,
			Series::xurl => Model::TEXT_TYPE,
			Series::xsource => Model::TEXT_TYPE,
			Series::xid => Model::TEXT_TYPE,
			Series::xupdated => Model::DATE_TYPE
		);
	}

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) === false || is_null($object) == true) {
			switch ($attr) {
				case Series::start_year:
					return 1900;
				case Series::issue_count:
					return 0;
				case Series::pub_active:
					return Model::TERTIARY_TRUE;
				case Series::pub_wanted:
					return Model::TERTIARY_TRUE;
				case Series::pub_available:
					return 0;
				case Series::pub_cycle:
					return 0;
				case Series::pub_count:
					return 0;
			}
		}
		return parent::attributeDefaultValue($object, $type, $attr);
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
				Series::publisher_id,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_parent_id($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Series::parent_id,
				"FILTER_VALIDATE_INT"
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
				Series::created,
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
				Series::name,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_search_name($object = null, $value)
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
	function validate_start_year($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Series::start_year,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
	function validate_issue_count($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Series::issue_count,
				"FILTER_VALIDATE_INT"
			);
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
				Series::pub_active,
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
				Series::pub_wanted,
				"FILTER_VALIDATE_BOOLEAN"
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
				Series::pub_available,
				"FILTER_VALIDATE_INT"
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
				Series::pub_cycle,
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
				Series::pub_count,
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

		// url format
		if ( filter_var($value, FILTER_VALIDATE_URL) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Series::xurl,
				"FILTER_VALIDATE_URL"
			);
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
