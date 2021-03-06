<?php

namespace model\network;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\network\EndpointDBO as EndpointDBO;

/* import related objects */
use \model\network\Endpoint_Type as Endpoint_Type;
use \model\network\Endpoint_TypeDBO as Endpoint_TypeDBO;
use \model\pull_list\Pull_List as Pull_List;
use \model\pull_list\Pull_ListDBO as Pull_ListDBO;
use \model\network\Rss as Rss;
use \model\network\RssDBO as RssDBO;
use \model\network\Flux as Flux;
use \model\network\FluxDBO as FluxDBO;
use \model\jobs\Job as Job;
use \model\jobs\JobDBO as JobDBO;

/** Generated class, do not edit.
 */
abstract class _Endpoint extends Model
{
	const TABLE = 'endpoint';

	// attribute keys
	const id = 'id';
	const type_code = 'type_code';
	const name = 'name';
	const base_url = 'base_url';
	const api_key = 'api_key';
	const username = 'username';
	const daily_max = 'daily_max';
	const daily_dnld_max = 'daily_dnld_max';
	const error_count = 'error_count';
	const parameter = 'parameter';
	const enabled = 'enabled';
	const compressed = 'compressed';

	// relationship keys
	const endpointType = 'endpointType';
	const pull_lists = 'pull_lists';
	const rss = 'rss';
	const flux_sources = 'flux_sources';
	const flux_destinations = 'flux_destinations';
	const jobs = 'jobs';

	public function modelName()
	{
		return "Endpoint";
	}

	public function dboName()
	{
		return '\model\network\EndpointDBO';
	}

