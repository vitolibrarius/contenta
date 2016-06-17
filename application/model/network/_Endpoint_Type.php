<?php

namespace model\network;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\network\Endpoint_TypeDBO as Endpoint_TypeDBO;

/* import related objects */
use \model\network\Endpoint as Endpoint;
use \model\network\EndpointDBO as EndpointDBO;
use \model\pull_list\Pull_List_Exclusion as Pull_List_Exclusion;
use \model\pull_list\Pull_List_ExclusionDBO as Pull_List_ExclusionDBO;
use \model\pull_list\Pull_List_Expansion as Pull_List_Expansion;
use \model\pull_list\Pull_List_ExpansionDBO as Pull_List_ExpansionDBO;

/** Sample Creation script */
		/** ENDPOINT_TYPE */
/*
		$sql = "CREATE TABLE IF NOT EXISTS endpoint_type ( "
			. Endpoint_Type::id . " INTEGER PRIMARY KEY, "
			. Endpoint_Type::code . " TEXT, "
			. Endpoint_Type::name . " TEXT, "
			. Endpoint_Type::comments . " TEXT, "
			. Endpoint_Type::data_type . " TEXT, "
			. Endpoint_Type::site_url . " TEXT, "
			. Endpoint_Type::api_url . " TEXT, "
			. Endpoint_Type::favicon_url . " TEXT, "
			. Endpoint_Type::throttle_hits . " INTEGER, "
			. Endpoint_Type::throttle_time . " INTEGER "
		. ")";
		$this->sqlite_execute( "endpoint_type", $sql, "Create table endpoint_type" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS endpoint_type_code on endpoint_type (code)';
		$this->sqlite_execute( "endpoint_type", $sql, "Index on endpoint_type (code)" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS endpoint_type_name on endpoint_type (name)';
		$this->sqlite_execute( "endpoint_type", $sql, "Index on endpoint_type (name)" );
*/
abstract class _Endpoint_Type extends Model
{
	const TABLE = 'endpoint_type';
	const id = 'id';
	const code = 'code';
	const name = 'name';
	const comments = 'comments';
	const data_type = 'data_type';
	const site_url = 'site_url';
	const api_url = 'api_url';
	const favicon_url = 'favicon_url';
	const throttle_hits = 'throttle_hits';
	const throttle_time = 'throttle_time';

	public function tableName() { return Endpoint_Type::TABLE; }
	public function tablePK() { return Endpoint_Type::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Endpoint_Type::name)
		);
	}

	public function allColumnNames()
	{
		return array(
			Endpoint_Type::id,
			Endpoint_Type::code,
			Endpoint_Type::name,
			Endpoint_Type::comments,
			Endpoint_Type::data_type,
			Endpoint_Type::site_url,
			Endpoint_Type::api_url,
			Endpoint_Type::favicon_url,
			Endpoint_Type::throttle_hits,
			Endpoint_Type::throttle_time
		);
	}

	/**
	 *	Simple fetches
	 */

	public function objectForCode($value)
	{
		return $this->singleObjectForKeyValue(Endpoint_Type::code, $value);
	}


	public function objectForName($value)
	{
		return $this->singleObjectForKeyValue(Endpoint_Type::name, $value);
	}


	public function allForComments($value)
	{
		return $this->allObjectsForKeyValue(Endpoint_Type::comments, $value);
	}


	public function allForData_type($value)
	{
		return $this->allObjectsForKeyValue(Endpoint_Type::data_type, $value);
	}


	public function allForSite_url($value)
	{
		return $this->allObjectsForKeyValue(Endpoint_Type::site_url, $value);
	}


	public function allForApi_url($value)
	{
		return $this->allObjectsForKeyValue(Endpoint_Type::api_url, $value);
	}


	public function allForFavicon_url($value)
	{
		return $this->allObjectsForKeyValue(Endpoint_Type::favicon_url, $value);
	}


	public function allForThrottle_hits($value)
	{
		return $this->allObjectsForKeyValue(Endpoint_Type::throttle_hits, $value);
	}

	public function allForThrottle_time($value)
	{
		return $this->allObjectsForKeyValue(Endpoint_Type::throttle_time, $value);
	}



	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "endpoint":
					return array( Endpoint_Type::id, "type_id"  );
					break;
				case "pull_list_excl":
					return array( Endpoint_Type::id, "endpoint_type_id"  );
					break;
				case "pull_list_expansion":
					return array( Endpoint_Type::id, "endpoint_type_id"  );
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
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Endpoint_Type ) {
		}

		return parent::updateObject($object, $values);
	}

	/**
	 *	Delete functions
	 */
	public function deleteObject( DataObject $object = null)
	{
		if ( $object instanceof Endpoint_TypeDBO )
		{
			$endpoint_model = Model::Named('Endpoint');
			if ( $endpoint_model->deleteAllForKeyValue(Endpoint::type_id, $this->id) == false ) {
				return false;
			}
			$pull_list_excl_model = Model::Named('Pull_List_Exclusion');
			if ( $pull_list_excl_model->deleteAllForKeyValue(Pull_List_Exclusion::endpoint_type_id, $this->id) == false ) {
				return false;
			}
			$pull_list_expansion_model = Model::Named('Pull_List_Expansion');
			if ( $pull_list_expansion_model->deleteAllForKeyValue(Pull_List_Expansion::endpoint_type_id, $this->id) == false ) {
				return false;
			}
			return parent::deleteObject($object);
		}

		return false;
	}


	/**
	 *	Named fetches
	 */


	/** Validation */
	function validate_code($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Endpoint_Type::code,
				"FIELD_EMPTY"
			);
		}

		// make sure Code is unique
		$existing = $this->objectForCode($value);
		if ( $existing != false && ( is_null($object) || $existing->id != $object->id)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Endpoint_Type::code,
				"UNIQUE_FIELD_VALUE"
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
				Endpoint_Type::name,
				"FIELD_EMPTY"
			);
		}

		// make sure Name is unique
		$existing = $this->objectForName($value);
		if ( $existing != false && ( is_null($object) || $existing->id != $object->id)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Endpoint_Type::name,
				"UNIQUE_FIELD_VALUE"
			);
		}
		return null;
	}
	function validate_comments($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_data_type($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_site_url($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Endpoint_Type::site_url,
				"FIELD_EMPTY"
			);
		}

		// url format
		if ( filter_var($value, FILTER_VALIDATE_URL) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Endpoint_Type::site_url,
				"FILTER_VALIDATE_URL"
			);
		}
		return null;
	}
	function validate_api_url($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Endpoint_Type::api_url,
				"FIELD_EMPTY"
			);
		}

		// url format
		if ( filter_var($value, FILTER_VALIDATE_URL) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Endpoint_Type::api_url,
				"FILTER_VALIDATE_URL"
			);
		}
		return null;
	}
	function validate_favicon_url($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// url format
		if ( filter_var($value, FILTER_VALIDATE_URL) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Endpoint_Type::favicon_url,
				"FILTER_VALIDATE_URL"
			);
		}
		return null;
	}
	function validate_throttle_hits($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Endpoint_Type::throttle_hits,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
	function validate_throttle_time($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Endpoint_Type::throttle_time,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
}

?>
