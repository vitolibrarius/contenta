<?php

namespace model\pull_list;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\pull_list\Pull_List_Group as Pull_List_Group;

/* import related objects */
use \model\pull_list\Pull_List_Item as Pull_List_Item;
use \model\pull_list\Pull_List_ItemDBO as Pull_List_ItemDBO;

abstract class _Pull_List_GroupDBO extends DataObject
{
	public $name;
	public $data;
	public $created;

	public function displayName()
	{
		return $this->name;
	}

	public function pkValue()
	{
		return $this->{Pull_List_Group::id};
	}

	public function formattedDateTime_created() { return $this->formattedDate( Pull_List_Group::created, "M d, Y H:i" ); }
	public function formattedDate_created() {return $this->formattedDate( Pull_List_Group::created, "M d, Y" ); }


	// to-many relationship
	public function pull_list_items()
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Pull_List_Item');
			return $model->allObjectsForKeyValue( Pull_List_Item::pull_list_group_id, $this->id);
		}

		return false;
	}


	/** Attributes */
	public function name()
	{
		return parent::changedValue( Pull_List_Group::name, $this->name );
	}

	public function setName( $value = null)
	{
		parent::storeChange( Pull_List_Group::name, $value );
	}

	public function data()
	{
		return parent::changedValue( Pull_List_Group::data, $this->data );
	}

	public function setData( $value = null)
	{
		parent::storeChange( Pull_List_Group::data, $value );
	}


}

?>
