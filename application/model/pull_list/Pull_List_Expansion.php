<?php

namespace model\pull_list;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use model\pull_list\Pull_List_ExpansionDBO as Pull_List_ExpansionDBO;

/** Sample Creation script */
		/** PULL_LIST_EXPANSION
		$sql = "CREATE TABLE IF NOT EXISTS pull_list_expansion ( "
			. model\pull_list\Pull_List_Expansion::id . " INTEGER PRIMARY KEY, "
			. model\pull_list\Pull_List_Expansion::pattern . " TEXT, "
			. model\pull_list\Pull_List_Expansion::replace . " TEXT, "
			. model\pull_list\Pull_List_Expansion::created . " INTEGER, "
			. model\pull_list\Pull_List_Expansion::endpoint_id . " INTEGER, "
			. "FOREIGN KEY (". model\pull_list\Pull_List_Expansion::endpoint_id .")"
				. " REFERENCES " . Endpoint::TABLE . "(" . Endpoint::id . ")"
			. ")";
		$this->sqlite_execute( "pull_list_expansion", $sql, "Create table pull_list_expansion" );

*/
class Pull_List_Expansion extends Model
{
	const TABLE = 'pull_list_expansion';
	const id = 'id';
	const pattern = 'pattern';
	const replace = 'replace';
	const created = 'created';
	const endpoint_id = 'endpoint_id';

	public function tableName() { return Pull_List_Expansion::TABLE; }
	public function tablePK() { return Pull_List_Expansion::id; }
	public function sortOrder() { return array( 'asc' => array(Pull_List_Expansion::pattern, )); }

	public function allColumnNames()
	{
		return array(
Pull_List_Expansion::id, Pull_List_Expansion::pattern, Pull_List_Expansion::replace, Pull_List_Expansion::created, Pull_List_Expansion::endpoint_id, 		 );
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
				Pull_List_Expansion::pattern => (isset($pattern) ? $pattern : ''),
				Pull_List_Expansion::replace => (isset($replace) ? $replace : ''),
				Pull_List_Expansion::created => time(),
			);

			if ( isset($endpoint)  && is_subclass_of($endpoint, 'DataObject')) {
				$params[Pull_List_Expansion::endpoint_id] = $endpoint->id;
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
			return parent::deleteObject($object);
		}

		return false;
	}

}

?>
