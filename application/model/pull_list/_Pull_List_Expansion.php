<?php

namespace model\pull_list;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
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

	/** * * * * * * * * *
		Basic search functions
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

	public function create( $endpoint, $pattern, $replace)
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

	public function deleteObject( DataObject $object = null)
	{
		if ( $object instanceof Pull_List_Expansion )
		{
			// does not own Endpoint
			return parent::deleteObject($object);
		}

		return false;
	}

}

?>
