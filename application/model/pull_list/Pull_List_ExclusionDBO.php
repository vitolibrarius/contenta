<?php

namespace model\pull_list;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use model\pull_list\Pull_List_Exclusion as Pull_List_Exclusion;

class Pull_List_ExclusionDBO extends DataObject
{
	public $id;
	public $pattern;
	public $type;
	public $created;
	public $endpoint_id;

}

?>
