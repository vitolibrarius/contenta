<?php

namespace model\logs;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use model\logs\Log_Level as Log_Level;

class Log_LevelDBO extends DataObject
{
	public $id;
	public $code;
	public $name;

	public function displayName()
	{
		return $this->name;
	}


}

?>
