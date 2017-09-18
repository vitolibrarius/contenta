<?php

namespace model\network;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\network\RssDBO as RssDBO;

/* import related objects */
use \model\network\Endpoint as Endpoint;
use \model\network\EndpointDBO as EndpointDBO;

/** Generated class, do not edit.
 */
abstract class _Rss extends Model
{
	const TABLE = 'rss';

	// attribute keys
	const id = 'id';
	const endpoint_id = 'endpoint_id';
	const created = 'created';
	const title = 'title';
	const desc = 'desc';
	const pub_date = 'pub_date';
	const guid = 'guid';
	const clean_name = 'clean_name';
	const clean_issue = 'clean_issue';
	const clean_year = 'clean_year';
	const enclosure_url = 'enclosure_url';
	const enclosure_length = 'enclosure_length';
	const enclosure_mime = 'enclosure_mime';
	const enclosure_hash = 'enclosure_hash';
	const enclosure_password = 'enclosure_password';

	// relationship keys
	const endpoint = 'endpoint';

	public function modelName()
	{
		return "Rss";
	}

	public function dboName()
	{
		return '\model\network\RssDBO';
	}

	public function tableName() { return Rss::TABLE; }
	public function tablePK() { return Rss::id; }

	public function sortOrder()
	{
		return array(
			array( 'desc' => Rss::created)
		);
	}

	public function allColumnNames()
	{
		return array(
			Rss::id,
			Rss::endpoint_id,
			Rss::created,
			Rss::title,
			Rss::desc,
			Rss::pub_date,
			Rss::guid,
			Rss::clean_name,
			Rss::clean_issue,
			Rss::clean_year,
			Rss::enclosure_url,
			Rss::enclosure_length,
			Rss::enclosure_mime,
			Rss::enclosure_hash,
			Rss::enclosure_password
		);
	}

	public function allAttributes()
	{
		return array(
			Rss::created,
			Rss::title,
			Rss::desc,
			Rss::pub_date,
			Rss::guid,
			Rss::clean_name,
			Rss::clean_issue,
			Rss::clean_year,
			Rss::enclosure_url,
			Rss::enclosure_length,
			Rss::enclosure_mime,
			Rss::enclosure_hash,
			Rss::enclosure_password
		);
	}

	public function allForeignKeys()
	{
		return array(Rss::endpoint_id);
	}

	public function allRelationshipNames()
	{
		return array(
			Rss::endpoint
		);
	}

	public function attributes()
	{
		return array(
			Rss::created => array('type' => 'DATE'),
			Rss::title => array('length' => 256,'type' => 'TEXT'),
			Rss::desc => array('length' => 4096,'type' => 'TEXT'),
			Rss::pub_date => array('type' => 'DATE'),
			Rss::guid => array('length' => 256,'type' => 'TEXT'),
			Rss::clean_name => array('length' => 256,'type' => 'TEXT'),
			Rss::clean_issue => array('length' => 256,'type' => 'TEXT'),
			Rss::clean_year => array('type' => 'INTEGER'),
			Rss::enclosure_url => array('length' => 256,'type' => 'TEXT'),
			Rss::enclosure_length => array('type' => 'INTEGER'),
			Rss::enclosure_mime => array('length' => 256,'type' => 'TEXT'),
			Rss::enclosure_hash => array('length' => 256,'type' => 'TEXT'),
			Rss::enclosure_password => array('type' => 'BOOLEAN')
		);
	}

	public function relationships()
	{
		return array(
			Rss::endpoint => array(
				'destination' => 'Endpoint',
				'ownsDestination' => false,
				'isMandatory' => true,
				'isToMany' => false,
				'joins' => array( 'endpoint_id' => 'id')
			)
		);
	}