	public function tableName() { return Endpoint::TABLE; }
	public function tablePK() { return Endpoint::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Endpoint::name)
		);
	}

	public function allColumnNames()
	{
		return array(
			Endpoint::id,
			Endpoint::type_code,
			Endpoint::name,
			Endpoint::base_url,
			Endpoint::api_key,
			Endpoint::username,
			Endpoint::daily_max,
			Endpoint::daily_dnld_max,
			Endpoint::error_count,
			Endpoint::parameter,
			Endpoint::enabled,
			Endpoint::compressed
		);
	}

	public function allAttributes()
	{
		return array(
			Endpoint::name,
			Endpoint::base_url,
			Endpoint::api_key,
			Endpoint::username,
			Endpoint::daily_max,
			Endpoint::daily_dnld_max,
			Endpoint::error_count,
			Endpoint::parameter,
			Endpoint::enabled,
			Endpoint::compressed
		);
	}

	public function allForeignKeys()
	{
		return array(Endpoint::type_code);
	}

	public function allRelationshipNames()
	{
		return array(
			Endpoint::endpointType,
			Endpoint::pull_lists,
			Endpoint::rss,
			Endpoint::flux_sources,
			Endpoint::flux_destinations,
			Endpoint::jobs
		);
	}

	public function attributes()
	{
		return array(
			Endpoint::name => array('length' => 256,'type' => 'TEXT'),
			Endpoint::base_url => array('length' => 1024,'type' => 'TEXT'),
			Endpoint::api_key => array('length' => 256,'type' => 'TEXT'),
			Endpoint::username => array('length' => 256,'type' => 'TEXT'),
			Endpoint::daily_max => array('type' => 'INTEGER'),
			Endpoint::daily_dnld_max => array('type' => 'INTEGER'),
			Endpoint::error_count => array('type' => 'INTEGER'),
			Endpoint::parameter => array('length' => 4096,'type' => 'TEXT'),
			Endpoint::enabled => array('type' => 'BOOLEAN'),
			Endpoint::compressed => array('type' => 'BOOLEAN')
		);
	}

	public function relationships()
	{
		return array(
			Endpoint::endpointType => array(
				'destination' => 'Endpoint_Type',
				'ownsDestination' => false,
				'isMandatory' => true,
				'isToMany' => false,
				'joins' => array( 'type_code' => 'code')
			),
			Endpoint::pull_lists => array(
				'destination' => 'Pull_List',
				'ownsDestination' => true,
				'isMandatory' => false,
				'isToMany' => true,
				'joins' => array( 'id' => 'endpoint_id')
			),
			Endpoint::rss => array(
				'destination' => 'Rss',
				'ownsDestination' => true,
				'isMandatory' => false,
				'isToMany' => true,
				'joins' => array( 'id' => 'endpoint_id')
			),
			Endpoint::flux_sources => array(
				'destination' => 'Flux',
				'ownsDestination' => true,
				'isMandatory' => false,
				'isToMany' => true,
				'joins' => array( 'id' => 'src_endpoint')
			),
			Endpoint::flux_destinations => array(
				'destination' => 'Flux',
				'ownsDestination' => true,
				'isMandatory' => false,
				'isToMany' => true,
				'joins' => array( 'id' => 'dest_endpoint')
			),
			Endpoint::jobs => array(
				'destination' => 'Job',
				'ownsDestination' => true,
				'isMandatory' => false,
				'isToMany' => true,
				'joins' => array( 'id' => 'endpoint_id')
			)
		);
	}

	public function searchQualifiers( array $query )
	{
		$qualifiers = array();
		if ( is_array($query) ) {
			foreach( $query as $attr => $value ) {
				switch ($attr) {
			// Endpoint::id == INTEGER

			// Endpoint::type_code == TEXT
				case Endpoint::type_code:
					if (strlen($value) > 0) {
						$qualifiers[Endpoint::type_code] = Qualifier::Equals( Endpoint::type_code, $value );
					}
					break;

			// Endpoint::name == TEXT
				case Endpoint::name:
					if (strlen($value) > 0) {
						$qualifiers[Endpoint::name] = Qualifier::Equals( Endpoint::name, $value );
					}
					break;

			// Endpoint::base_url == TEXT
				case Endpoint::base_url:
					if (strlen($value) > 0) {
						$qualifiers[Endpoint::base_url] = Qualifier::Equals( Endpoint::base_url, $value );
					}
					break;

			// Endpoint::api_key == TEXT
				case Endpoint::api_key:
					if (strlen($value) > 0) {
						$qualifiers[Endpoint::api_key] = Qualifier::Equals( Endpoint::api_key, $value );
					}
					break;

			// Endpoint::username == TEXT
				case Endpoint::username:
					if (strlen($value) > 0) {
						$qualifiers[Endpoint::username] = Qualifier::Equals( Endpoint::username, $value );
					}
					break;

			// Endpoint::daily_max == INTEGER
				case Endpoint::daily_max:
					if ( intval($value) > 0 ) {
						$qualifiers[Endpoint::daily_max] = Qualifier::Equals( Endpoint::daily_max, intval($value) );
					}
					break;

			// Endpoint::daily_dnld_max == INTEGER
				case Endpoint::daily_dnld_max:
					if ( intval($value) > 0 ) {
						$qualifiers[Endpoint::daily_dnld_max] = Qualifier::Equals( Endpoint::daily_dnld_max, intval($value) );
					}
					break;

			// Endpoint::error_count == INTEGER
				case Endpoint::error_count:
					if ( intval($value) > 0 ) {
						$qualifiers[Endpoint::error_count] = Qualifier::Equals( Endpoint::error_count, intval($value) );
					}
					break;

			// Endpoint::parameter == TEXT
				case Endpoint::parameter:
					if (strlen($value) > 0) {
						$qualifiers[Endpoint::parameter] = Qualifier::Equals( Endpoint::parameter, $value );
					}
					break;

			// Endpoint::enabled == BOOLEAN
				case Endpoint::enabled:
					$v = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
					if (is_null($v) == false) {
						$qualifiers[Endpoint::enabled] = Qualifier::Equals( Endpoint::enabled, $v );
					}
					break;

			// Endpoint::compressed == BOOLEAN
				case Endpoint::compressed:
					$v = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
					if (is_null($v) == false) {
						$qualifiers[Endpoint::compressed] = Qualifier::Equals( Endpoint::compressed, $v );
					}
					break;

				default:
					/* no type specified for Endpoint::compressed */
					break;
				}
			}
		}
		return $qualifiers;
	}

	/**
	 *	Simple fetches
	 */

	public function allForType_code($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Endpoint::type_code, $value, null, $limit);
	}


	public function allForName($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Endpoint::name, $value, null, $limit);
	}


	public function allForBase_url($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Endpoint::base_url, $value, null, $limit);
	}


	public function allForApi_key($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Endpoint::api_key, $value, null, $limit);
	}


	public function allForUsername($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Endpoint::username, $value, null, $limit);
	}


	public function allForDaily_max($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Endpoint::daily_max, $value, null, $limit);
	}

	public function allForDaily_dnld_max($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Endpoint::daily_dnld_max, $value, null, $limit);
	}

	public function allForError_count($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Endpoint::error_count, $value, null, $limit);
	}

	public function allForParameter($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Endpoint::parameter, $value, null, $limit);
	}





	/**
	 * Simple relationship fetches
	 */
	public function allForEndpointType($obj, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForFK(Endpoint::type_code, $obj, $this->sortOrder(), $limit);
	}

	public function countForEndpointType($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Endpoint::type_code, $obj );
		}
		return false;
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "endpoint_type":
					return array( Endpoint::type_code, "code"  );
					break;
				case "pull_list":
					return array( Endpoint::id, "endpoint_id"  );
					break;
				case "rss":
					return array( Endpoint::id, "endpoint_id"  );
					break;
				case "flux":
					return array( Endpoint::id, "src_endpoint"  );
					break;
				case "flux":
					return array( Endpoint::id, "dest_endpoint"  );
					break;
				case "job":
					return array( Endpoint::id, "endpoint_id"  );
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
				$default_name = $this->attributeDefaultValue( null, null, Endpoint::name);
				if ( is_null( $default_name ) == false ) {
					$values['name'] = $default_name;
				}
			}
			if ( isset($values['base_url']) == false ) {
				$default_base_url = $this->attributeDefaultValue( null, null, Endpoint::base_url);
				if ( is_null( $default_base_url ) == false ) {
					$values['base_url'] = $default_base_url;
				}
			}
			if ( isset($values['api_key']) == false ) {
				$default_api_key = $this->attributeDefaultValue( null, null, Endpoint::api_key);
				if ( is_null( $default_api_key ) == false ) {
					$values['api_key'] = $default_api_key;
				}
			}
			if ( isset($values['username']) == false ) {
				$default_username = $this->attributeDefaultValue( null, null, Endpoint::username);
				if ( is_null( $default_username ) == false ) {
					$values['username'] = $default_username;
				}
			}
			if ( isset($values['daily_max']) == false ) {
				$default_daily_max = $this->attributeDefaultValue( null, null, Endpoint::daily_max);
				if ( is_null( $default_daily_max ) == false ) {
					$values['daily_max'] = $default_daily_max;
				}
			}
			if ( isset($values['daily_dnld_max']) == false ) {
				$default_daily_dnld_max = $this->attributeDefaultValue( null, null, Endpoint::daily_dnld_max);
				if ( is_null( $default_daily_dnld_max ) == false ) {
					$values['daily_dnld_max'] = $default_daily_dnld_max;
				}
			}
			if ( isset($values['error_count']) == false ) {
				$default_error_count = $this->attributeDefaultValue( null, null, Endpoint::error_count);
				if ( is_null( $default_error_count ) == false ) {
					$values['error_count'] = $default_error_count;
				}
			}
			if ( isset($values['parameter']) == false ) {
				$default_parameter = $this->attributeDefaultValue( null, null, Endpoint::parameter);
				if ( is_null( $default_parameter ) == false ) {
					$values['parameter'] = $default_parameter;
				}
			}
			if ( isset($values['enabled']) == false ) {
				$default_enabled = $this->attributeDefaultValue( null, null, Endpoint::enabled);
				if ( is_null( $default_enabled ) == false ) {
					$values['enabled'] = $default_enabled;
				}
			}
			if ( isset($values['compressed']) == false ) {
				$default_compressed = $this->attributeDefaultValue( null, null, Endpoint::compressed);
				if ( is_null( $default_compressed ) == false ) {
					$values['compressed'] = $default_compressed;
				}
			}

			// default conversion for relationships
			if ( isset($values['endpointType']) ) {
				$local_endpointType = $values['endpointType'];
				if ( $local_endpointType instanceof Endpoint_TypeDBO) {
					$values[Endpoint::type_code] = $local_endpointType->code;
				}
				else if ( is_string( $local_endpointType) ) {
					$params[Endpoint::type_code] = $local_endpointType;
				}
			}
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Endpoint ) {
			if ( isset($values['endpointType']) ) {
				$local_endpointType = $values['endpointType'];
				if ( $local_endpointType instanceof Endpoint_TypeDBO) {
					$values[Endpoint::type_code] = $local_endpointType->code;
				}
				else if ( is_string( $local_endpointType) ) {
					$params[Endpoint::type_code] = $values['endpointType'];
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
		if ( $object instanceof EndpointDBO )
		{
			// does not own endpointType Endpoint_Type
			$pull_list_model = Model::Named('Pull_List');
			if ( $pull_list_model->deleteAllForKeyValue(Pull_List::endpoint_id, $object->id) == false ) {
				return false;
			}
			$rss_model = Model::Named('Rss');
			if ( $rss_model->deleteAllForKeyValue(Rss::endpoint_id, $object->id) == false ) {
				return false;
			}
			$flux_model = Model::Named('Flux');
			if ( $flux_model->deleteAllForKeyValue(Flux::src_endpoint, $object->id) == false ) {
				return false;
			}
			$flux_model = Model::Named('Flux');
			if ( $flux_model->deleteAllForKeyValue(Flux::dest_endpoint, $object->id) == false ) {
				return false;
			}
			$job_model = Model::Named('Job');
			if ( $job_model->deleteAllForKeyValue(Job::endpoint_id, $object->id) == false ) {
				return false;
			}
			return parent::deleteObject($object);
		}

		return false;
	}

	public function deleteAllForEndpointType(Endpoint_TypeDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForEndpointType($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForEndpointType($obj);
			}
		}
		return $success;
	}

	/**
	 * Named fetches
	 */

	/**
	 * Attribute editing
	 */
	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Endpoint::name,
				Endpoint::base_url
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesMap() {
		return array(
			Endpoint::type_code => Model::TO_ONE_TYPE,
			Endpoint::name => Model::TEXT_TYPE,
			Endpoint::base_url => Model::TEXTAREA_TYPE,
			Endpoint::api_key => Model::TEXT_TYPE,
			Endpoint::username => Model::TEXT_TYPE,
			Endpoint::daily_max => Model::INT_TYPE,
			Endpoint::daily_dnld_max => Model::INT_TYPE,
			Endpoint::error_count => Model::INT_TYPE,
			Endpoint::parameter => Model::TEXTAREA_TYPE,
			Endpoint::enabled => Model::FLAG_TYPE,
			Endpoint::compressed => Model::FLAG_TYPE
		);
	}

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) === false || is_null($object) == true) {
			switch ($attr) {
				case Endpoint::error_count:
					return 0;
				case Endpoint::enabled:
					return Model::TERTIARY_TRUE;
				case Endpoint::compressed:
					return Model::TERTIARY_FALSE;
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
				case Endpoint::type_code:
					$endpoint_type_model = Model::Named('Endpoint_Type');
					$fkObject = $endpoint_type_model->objectForId( $value );
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
	function validate_type_code($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Endpoint::type_code,
				"FIELD_EMPTY"
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
				Endpoint::name,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_base_url($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Endpoint::base_url,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_api_key($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_username($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_daily_max($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Endpoint::daily_max,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
	function validate_daily_dnld_max($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Endpoint::daily_dnld_max,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
	function validate_error_count($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Endpoint::error_count,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
	function validate_parameter($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_enabled($object = null, $value)
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
				Endpoint::enabled,
				"FILTER_VALIDATE_BOOLEAN"
			);
		}
		return null;
	}
	function validate_compressed($object = null, $value)
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
				Endpoint::compressed,
				"FILTER_VALIDATE_BOOLEAN"
			);
		}
		return null;
	}
}

?>
