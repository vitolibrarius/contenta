<?php

namespace model\logs;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\logs\Log_Level as Log_Level;

/* import related objects */

abstract class _Log_LevelDBO extends DataObject
{
	public $code;
	public $name;

	public function displayName()
	{
		return $this->name;
	}

	public function pkValue()
	{
		return $this->{Log_Level::code};
	}



	/** Attributes */
	public function name()
	{
		return parent::changedValue( Log_Level::name, $this->name );
	}

	public function setName( $value = null)
	{
		parent::storeChange( Log_Level::name, $value );
	}


}

?>