	public function searchQualifiers( array $query )
	{
		$qualifiers = array();
		if ( is_array($query) ) {
			foreach( $query as $attr => $value ) {
				switch ($attr) {
			// Rss::id == INTEGER

			// Rss::endpoint_id == INTEGER
				case Rss::endpoint_id:
					if ( intval($value) > 0 ) {
						$qualifiers[Rss::endpoint_id] = Qualifier::Equals( Rss::endpoint_id, intval($value) );
					}
					break;

			// Rss::created == DATE

			// Rss::title == TEXT
				case Rss::title:
					if (strlen($value) > 0) {
						$qualifiers[Rss::title] = Qualifier::Equals( Rss::title, $value );
					}
					break;

			// Rss::desc == TEXT
				case Rss::desc:
					if (strlen($value) > 0) {
						$qualifiers[Rss::desc] = Qualifier::Equals( Rss::desc, $value );
					}
					break;

			// Rss::pub_date == DATE

			// Rss::guid == TEXT
				case Rss::guid:
					if (strlen($value) > 0) {
						$qualifiers[Rss::guid] = Qualifier::Equals( Rss::guid, $value );
					}
					break;

			// Rss::clean_name == TEXT
				case Rss::clean_name:
					if (strlen($value) > 0) {
						$qualifiers[Rss::clean_name] = Qualifier::Equals( Rss::clean_name, $value );
					}
					break;

			// Rss::clean_issue == TEXT
				case Rss::clean_issue:
					if (strlen($value) > 0) {
						$qualifiers[Rss::clean_issue] = Qualifier::Equals( Rss::clean_issue, $value );
					}
					break;

			// Rss::clean_year == INTEGER
				case Rss::clean_year:
					if ( intval($value) > 0 ) {
						$qualifiers[Rss::clean_year] = Qualifier::Equals( Rss::clean_year, intval($value) );
					}
					break;

			// Rss::enclosure_url == TEXT
				case Rss::enclosure_url:
					if (strlen($value) > 0) {
						$qualifiers[Rss::enclosure_url] = Qualifier::Equals( Rss::enclosure_url, $value );
					}
					break;

			// Rss::enclosure_length == INTEGER
				case Rss::enclosure_length:
					if ( intval($value) > 0 ) {
						$qualifiers[Rss::enclosure_length] = Qualifier::Equals( Rss::enclosure_length, intval($value) );
					}
					break;

			// Rss::enclosure_mime == TEXT
				case Rss::enclosure_mime:
					if (strlen($value) > 0) {
						$qualifiers[Rss::enclosure_mime] = Qualifier::Equals( Rss::enclosure_mime, $value );
					}
					break;

			// Rss::enclosure_hash == TEXT
				case Rss::enclosure_hash:
					if (strlen($value) > 0) {
						$qualifiers[Rss::enclosure_hash] = Qualifier::Equals( Rss::enclosure_hash, $value );
					}
					break;

			// Rss::enclosure_password == BOOLEAN
				case Rss::enclosure_password:
					$v = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
					if (is_null($v) == false) {
						$qualifiers[Rss::enclosure_password] = Qualifier::Equals( Rss::enclosure_password, $v );
					}
					break;

				default:
					/* no type specified for Rss::enclosure_password */
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
		return $this->allObjectsForKeyValue(Rss::title, $value, null, $limit);
	}


	public function allForDesc($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Rss::desc, $value, null, $limit);
	}



	public function objectForGuid($value)
	{
		return $this->singleObjectForKeyValue(Rss::guid, $value);
	}


	public function allForClean_name($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Rss::clean_name, $value, null, $limit);
	}


	public function allForClean_issue($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Rss::clean_issue, $value, null, $limit);
	}


