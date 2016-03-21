<?php

namespace model\pull_list;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use model\pull_list\Pull_List_Exclusion as Pull_List_Exclusion;

class Pull_List_ExclusionDBO extends DataObject
{
	public $id;
	public $pattern;
	public $type;
	public $created;
	public $endpoint_id;


	public function formattedDateTimeCreated() { return $this->formattedDate( Pull_List_Exclusion::created, "M d, Y H:i" ); }
	public function formattedDateCreated() {return $this->formattedDate( Pull_List_Exclusion::created, "M d, Y" ); }

	// to-one relationship
	public function endpoint()
	{
		if ( isset( $this->endpoint_id ) ) {
			$model = Model::Named('Endpoint');
			return $model->objectForId($this->endpoint_id);
		}
		return false;
	}

}

?>
