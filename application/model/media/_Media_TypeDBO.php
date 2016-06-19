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



	/** Attributes */
	public function code()
	{
		return parent::changedValue( Media_Type::code, $this->code );
	}

	public function setCode( $value = null)
	{
		parent::storeChange( Media_Type::code, $value );
	}

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