	public function allForClean_year($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Rss::clean_year, $value, null, $limit);
	}

	public function allForEnclosure_url($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Rss::enclosure_url, $value, null, $limit);
	}


	public function allForEnclosure_length($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Rss::enclosure_length, $value, null, $limit);
	}

	public function allForEnclosure_mime($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Rss::enclosure_mime, $value, null, $limit);
	}


	public function allForEnclosure_hash($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Rss::enclosure_hash, $value, null, $limit);
	}




	/**
	 * Simple relationship fetches
	 */
	public function allForEndpoint($obj, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForFK(Rss::endpoint_id, $obj, $this->sortOrder(), $limit);
	}

	public function countForEndpoint($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Rss::endpoint_id, $obj );
		}
		return false;
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "endpoint":
					return array( Rss::endpoint_id, "id"  );
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
				$default_created = $this->attributeDefaultValue( null, null, Rss::created);
				if ( is_null( $default_created ) == false ) {
					$values['created'] = $default_created;
				}
			}
			if ( isset($values['title']) == false ) {
				$default_title = $this->attributeDefaultValue( null, null, Rss::title);
				if ( is_null( $default_title ) == false ) {
					$values['title'] = $default_title;
				}
			}
			if ( isset($values['desc']) == false ) {
				$default_desc = $this->attributeDefaultValue( null, null, Rss::desc);
				if ( is_null( $default_desc ) == false ) {
					$values['desc'] = $default_desc;
				}
			}
			if ( isset($values['pub_date']) == false ) {
				$default_pub_date = $this->attributeDefaultValue( null, null, Rss::pub_date);
				if ( is_null( $default_pub_date ) == false ) {
					$values['pub_date'] = $default_pub_date;
				}
			}
			if ( isset($values['guid']) == false ) {
				$default_guid = $this->attributeDefaultValue( null, null, Rss::guid);
				if ( is_null( $default_guid ) == false ) {
					$values['guid'] = $default_guid;
				}
			}
			if ( isset($values['clean_name']) == false ) {
				$default_clean_name = $this->attributeDefaultValue( null, null, Rss::clean_name);
				if ( is_null( $default_clean_name ) == false ) {
					$values['clean_name'] = $default_clean_name;
				}
			}
			if ( isset($values['clean_issue']) == false ) {
				$default_clean_issue = $this->attributeDefaultValue( null, null, Rss::clean_issue);
				if ( is_null( $default_clean_issue ) == false ) {
					$values['clean_issue'] = $default_clean_issue;
				}
			}
			if ( isset($values['clean_year']) == false ) {
				$default_clean_year = $this->attributeDefaultValue( null, null, Rss::clean_year);
				if ( is_null( $default_clean_year ) == false ) {
					$values['clean_year'] = $default_clean_year;
				}
			}
			if ( isset($values['enclosure_url']) == false ) {
				$default_enclosure_url = $this->attributeDefaultValue( null, null, Rss::enclosure_url);
				if ( is_null( $default_enclosure_url ) == false ) {
					$values['enclosure_url'] = $default_enclosure_url;
				}
			}
			if ( isset($values['enclosure_length']) == false ) {
				$default_enclosure_length = $this->attributeDefaultValue( null, null, Rss::enclosure_length);
				if ( is_null( $default_enclosure_length ) == false ) {
					$values['enclosure_length'] = $default_enclosure_length;
				}
			}
			if ( isset($values['enclosure_mime']) == false ) {
				$default_enclosure_mime = $this->attributeDefaultValue( null, null, Rss::enclosure_mime);
				if ( is_null( $default_enclosure_mime ) == false ) {
					$values['enclosure_mime'] = $default_enclosure_mime;
				}
			}
			if ( isset($values['enclosure_hash']) == false ) {
				$default_enclosure_hash = $this->attributeDefaultValue( null, null, Rss::enclosure_hash);
				if ( is_null( $default_enclosure_hash ) == false ) {
					$values['enclosure_hash'] = $default_enclosure_hash;
				}
			}
			if ( isset($values['enclosure_password']) == false ) {
				$default_enclosure_password = $this->attributeDefaultValue( null, null, Rss::enclosure_password);
				if ( is_null( $default_enclosure_password ) == false ) {
					$values['enclosure_password'] = $default_enclosure_password;
				}
			}

			// default conversion for relationships
			if ( isset($values['endpoint']) ) {
				$local_endpoint = $values['endpoint'];
				if ( $local_endpoint instanceof EndpointDBO) {
					$values[Rss::endpoint_id] = $local_endpoint->id;
				}
				else if ( is_integer( $local_endpoint) ) {
					$params[Rss::endpoint_id] = $local_endpoint;
				}
			}
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Rss ) {
			if ( isset($values['endpoint']) ) {
				$local_endpoint = $values['endpoint'];
				if ( $local_endpoint instanceof EndpointDBO) {
					$values[Rss::endpoint_id] = $local_endpoint->id;
				}
				else if ( is_integer( $local_endpoint) ) {
					$params[Rss::endpoint_id] = $values['endpoint'];
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
		if ( $object instanceof RssDBO )
		{
			// does not own endpoint Endpoint
			return parent::deleteObject($object);
		}

		return false;
	}

	public function deleteAllForEndpoint(EndpointDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForEndpoint($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForEndpoint($obj);
			}
		}
		return $success;
	}

	/**
	 * Named fetches
	 */
	public function objectsForNameIssueYear( $name, $issue, $year )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		if ( isset($name) && is_null($name) == false) {
			$qualifiers[] = Qualifier::Like( 'clean_name', $name, SQL::SQL_LIKE_BOTH);
		}
		if ( isset($issue) && is_null($issue) == false) {
			$qualifiers[] = Qualifier::Equals( 'clean_issue', $issue);
		}
		if ( isset($year) && is_null($year) == false) {
			$qualifiers[] = Qualifier::Equals( 'clean_year', $year);
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
				Rss::title,
				Rss::pub_date,
				Rss::guid,
				Rss::clean_name,
				Rss::enclosure_url
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesMap() {
		return array(
			Rss::endpoint_id => Model::TO_ONE_TYPE,
			Rss::created => Model::DATE_TYPE,
			Rss::title => Model::TEXT_TYPE,
			Rss::desc => Model::TEXTAREA_TYPE,
			Rss::pub_date => Model::DATE_TYPE,
			Rss::guid => Model::TEXT_TYPE,
			Rss::clean_name => Model::TEXT_TYPE,
			Rss::clean_issue => Model::TEXT_TYPE,
			Rss::clean_year => Model::INT_TYPE,
			Rss::enclosure_url => Model::TEXT_TYPE,
			Rss::enclosure_length => Model::INT_TYPE,
			Rss::enclosure_mime => Model::TEXT_TYPE,
			Rss::enclosure_hash => Model::TEXT_TYPE,
			Rss::enclosure_password => Model::FLAG_TYPE
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
				case Rss::endpoint_id:
					$endpoint_model = Model::Named('Endpoint');
					$fkObject = $endpoint_model->objectForId( $value );
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
	function validate_endpoint_id($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Rss::endpoint_id,
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
				Rss::created,
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
				Rss::title,
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
	function validate_pub_date($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Rss::pub_date,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_guid($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Rss::guid,
				"FIELD_EMPTY"
			);
		}

		// make sure Guid is unique
		$existing = $this->objectForGuid($value);
		if ( $existing != false && ( is_null($object) || $existing->id != $object->id)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Rss::guid,
				"UNIQUE_FIELD_VALUE"
			);
		}
		return null;
	}
	function validate_clean_name($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Rss::clean_name,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_clean_issue($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_clean_year($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Rss::clean_year,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
	function validate_enclosure_url($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Rss::enclosure_url,
				"FIELD_EMPTY"
			);
		}

		// url format
		if ( filter_var($value, FILTER_VALIDATE_URL) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Rss::enclosure_url,
				"FILTER_VALIDATE_URL"
			);
		}
		return null;
	}
	function validate_enclosure_length($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Rss::enclosure_length,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
	function validate_enclosure_mime($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_enclosure_hash($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_enclosure_password($object = null, $value)
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
				Rss::enclosure_password,
				"FILTER_VALIDATE_BOOLEAN"
			);
		}
		return null;
	}
}

?>
