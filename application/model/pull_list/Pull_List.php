<?php

namespace model\pull_list;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use model\pull_list\Pull_ListDBO as Pull_ListDBO;

/** Sample Creation script */
		/** PULL_LIST
		$sql = "CREATE TABLE IF NOT EXISTS pull_list ( "
			. model\pull_list\Pull_List::id . " INTEGER PRIMARY KEY, "
			. model\pull_list\Pull_List::name . " TEXT, "
			. model\pull_list\Pull_List::etag . " TEXT, "
			. model\pull_list\Pull_List::created . " INTEGER, "
			. model\pull_list\Pull_List::published . " INTEGER, "
			. model\pull_list\Pull_List::endpoint_id . " INTEGER, "
			. "FOREIGN KEY (". model\pull_list\Pull_List::endpoint_id .")"
				. " REFERENCES " . Endpoint::TABLE . "(" . Endpoint::id . ")"
			. ")";
		$this->sqlite_execute( "pull_list", $sql, "Create table pull_list" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS pull_list_etag on pull_list (etag)';
		$this->sqlite_execute( "pull_list", $sql, "Index on pull_list (etag)" );
*/
class Pull_List extends Model
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

	/** * * * * * * * * *
		Basic search functions
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

	public function create( $endpoint, $name, $etag, $published)
	{
		$obj = false;
		if ( isset($endpoint, $name) ) {
			$params = array(
				Pull_List::name => (isset($name) ? $name : ''),
				Pull_List::etag => (isset($etag) ? $etag : ''),
				Pull_List::created => time(),
				Pull_List::published => (isset($published) ? $published : time()),
			);

			if ( isset($endpoint) ) {
				if ( $endpoint instanceof Endpoint) {
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

	public function deleteObject( DataObject $object = null)
	{
		if ( $object instanceof Pull_List )
		{
			return parent::deleteObject($object);
		}

		return false;
	}

}

?>
