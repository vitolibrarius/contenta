<?php

namespace model\version;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \model\version\Version as Version;

/* import related objects */
use \model\version\Patch as Patch;
use \model\version\PatchDBO as PatchDBO;

abstract class _VersionDBO extends DataObject
{
	public $code;
	public $major;
	public $minor;
	public $patch;
	public $created;


	public function pkValue()
	{
		return $this->{Version::id};
	}

	public function modelName()
	{
		return "Version";
	}

	public function dboName()
	{
		return "\model\version\VersionDBO";
	}

	public function formattedDateTime_created() { return $this->formattedDate( Version::created, "M d, Y H:i" ); }
	public function formattedDate_created() {return $this->formattedDate( Version::created, "M d, Y" ); }


	// to-many relationship
	public function patches($limit = SQL::SQL_DEFAULT_LIMIT)
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Patch');
			return $model->allObjectsForKeyValue(
				Patch::version_id,
				$this->id,
				null,
				$limit
			);
		}

		return false;
	}


	/** Attributes */
	public function code()
	{
		return parent::changedValue( Version::code, $this->code );
	}

	public function setCode( $value = null)
	{
		parent::storeChange( Version::code, $value );
	}

	public function major()
	{
		return parent::changedValue( Version::major, $this->major );
	}

	public function setMajor( $value = null)
	{
		parent::storeChange( Version::major, $value );
	}

	public function minor()
	{
		return parent::changedValue( Version::minor, $this->minor );
	}

	public function setMinor( $value = null)
	{
		parent::storeChange( Version::minor, $value );
	}

	public function patch()
	{
		return parent::changedValue( Version::patch, $this->patch );
	}

	public function setPatch( $value = null)
	{
		parent::storeChange( Version::patch, $value );
	}


}

?>
