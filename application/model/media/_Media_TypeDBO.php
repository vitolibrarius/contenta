<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \model\media\Media_Type as Media_Type;

/* import related objects */

abstract class _Media_TypeDBO extends DataObject
{
	public $code;
	public $name;

	public function displayName()
	{
		return $this->name;
	}

	public function pkValue()
	{
		return $this->{Media_Type::code};
	}

	public function modelName()
	{
		return "Media_Type";
	}

	public function dboName()
	{
		return "\model\media\Media_TypeDBO";
	}



	/** Attributes */
	public function name()
	{
		return parent::changedValue( Media_Type::name, $this->name );
	}

	public function setName( $value = null)
	{
		parent::storeChange( Media_Type::name, $value );
	}


}

?>
