<?php

namespace model\pull_list;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use model\pull_list\Pull_List_ItemDBO as Pull_List_ItemDBO;

/** Sample Creation script */
		/** PULL_LIST_ITEM
		$sql = "CREATE TABLE IF NOT EXISTS pull_list_item ( "
			. model\pull_list\Pull_List_Item::id . " INTEGER PRIMARY KEY, "
			. model\pull_list\Pull_List_Item::group_name . " TEXT, "
			. model\pull_list\Pull_List_Item::data . " TEXT, "
			. model\pull_list\Pull_List_Item::created . " INTEGER, "
			. model\pull_list\Pull_List_Item::name . " TEXT, "
			. model\pull_list\Pull_List_Item::issue . " TEXT, "
			. model\pull_list\Pull_List_Item::year . " INTEGER, "
			. model\pull_list\Pull_List_Item::pull_list_id . " INTEGER, "
			. "FOREIGN KEY (". model\pull_list\Pull_List_Item::pull_list_id .")"
				. " REFERENCES " . Pull_List::TABLE . "(" . Pull_List::id . ")"
			. ")";
		$this->sqlite_execute( "pull_list_item", $sql, "Create table pull_list_item" );

		$sql = 'CREATE  INDEX IF NOT EXISTS pull_list_item_name on pull_list_item (name)';
		$this->sqlite_execute( "pull_list_item", $sql, "Index on pull_list_item (name)" );
*/
class Pull_List_Item extends Model
{
	const TABLE = 'pull_list_item';
	const id = 'id';
	const group_name = 'group_name';
	const data = 'data';
	const created = 'created';
	const name = 'name';
	const issue = 'issue';
	const year = 'year';
	const pull_list_id = 'pull_list_id';

	public function tableName() { return Pull_List_Item::TABLE; }
	public function tablePK() { return Pull_List_Item::id; }
	public function sortOrder()
	{
		return array(
			array( 'asc' => Pull_List_Item::group),
			array( 'asc' => Pull_List_Item::name),
			array( 'asc' => Pull_List_Item::issue)
		);
	}

	public function allColumnNames()
	{
		return array(
			Pull_List_Item::id,
			Pull_List_Item::group_name,
			Pull_List_Item::data,
			Pull_List_Item::created,
			Pull_List_Item::name,
			Pull_List_Item::issue,
			Pull_List_Item::year,
			Pull_List_Item::pull_list_id
		);
	}

	/** * * * * * * * * *
		Basic search functions
	 */
	public function allForGroup_name($value)
	{
		return $this->allObjectsForKeyValue(Pull_List_Item::group_name, $value);
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

	public function create( $pull_list, $group_name, $data, $name, $issue, $year)
	{
		$obj = false;
		if ( isset($pull_list, $data, $name) ) {
			$params = array(
				Pull_List_Item::group_name => (isset($group_name) ? $group_name : null),
				Pull_List_Item::data => (isset($data) ? $data : null),
				Pull_List_Item::created => time(),
				Pull_List_Item::name => (isset($name) ? $name : null),
				Pull_List_Item::issue => (isset($issue) ? $issue : null),
				Pull_List_Item::year => (isset($year) ? $year : null),
			);

			if ( isset($pull_list) ) {
				if ( $pull_list instanceof Pull_List) {
					$params[Pull_List_Item::pull_list_id] = $pull_list->id;
				}
				else if (  is_integer($pull_list) ) {
					$params[Pull_List_Item::pull_list_id] = $pull_list;
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
		if ( $object instanceof Pull_List_Item )
		{
			// does not own Pull_List
			return parent::deleteObject($object);
		}

		return false;
	}

}

?>
