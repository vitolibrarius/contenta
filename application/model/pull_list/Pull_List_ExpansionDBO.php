<?php

namespace model\pull_list;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use model\pull_list\Pull_List_Expansion as Pull_List_Expansion;

class Pull_List_ExpansionDBO extends DataObject
{
	public $id;
	public $pattern;
	public $replace;
	public $created;
	public $endpoint_id;

}

?>
