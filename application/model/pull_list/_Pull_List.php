<?php

namespace model\pull_list;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \model\pull_list\Pull_ListDBO as Pull_ListDBO;

/* import related objects */
use \model\Endpoint as Endpoint;
use \model\EndpointDBO as EndpointDBO;
use \model\pull_list\Pull_List_Item as Pull_List_Item;
use \model\pull_list\Pull_List_ItemDBO as Pull_List_ItemDBO;
use \model\pull_list\Pull_List_Exclusion as Pull_List_Exclusion;
use \model\pull_list\Pull_List_ExclusionDBO as Pull_List_ExclusionDBO;
use \model\pull_list\Pull_List_Expansion as Pull_List_Expansion;
use \model\pull_list\Pull_List_ExpansionDBO as Pull_List_ExpansionDBO;

/** Sample Creation script */
		/** PULL_LIST */
/*
		$sql = "CREATE TABLE IF NOT EXISTS pull_list ( "
			. Pull_List::id . " INTEGER PRIMARY KEY, "
			. Pull_List::name . " TEXT, "
			. Pull_List::etag . " TEXT, "
			. Pull_List::created . " INTEGER, "
			. Pull_List::published . " INTEGER, "
			. Pull_List::endpoint_id . " INTEGER, "
			. "FOREIGN KEY (". Pull_List::endpoint_id .") REFERENCES " . Endpoint::TABLE . "(" . Endpoint::id . ")"
		. ")";
		$this->sqlite_execute( "pull_list", $sql, "Create table pull_list" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS pull_list_etag on pull_list (etag)';
		$this->sqlite_execute( "pull_list", $sql, "Index on pull_list (etag)" );
*/
abstract class _Pull_List extends Model
{
	const TABLE = 'pull_list';
	const id = 'id';
	const name = 'name';
	const etag = 'etag';
	const created = 'created';
	const published = 'published';
	const endpoint_id = 'endpoint_id';

	public function tableName() { return Pull_List::TABLE; }
	public function tablePK() { return Pull_List::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Pull_List::name)
		);
	}

	public function allColumnNames()
	{
		return array(
			Pull_List::id,
			Pull_List::name,
			Pull_List::etag,
			Pull_List::created,
			Pull_List::published,
			Pull_List::endpoint_id
		);
	}

	/**
	 *	Simple fetches
	 */
	public function allForName($value)
	{
		return $this->allObjectsForKeyValue(Pull_List::name, $value);
	}

	public function allLikeName($value)
	{
		return SQL::Select( $this )
			->where( Qualifier::Like( Pull_List::name, $value, SQL::SQL_LIKE_AFTER ))
			->orderBy( $this->sortOrder() )
			->limit( 50 )
			->fetchAll();
	}
	public function objectForEtag($value)
	{
		return $this->singleObjectForKeyValue(Pull_List::etag, $value);
	}


	public function allForEndpoint($obj)
	{
		return $this->allObjectsForFK(Pull_List::endpoint_id, $obj, $this->sortOrder(), 50);
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "endpoint":
					return array( Pull_List::endpoint_id, "id"  );
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
	public function base_create( $endpoint, $name, $etag, $published)
	{
		$obj = false;
		if ( isset($endpoint, $name) ) {
			$params = array(
				Pull_List::name => (isset($name) ? $name : null),
				Pull_List::etag => (isset($etag) ? $etag : null),
				Pull_List::created => time(),
				Pull_List::published => (isset($published) ? $published : time()),
			);

			if ( isset($endpoint) ) {
				if ( $endpoint instanceof EndpointDBO) {
					$params[Pull_List::endpoint_id] = $endpoint->id;
				}
				else if (  is_integer($endpoint) ) {
					$params[Pull_List::endpoint_id] = $endpoint;
				}
			}

			list( $obj, $errorList ) = $this->createObject($params);
			if ( is_array($errorList) ) {
				return $errorList;
			}
		}
		return $obj;
	}

	public function base_update( Pull_ListDBO $obj,
		$endpoint, $name, $etag, $published)
	{
		if ( isset( $obj ) && is_null($obj) == false ) {
			$updates = array();

			if (isset($name) && (isset($obj->name) == false || $name != $obj->name)) {
				$updates[Pull_List::name] = $name;
			}
			if (isset($etag) && (isset($obj->etag) == false || $etag != $obj->etag)) {
				$updates[Pull_List::etag] = $etag;
			}
			if (isset($published) && (isset($obj->published) == false || $published != $obj->published)) {
				$updates[Pull_List::published] = $published;
			}

			if ( isset($endpoint) ) {
				if ( $endpoint instanceof EndpointDBO) {
					$updates[Pull_List::endpoint_id] = $endpoint->id;
				}
				else if (  is_integer($endpoint) ) {
					$updates[Pull_List::endpoint_id] = $endpoint;
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
		if ( $object instanceof Pull_List )
		{
			// does not own Endpoint
			$pull_list_item_model = Model::Named('Pull_List_Item');
			if ( $pull_list_item_model->deleteAllForKeyValue(Pull_List_Item::pull_list_id, $this->id) == false ) {
				return false;
			}
			// does not own Pull_List_Exclusion
			// does not own Pull_List_Expansion
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
