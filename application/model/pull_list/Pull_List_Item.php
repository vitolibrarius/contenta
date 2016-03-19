<?php

namespace model\pull_list;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use model\pull_list\Pull_List_ItemDBO as Pull_List_ItemDBO;

/** Sample Creation script */
		/** PULL_LIST_ITEM
		$sql = "CREATE TABLE IF NOT EXISTS pull_list_item ( "
			. model\pull_list\Pull_List_Item::id . " INTEGER PRIMARY KEY, "
			. model\pull_list\Pull_List_Item::group . " TEXT, "
			. model\pull_list\Pull_List_Item::data . " TEXT, "
			. model\pull_list\Pull_List_Item::created . " INTEGER, "
			. model\pull_list\Pull_List_Item::name . " TEXT, "
			. model\pull_list\Pull_List_Item::issue . " TEXT, "
			. model\pull_list\Pull_List_Item::year . " INTEGER, "
			. model\pull_list\Pull_List_Item::pull_list_id . " INTEGER, "
			. "FOREIGN KEY (". model\pull_list\Pull_List_Item::pull_list_id .")"
				. " REFERENCES " . model\pull_list\Pull_List::TABLE . "(" . model\pull_list\Pull_List::id . "),"
			. ")";
		$this->sqlite_execute( "pull_list_item", $sql, "Create table pull_list_item" );

		$sql = 'CREATE  INDEX IF NOT EXISTS pull_list_item_name on pull_list_item (name)';
		$this->sqlite_execute( "pull_list_item", $sql, "Index on pull_list_item (name)' );
*/
class Pull_List_Item extends Model
{
	const TABLE = 'pull_list_item';
	const id = 'id';
	const group = 'group';
	const data = 'data';
	const created = 'created';
	const name = 'name';
	const issue = 'issue';
	const year = 'year';
	const pull_list_id = 'pull_list_id';

	public function tableName() { return Pull_List_Item::TABLE; }
	public function tablePK() { return Pull_List_Item::id; }
	public function sortOrder() { return array( 'asc' => array(Pull_List_Item::group, Pull_List_Item::name, Pull_List_Item::issue, )); }

	public function allColumnNames()
	{
		return array(
Pull_List_Item::id, Pull_List_Item::group, Pull_List_Item::data, Pull_List_Item::created, Pull_List_Item::name, Pull_List_Item::issue, Pull_List_Item::year, Pull_List_Item::pull_list_id, 		 );
	}

	/** * * * * * * * * *
		Basic search functions
	 */
	public function allForGroup($value)
	{
		return $this->allObjectsForKeyValue(Pull_List_Item::group, $value);
	}

	public function allForData($value)
	{
		return $this->allObjectsForKeyValue(Pull_List_Item::data, $value);
	}

	public function allForName($value)
	{
		return $this->allObjectsForKeyValue(Pull_List_Item::name, $value);
	}

	public function allForIssue($value)
	{
		return $this->allObjectsForKeyValue(Pull_List_Item::issue, $value);
	}


	public function allForPull_list($obj)
	{
		return $this->allObjectsForFK(Pull_List_Item::pull_list_id, $obj, $this->sortOrder(), 50);
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "pull_list":
					return array( Pull_List_Item::pull_list_id, "id"  );
					break;
				default:
					break;
			}
		}
		return parent::joinAttributes( $joinModel );
	}

	public function create( $pull_list, $group, $data, $name, $issue, $year)
	{
		$obj = false;
		if ( isset($pull_list, $data, $name) ) {
			$params = array(
				Pull_List_Item::group => $group,
				Pull_List_Item::data => $data,
				Pull_List_Item::created => time(),
				Pull_List_Item::name => $name,
				Pull_List_Item::issue => $issue,
				Pull_List_Item::year => $year,
			);

			if ( isset($pull_list)  && is_a($pull_list, DataObject)) {
				$params[Pull_List_Item::pull_list_id] = $pull_list->id;
			}

			list( $obj, $errorList ) = $this->createObject($params);
			if ( is_array($errorList) ) {
				return $errorList;
			}
		}
		return $obj;
	}

	public function deleteObject( \DataObject $object = null)
	{
		if ( $object instanceof Pull_List_Item )
		{
			return parent::deleteObject($object);
		}

		return false;
	}

}

?>
