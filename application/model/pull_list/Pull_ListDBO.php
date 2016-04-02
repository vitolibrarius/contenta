<?php

namespace model\pull_list;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use model\pull_list\Pull_List as Pull_List;

class Pull_ListDBO extends DataObject
{
	public $name;
	public $etag;
	public $created;
	public $published;
	public $endpoint_id;

	public function displayName()
	{
		return $this->name;
	}

	public function formattedDateTimeCreated() { return $this->formattedDate( Pull_List::created, "M d, Y H:i" ); }
	public function formattedDateCreated() {return $this->formattedDate( Pull_List::created, "M d, Y" ); }

	public function formattedDateTimePublished() { return $this->formattedDate( Pull_List::published, "M d, Y H:i" ); }
	public function formattedDatePublished() {return $this->formattedDate( Pull_List::published, "M d, Y" ); }


	// to-one relationship
	public function endpoint()
	{
		if ( isset( $this->endpoint_id ) ) {
			$model = Model::Named('Endpoint');
			return $model->objectForId($this->endpoint_id);
		}
		return false;
	}

	// to-many relationship
	public function pull_list_items()
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Pull_List_Item');
			return $model->allObjectsForKeyValue( Pull_List_Item::pull_list_id, $this->id);
		}

		return false;
	}

	// to-many relationship
	public function exclusions()
	{
		if ( isset( $this->endpoint_id ) ) {
			$model = Model::Named('Pull_List_Exclusion');
			return $model->allObjectsForKeyValue( Pull_List_Exclusion::endpoint_id, $this->endpoint_id);
		}

		return false;
	}

	// to-many relationship
	public function expansions()
	{
		if ( isset( $this->endpoint_id ) ) {
			$model = Model::Named('Pull_List_Expansion');
			return $model->allObjectsForKeyValue( Pull_List_Expansion::endpoint_id, $this->endpoint_id);
		}

		return false;
	}

}

?>
