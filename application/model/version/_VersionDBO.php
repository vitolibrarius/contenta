<?php

namespace model\version;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\version\Version as Version;

/* import related objects */
use \model\version\Patch as Patch;
use \model\version\PatchDBO as PatchDBO;

class _VersionDBO extends DataObject
{
	public $code;
	public $major;
	public $minor;
	public $patch;
	public $created;


	public function formattedDateTime_created() { return $this->formattedDate( Version::created, "M d, Y H:i" ); }
	public function formattedDate_created() {return $this->formattedDate( Version::created, "M d, Y" ); }


	// to-many relationship
	public function patches()
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Patch');
			return $model->allObjectsForKeyValue( Patch::version_id, $this->id);
		}

		return false;
	}

}

?>
