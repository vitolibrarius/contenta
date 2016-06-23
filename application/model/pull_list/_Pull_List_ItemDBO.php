<?php

namespace model\pull_list;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\pull_list\Pull_List_Item as Pull_List_Item;

/* import related objects */
use \model\pull_list\Pull_List_Group as Pull_List_Group;
use \model\pull_list\Pull_List_GroupDBO as Pull_List_GroupDBO;
use \model\pull_list\Pull_List as Pull_List;
use \model\pull_list\Pull_ListDBO as Pull_ListDBO;

abstract class _Pull_List_ItemDBO extends DataObject
{
	public $data;
	public $created;
	public $search_name;
	public $name;
	public $issue;
	public $year;
	public $pull_list_id;
	public $pull_list_group_id;

	public function displayName()
	{
		return $this->name;
	}

	public function formattedDateTime_created() { return $this->formattedDate( Pull_List_Item::created, "M d, Y H:i" ); }
	public function formattedDate_created() {return $this->formattedDate( Pull_List_Item::created, "M d, Y" ); }


	// to-one relationship
	public function pull_list_group()
	{
		if ( isset( $this->pull_list_group_id ) ) {
			$model = Model::Named('Pull_List_Group');
			return $model->objectForId($this->pull_list_group_id);
		}
		return false;
	}

	// to-one relationship
	public function pull_list()
	{
		if ( isset( $this->pull_list_id ) ) {
			$model = Model::Named('Pull_List');
			return $model->objectForId($this->pull_list_id);
		}
		return false;
	}


	/** Attributes */
	public function data()
	{
		return parent::changedValue( Pull_List_Item::data, $this->data );
	}

	public function setData( $value = null)
	{
		parent::storeChange( Pull_List_Item::data, $value );
	}

	public function search_name()
	{
		return parent::changedValue( Pull_List_Item::search_name, $this->search_name );
	}

	public function setSearch_name( $value = null)
	{
		parent::storeChange( Pull_List_Item::search_name, $value );
	}

	public function name()
	{
		return parent::changedValue( Pull_List_Item::name, $this->name );
	}

	public function setName( $value = null)
	{
		parent::storeChange( Pull_List_Item::name, $value );
	}

	public function issue()
	{
		return parent::changedValue( Pull_List_Item::issue, $this->issue );
	}

	public function setIssue( $value = null)
	{
		parent::storeChange( Pull_List_Item::issue, $value );
	}

	public function year()
	{
		return parent::changedValue( Pull_List_Item::year, $this->year );
	}

	public function setYear( $value = null)
	{
		parent::storeChange( Pull_List_Item::year, $value );
	}


}

?>
