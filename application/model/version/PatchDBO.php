<?php

namespace model\version;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use model\version\Patch as Patch;

class PatchDBO extends DataObject
{
	public $id;
	public $name;
	public $created;
	public $version_id;

	public function displayName()
	{
		return $this->name;
	}

	public function formattedDateTimeCreated() { return $this->formattedDate( Patch::created, "M d, Y H:i" ); }
	public function formattedDateCreated() {return $this->formattedDate( Patch::created, "M d, Y" ); }

	// to-one relationship
	public function version()
	{
		if ( isset( $this->version_id ) ) {
			$model = Model::Named('Version');
			return $model->objectForId($this->version_id);
		}
		return false;
	}

}

?>
