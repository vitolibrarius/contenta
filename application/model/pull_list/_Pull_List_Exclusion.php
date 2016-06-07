<?php

namespace model\pull_list;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \model\pull_list\Pull_List_ExclusionDBO as Pull_List_ExclusionDBO;

/* import related objects */
use \model\Endpoint as Endpoint;
use \model\EndpointDBO as EndpointDBO;

/** Sample Creation script */
		/** PULL_LIST_EXCL */
/*
		$sql = "CREATE TABLE IF NOT EXISTS pull_list_excl ( "
			. Pull_List_Exclusion::id . " INTEGER PRIMARY KEY, "
			. Pull_List_Exclusion::pattern . " TEXT, "
			. Pull_List_Exclusion::type . " TEXT, "
			. Pull_List_Exclusion::created . " INTEGER, "
			. Pull_List_Exclusion::endpoint_id . " INTEGER, "
			. "FOREIGN KEY (". Pull_List_Exclusion::endpoint_id .") REFERENCES " . Endpoint::TABLE . "(" . Endpoint::id . ")"
		. ")";
		$this->sqlite_execute( "pull_list_excl", $sql, "Create table pull_list_excl" );

*/
abstract class _Pull_List_Exclusion extends Model
{
	const TABLE = 'pull_list_excl';
	const id = 'id';
	const pattern = 'pattern';
	const type = 'type';
	const created = 'created';
	const endpoint_id = 'endpoint_id';

	public function tableName() { return Pull_List_Exclusion::TABLE; }
	public function tablePK() { return Pull_List_Exclusion::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Pull_List_Exclusion::pattern)
		);
	}

	public function allColumnNames()
	{
		return array(
			Pull_List_Exclusion::id,
			Pull_List_Exclusion::pattern,
			Pull_List_Exclusion::type,
			Pull_List_Exclusion::created,
			Pull_List_Exclusion::endpoint_id
		);
	}

	/**
	 *	Simple fetches
	 */
	public function allForPattern($value)
	{
		return $this->allObjectsForKeyValue(Pull_List_Exclusion::pattern, $value);
	}

	public function allForType($value)
	{
		return $this->allObjectsForKeyValue(Pull_List_Exclusion::type, $value);
	}


	public function allForEndpoint($obj)
	{
		return $this->allObjectsForFK(Pull_List_Exclusion::endpoint_id, $obj, $this->sortOrder(), 50);
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "endpoint":
					return array( Pull_List_Exclusion::endpoint_id, "id"  );
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
			if ( isset($values['endpoint']) ) {
				$local_endpoint = $values['endpoint'];
				if ( $local_endpoint instanceof EndpointDBO) {
					$values[Pull_List_Exclusion::endpoint_id] = $local_endpoint->id;
				}
				else if ( is_integer( $local_endpoint) ) {
					$params[Pull_List_Exclusion::endpoint_id] = $local_endpoint;
				}
			}
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Pull_List_Exclusion ) {
			if ( isset($values['endpoint']) ) {
				$local_endpoint = $values['endpoint'];
				if ( $local_endpoint instanceof EndpointDBO) {
					$values[Pull_List_Exclusion::endpoint_id] = $local_endpoint->id;
				}
				else if ( is_integer( $local_endpoint) ) {
					$params[Pull_List_Exclusion::endpoint_id] = $values['endpoint'];
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
		if ( $object instanceof Pull_List_Exclusion )
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
	public function setPattern( Pull_List_ExclusionDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Pull_List_Exclusion::pattern => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setType( Pull_List_ExclusionDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Pull_List_Exclusion::type => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setCreated( Pull_List_ExclusionDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Pull_List_Exclusion::created => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setEndpoint_id( Pull_List_ExclusionDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Pull_List_Exclusion::endpoint_id => $value)) ) {
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
				Pull_List_Exclusion::pattern,
				"FIELD_EMPTY"
			);
		}
		return null;
	}
	function validate_type($object = null, $value)
	{
		$value = trim($value);
		return null;
	}
	function validate_created($object = null, $value)
	{
		if ( isset($object, $object->created) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List_Exclusion::created,
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
				Pull_List_Exclusion::endpoint_id,
				"FIELD_EMPTY"
			);
		}
		return null;
	}
}

?>
