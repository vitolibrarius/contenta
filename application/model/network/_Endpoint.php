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

/** Sample Creation script */
		/** ENDPOINT */
/*
		$sql = "CREATE TABLE IF NOT EXISTS endpoint ( "
			. Endpoint::id . " INTEGER PRIMARY KEY, "
			. Endpoint::type_id . " INTEGER, "
			. Endpoint::name . " TEXT, "
			. Endpoint::base_url . " TEXT, "
			. Endpoint::api_key . " TEXT, "
			. Endpoint::username . " TEXT, "
			. Endpoint::enabled . " INTEGER, "
			. Endpoint::compressed . " INTEGER, "
			. "FOREIGN KEY (". Endpoint::type_id .") REFERENCES " . Endpoint_Type::TABLE . "(" . Endpoint_Type::id . ")"
		. ")";
		$this->sqlite_execute( "endpoint", $sql, "Create table endpoint" );

*/
abstract class _Endpoint extends Model
{
	const TABLE = 'endpoint';
	const id = 'id';
	const type_id = 'type_id';
	const name = 'name';
	const base_url = 'base_url';
	const api_key = 'api_key';
	const username = 'username';
	const enabled = 'enabled';
	const compressed = 'compressed';

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
			Endpoint::type_id,
			Endpoint::name,
			Endpoint::base_url,
			Endpoint::api_key,
			Endpoint::username,
			Endpoint::enabled,
			Endpoint::compressed
		);
	}

	/**
	 *	Simple fetches
	 */


	public function allForName($value)
	{
		return $this->allObjectsForKeyValue(Endpoint::name, $value);
	}


	public function allForBase_url($value)
	{
		return $this->allObjectsForKeyValue(Endpoint::base_url, $value);
	}


	public function allForApi_key($value)
	{
		return $this->allObjectsForKeyValue(Endpoint::api_key, $value);
	}


	public function allForUsername($value)
	{
		return $this->allObjectsForKeyValue(Endpoint::username, $value);
	}





	public function allForEndpointType($obj)
	{
		return $this->allObjectsForFK(Endpoint::type_id, $obj, $this->sortOrder(), 50);
	}

	public function countForEndpointType($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Endpoint::type_id, $obj );
		}
		return false;
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "endpoint_type":
					return array( Endpoint::type_id, "id"  );
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
					$values[Endpoint::type_id] = $local_endpointType->id;
				}
				else if ( is_integer( $local_endpointType) ) {
					$params[Endpoint::type_id] = $local_endpointType;
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
					$values[Endpoint::type_id] = $local_endpointType->id;
				}
				else if ( is_integer( $local_endpointType) ) {
					$params[Endpoint::type_id] = $values['endpointType'];
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
			if ( $pull_list_model->deleteAllForKeyValue(Pull_List::endpoint_id, $this->id) == false ) {
				return false;
			}
			$rss_model = Model::Named('Rss');
			if ( $rss_model->deleteAllForKeyValue(Rss::endpoint_id, $this->id) == false ) {
				return false;
			}
			$flux_model = Model::Named('Flux');
			if ( $flux_model->deleteAllForKeyValue(Flux::src_endpoint, $this->id) == false ) {
				return false;
			}
			$flux_model = Model::Named('Flux');
			if ( $flux_model->deleteAllForKeyValue(Flux::dest_endpoint, $this->id) == false ) {
				return false;
			}
			$job_model = Model::Named('Job');
			if ( $job_model->deleteAllForKeyValue(Job::endpoint_id, $this->id) == false ) {
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
			Endpoint::type_id => Model::TO_ONE_TYPE,
			Endpoint::name => Model::TEXT_TYPE,
			Endpoint::base_url => Model::TEXTAREA_TYPE,
			Endpoint::api_key => Model::TEXT_TYPE,
			Endpoint::username => Model::TEXT_TYPE,
			Endpoint::enabled => Model::FLAG_TYPE,
			Endpoint::compressed => Model::FLAG_TYPE
		);
	}

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) === false || is_null($object) == true) {
			switch ($attr) {
				case Endpoint::enabled:
					return Model::TERTIARY_TRUE;
				case Endpoint::compressed:
					return Model::TERTIARY_FALSE;
			}
		}
		return parent::attributeDefaultValue($object, $type, $attr);
	}

	/**
	 * Validation
	 */
	function validate_type_id($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Endpoint::type_id,
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
