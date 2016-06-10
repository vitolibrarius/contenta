<?php

namespace model\pull_list;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

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
	public function createObject( array $values = array() )
	{
		if ( isset($values) ) {
			if ( isset($values['endpoint']) ) {
				$local_endpoint = $values['endpoint'];
				if ( $local_endpoint instanceof EndpointDBO) {
					$values[Pull_List_Expansion::endpoint_id] = $local_endpoint->id;
				}
				else if ( is_integer( $local_endpoint) ) {
					$params[Pull_List_Expansion::endpoint_id] = $local_endpoint;
				}
			}
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Pull_List_Expansion ) {
			if ( isset($values['endpoint']) ) {
				$local_endpoint = $values['endpoint'];
				if ( $local_endpoint instanceof EndpointDBO) {
					$values[Pull_List_Expansion::endpoint_id] = $local_endpoint->id;
				}
				else if ( is_integer( $local_endpoint) ) {
					$params[Pull_List_Expansion::endpoint_id] = $values['endpoint'];
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
		if ( $object instanceof Pull_List_ExpansionDBO )
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
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
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
				Pull_List_Expansion::created,
				"IMMUTABLE"
			);
		}
		return null;
	}
	function validate_endpoint_id($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List_Expansion::endpoint_id,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
}

?>
