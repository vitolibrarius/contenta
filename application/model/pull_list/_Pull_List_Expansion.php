<?php

namespace model\pull_list;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \model\pull_list\Pull_List_ExpansionDBO as Pull_List_ExpansionDBO;

/* import related objects */
use \model\Endpoint as Endpoint;
use \model\EndpointDBO as EndpointDBO;

/** Sample Creation script */
		/** PULL_LIST_EXPANSION */
/*
		$sql = "CREATE TABLE IF NOT EXISTS pull_list_expansion ( "
			. Pull_List_Expansion::id . " INTEGER PRIMARY KEY, "
			. Pull_List_Expansion::pattern . " TEXT, "
			. Pull_List_Expansion::replace . " TEXT, "
			. Pull_List_Expansion::created . " INTEGER, "
			. Pull_List_Expansion::endpoint_id . " INTEGER, "
			. "FOREIGN KEY (". Pull_List_Expansion::endpoint_id .") REFERENCES " . Endpoint::TABLE . "(" . Endpoint::id . ")"
		. ")";
		$this->sqlite_execute( "pull_list_expansion", $sql, "Create table pull_list_expansion" );

*/
abstract class _Pull_List_Expansion extends Model
{
	const TABLE = 'pull_list_expansion';
	const id = 'id';
	const pattern = 'pattern';
	const replace = 'replace';
	const created = 'created';
	const endpoint_id = 'endpoint_id';

	public function tableName() { return Pull_List_Expansion::TABLE; }
	public function tablePK() { return Pull_List_Expansion::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Pull_List_Expansion::pattern)
		);
	}

	public function allColumnNames()
	{
		return array(
			Pull_List_Expansion::id,
			Pull_List_Expansion::pattern,
			Pull_List_Expansion::replace,
			Pull_List_Expansion::created,
			Pull_List_Expansion::endpoint_id
		);
	}

	/**
	 *	Simple fetches
	 */
	public function allForPattern($value)
	{
		return $this->allObjectsForKeyValue(Pull_List_Expansion::pattern, $value);
	}

	public function allForReplace($value)
	{
		return $this->allObjectsForKeyValue(Pull_List_Expansion::replace, $value);
	}


	public function allForEndpoint($obj)
	{
		return $this->allObjectsForFK(Pull_List_Expansion::endpoint_id, $obj, $this->sortOrder(), 50);
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "endpoint":
					return array( Pull_List_Expansion::endpoint_id, "id"  );
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
	public function base_create( $endpoint, $pattern, $replace)
	{
		$obj = false;
		if ( isset($endpoint, $pattern) ) {
			$params = array(
				Pull_List_Expansion::pattern => (isset($pattern) ? $pattern : null),
				Pull_List_Expansion::replace => (isset($replace) ? $replace : null),
				Pull_List_Expansion::created => time(),
			);

			if ( isset($endpoint) ) {
				if ( $endpoint instanceof EndpointDBO) {
					$params[Pull_List_Expansion::endpoint_id] = $endpoint->id;
				}
				else if (  is_integer($endpoint) ) {
					$params[Pull_List_Expansion::endpoint_id] = $endpoint;
				}
			}

			list( $obj, $errorList ) = $this->createObject($params);
			if ( is_array($errorList) ) {
				return $errorList;
			}
		}
		return $obj;
	}

	public function base_update( Pull_List_ExpansionDBO $obj,
		$endpoint, $pattern, $replace)
	{
		if ( isset( $obj ) && is_null($obj) == false ) {
			$updates = array();

			if (isset($pattern) && (isset($obj->pattern) == false || $pattern != $obj->pattern)) {
				$updates[Pull_List_Expansion::pattern] = $pattern;
			}
			if (isset($replace) && (isset($obj->replace) == false || $replace != $obj->replace)) {
				$updates[Pull_List_Expansion::replace] = $replace;
			}

			if ( isset($endpoint) ) {
				if ( $endpoint instanceof EndpointDBO) {
					$updates[Pull_List_Expansion::endpoint_id] = $endpoint->id;
				}
				else if (  is_integer($endpoint) ) {
					$updates[Pull_List_Expansion::endpoint_id] = $endpoint;
				}
			}

			if ( count($updates) > 0 ) {
				list($obj, $errorList) = $this->updateObject( $obj, $updates );
				if ( is_array($errorList) ) {
					return $errorList;
				}
			}
		}
		return $obj;
	}

	/**
	 *	Delete functions
	 */
	public function deleteObject( DataObject $object = null)
	{
		if ( $object instanceof Pull_List_Expansion )
		{
			// does not own Endpoint
			return parent::deleteObject($object);
		}

		return false;
	}

	public function deleteAllForEndpoint(EndpointDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForEndpoint($obj);
			foreach ($array as $key => $value) {
				if ($this->deleteObject($value) == false) {
					$success = false;
					break;
				}
			}
		}
		return $success;
	}

	/**
	 *	Named fetches
	 */

	/** Set attributes */
	public function setPattern( Pull_List_ExpansionDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Pull_List_Expansion::pattern => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setReplace( Pull_List_ExpansionDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Pull_List_Expansion::replace => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setCreated( Pull_List_ExpansionDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Pull_List_Expansion::created => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setEndpoint_id( Pull_List_ExpansionDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Pull_List_Expansion::endpoint_id => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}


	/** Validation */
	function validate_pattern($object = null, $value)
	{
		$value = trim($value);
		if (empty($value)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List_Expansion::pattern,
				"FIELD_EMPTY"
			);
		}
		return null;
	}
	function validate_replace($object = null, $value)
	{
		$value = trim($value);
		return null;
	}
	function validate_created($object = null, $value)
	{
		if ( isset($object, $object->created) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List_Expansion::created,
				"IMMUTABLE"
			);
		}
		return null;
	}
	function validate_endpoint_id($object = null, $value)
	{
		if (isset($object->endpoint_id) === false && empty($value) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List_Expansion::endpoint_id,
				"FIELD_EMPTY"
			);
		}
		return null;
	}
}

?>
