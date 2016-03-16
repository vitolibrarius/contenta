<?php

namespace model\pull_list;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use model\pull_list\Pull_List_ItemDBO as Pull_List_ItemDBO;

/** Sample Creation script
		$sql = "CREATE TABLE IF NOT EXISTS " . pull_list_item . " ( "
			. Pull_List_Item::id . " INTEGER PRIMARY KEY, "
			. Pull_List_Item::group . " TEXT, "
			. Pull_List_Item::data . " TEXT, "
			. Pull_List_Item::created . " INTEGER, "
			. Pull_List_Item::name . " TEXT, "
			. Pull_List_Item::issue . " TEXT, "
			. Pull_List_Item::year . " INTEGER, "
			. Pull_List_Item::pull_list_id . " INTEGER, "
			. "FOREIGN KEY (". Pull_List_Item::pull_list_id .") REFERENCES " . model\pull_list\Pull_List::TABLE . "(" . model\pull_list\Pull_List::id . "),"
			. ")";
		$this->sqlite_execute( "pull_list_item", $sql, "Create table pull_list_item" );
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



	// to-one relationship
	public function pull_list()
	{
		if ( isset( $this->pull_list_id ) ) {
			$model = Model::Named('model\pull_list\Pull_List');
			return $model->objectForId($this->pull_list_id);
		}
		return false;
	}

}

?>
