<?php

namespace model\pull_list;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use model\pull_list\Pull_List as Pull_List;

class Pull_ListDBO extends DataObject
{
	public $id;
	public $name;
	public $etag;
	public $created;
	public $published;
	public $endpoint_id;

	public function displayName()
	{
		return $this->name;
	}
}

?>
