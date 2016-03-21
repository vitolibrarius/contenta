<?php

namespace model\pull_list;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use model\pull_list\Pull_List_Item as Pull_List_Item;

class Pull_List_ItemDBO extends DataObject
{
	public $id;
	public $group_name;
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

	public function formattedDateTimeCreated() { return $this->formattedDate( Pull_List_Item::created, "M d, Y H:i" ); }
	public function formattedDateCreated() {return $this->formattedDate( Pull_List_Item::created, "M d, Y" ); }

	// to-one relationship
	public function pull_list()
	{
		if ( isset( $this->pull_list_id ) ) {
			$model = Model::Named('Pull_List');
			return $model->objectForId($this->pull_list_id);
		}
		return false;
	}

}

?>
