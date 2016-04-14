<?php

namespace model\pull_list;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
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
	public function base_create( $endpoint, $pattern, $type)
	{
		$obj = false;
		if ( isset($endpoint, $pattern) ) {
			$params = array(
				Pull_List_Exclusion::pattern => (isset($pattern) ? $pattern : null),
				Pull_List_Exclusion::type => (isset($type) ? $type : 'item'),
				Pull_List_Exclusion::created => time(),
			);

			if ( isset($endpoint) ) {
				if ( $endpoint instanceof EndpointDBO) {
					$params[Pull_List_Exclusion::endpoint_id] = $endpoint->id;
				}
				else if (  is_integer($endpoint) ) {
					$params[Pull_List_Exclusion::endpoint_id] = $endpoint;
				}
			}

			list( $obj, $errorList ) = $this->createObject($params);
			if ( is_array($errorList) ) {
				return $errorList;
			}
		}
		return $obj;
	}

	public function base_update( Pull_List_ExclusionDBO $obj,
		$endpoint, $pattern, $type)
	{
		if ( isset( $obj ) && is_null($obj) == false ) {
			$updates = array();

			if (isset($pattern) && (isset($obj->pattern) == false || $pattern != $obj->pattern)) {
				$updates[Pull_List_Exclusion::pattern] = $pattern;
			}
			if (isset($type) && (isset($obj->type) == false || $type != $obj->type)) {
				$updates[Pull_List_Exclusion::type] = $type;
			}

			if ( isset($endpoint) ) {
				if ( $endpoint instanceof EndpointDBO) {
					$updates[Pull_List_Exclusion::endpoint_id] = $endpoint->id;
				}
				else if (  is_integer($endpoint) ) {
					$updates[Pull_List_Exclusion::endpoint_id] = $endpoint;
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
}

?>
