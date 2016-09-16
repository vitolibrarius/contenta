<?php

namespace model\reading;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\reading\Reading_Queue_Item as Reading_Queue_Item;

/* import related objects */
use \model\reading\Reading_Queue as Reading_Queue;
use \model\reading\Reading_QueueDBO as Reading_QueueDBO;
use \model\reading\Reading_Item as Reading_Item;
use \model\reading\Reading_ItemDBO as Reading_ItemDBO;

abstract class _Reading_Queue_ItemDBO extends DataObject
{
	public $issue_order;
	public $reading_item_id;
	public $reading_queue_id;


	public function pkValue()
	{
		return $this->{Reading_Queue_Item::id};
	}


	// to-one relationship
	public function reading_queue()
	{
		if ( isset( $this->reading_queue_id ) ) {
			$model = Model::Named('Reading_Queue');
			return $model->objectForId($this->reading_queue_id);
		}
		return false;
	}

	public function setReading_queue(Reading_QueueDBO $obj = null)
	{
		if ( isset($obj, $obj->id) && (isset($this->reading_queue_id) == false || $obj->id != $this->reading_queue_id) ) {
			parent::storeChange( Reading_Queue_Item::reading_queue_id, $obj->id );
			$this->saveChanges();
		}
	}

	// to-one relationship
	public function reading_item()
	{
		if ( isset( $this->reading_item_id ) ) {
			$model = Model::Named('Reading_Item');
			return $model->objectForId($this->reading_item_id);
		}
		return false;
	}

	public function setReading_item(Reading_ItemDBO $obj = null)
	{
		if ( isset($obj, $obj->id) && (isset($this->reading_item_id) == false || $obj->id != $this->reading_item_id) ) {
			parent::storeChange( Reading_Queue_Item::reading_item_id, $obj->id );
			$this->saveChanges();
		}
	}


	/** Attributes */
	public function issue_order()
	{
		return parent::changedValue( Reading_Queue_Item::issue_order, $this->issue_order );
	}

	public function setIssue_order( $value = null)
	{
		parent::storeChange( Reading_Queue_Item::issue_order, $value );
	}


}

?>
