<?php

namespace model\pull_list;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \model\pull_list\Pull_List as Pull_List;

/* import related objects */
use \model\network\Endpoint as Endpoint;
use \model\network\EndpointDBO as EndpointDBO;
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

	public function pkValue()
	{
		return $this->{Pull_List::id};
	}

	public function modelName()
	{
		return "Pull_List";
	}

	public function dboName()
	{
		return "\model\pull_list\Pull_ListDBO";
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

	public function setEndpoint(EndpointDBO $obj = null)
	{
		if ( isset($obj, $obj->id) && (isset($this->endpoint_id) == false || $obj->id != $this->endpoint_id) ) {
			parent::storeChange( Pull_List::endpoint_id, $obj->id );
			$this->saveChanges();
		}
	}

	// to-many relationship
	public function pull_list_items($limit = SQL::SQL_DEFAULT_LIMIT)
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Pull_List_Item');
			return $model->allObjectsForKeyValue(
				Pull_List_Item::pull_list_id,
				$this->id,
				null,
				$limit
			);
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


}

?>
