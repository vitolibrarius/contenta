<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

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
