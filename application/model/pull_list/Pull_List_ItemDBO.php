<?php

namespace model\pull_list;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use model\pull_list\Pull_List_Item as Pull_List_Item;

class Pull_List_ItemDBO extends DataObject
{
	public $id;
	public $group;
	public $data;
	public $created;
	public $name;
	public $issue;
	public $year;
	public $pull_list_id;

	public function displayName()
	{
		return $this->name;
	}
}

?>
