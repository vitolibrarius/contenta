<?php

namespace model\pull_list;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\pull_list\Pull_List as Pull_List;

/* import related objects */
use \model\Endpoint as Endpoint;
use \model\EndpointDBO as EndpointDBO;
use \model\pull_list\Pull_List_Item as Pull_List_Item;
use \model\pull_list\Pull_List_ItemDBO as Pull_List_ItemDBO;

abstract class _Pull_ListDBO extends DataObject
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

	public function formattedDateTime_created() { return $this->formattedDate( Pull_List::created, "M d, Y H:i" ); }
	public function formattedDate_created() {return $this->formattedDate( Pull_List::created, "M d, Y" ); }

	public function formattedDateTime_published() { return $this->formattedDate( Pull_List::published, "M d, Y H:i" ); }
	public function formattedDate_published() {return $this->formattedDate( Pull_List::published, "M d, Y" ); }


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


	/** Attributes */
	public function name()
	{
		return parent::changedValue( Pull_List::name, $this->name );
	}

	public function setName( $value = null)
	{
		parent::storeChange( Pull_List::name, $value );
	}

	public function etag()
	{
		return parent::changedValue( Pull_List::etag, $this->etag );
	}

	public function setEtag( $value = null)
	{
		parent::storeChange( Pull_List::etag, $value );
	}

	public function published()
	{
		return parent::changedValue( Pull_List::published, $this->published );
	}

	public function setPublished( $value = null)
	{
		parent::storeChange( Pull_List::published, $value );
	}

	public function endpoint_id()
	{
		return parent::changedValue( Pull_List::endpoint_id, $this->endpoint_id );
	}

	public function setEndpoint_id( $value = null)
	{
		parent::storeChange( Pull_List::endpoint_id, $value );
	}


}

?>
