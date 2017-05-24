<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \model\media\Artist_Role as Artist_Role;

/* import related objects */

abstract class _Artist_RoleDBO extends DataObject
{
	public $code;
	public $name;
	public $enabled;

	public function displayName()
	{
		return $this->name;
	}

	public function pkValue()
	{
		return $this->{Artist_Role::code};
	}

	public function modelName()
	{
		return "Artist_Role";
	}

	public function dboName()
	{
		return "\model\media\Artist_RoleDBO";
	}

	public function isEnabled() {
		return (isset($this->enabled) && $this->enabled == Model::TERTIARY_TRUE);
	}



	/** Attributes */
	public function name()
	{
		return parent::changedValue( Artist_Role::name, $this->name );
	}

	public function setName( $value = null)
	{
		parent::storeChange( Artist_Role::name, $value );
	}

	public function enabled()
	{
		return parent::changedValue( Artist_Role::enabled, $this->enabled );
	}

	public function setEnabled( $value = null)
	{
		parent::storeChange( Artist_Role::enabled, $value );
	}


}

?>
