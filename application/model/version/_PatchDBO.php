<?php

namespace model\version;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\version\Patch as Patch;

/* import related objects */
use \model\version\Version as Version;
use \model\version\VersionDBO as VersionDBO;

abstract class _PatchDBO extends DataObject
{
	public $name;
	public $created;
	public $version_id;

	public function displayName()
	{
		return $this->name;
	}

	public function formattedDateTime_created() { return $this->formattedDate( Patch::created, "M d, Y H:i" ); }
	public function formattedDate_created() {return $this->formattedDate( Patch::created, "M d, Y" ); }


	// to-one relationship
	public function version()
	{
		if ( isset( $this->version_id ) ) {
			$model = Model::Named('Version');
			return $model->objectForId($this->version_id);
		}
		return false;
	}


	/** Attributes */
	public function name()
	{
		return parent::changedValue( Patch::name, $this->name );
	}

	public function setName( $value = null)
	{
		parent::storeChange( Patch::name, $value );
	}

	public function version_id()
	{
		return parent::changedValue( Patch::version_id, $this->version_id );
	}

	public function setVersion_id( $value = null)
	{
		parent::storeChange( Patch::version_id, $value );
	}


}

?>
