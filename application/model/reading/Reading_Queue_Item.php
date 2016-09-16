<?php

namespace model\reading;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;

use \db\Qualifier as Qualifier;

use \model\reading\Reading_Queue_ItemDBO as Reading_Queue_ItemDBO;

/* import related objects */
use \model\reading\Reading_Queue as Reading_Queue;
use \model\reading\Reading_QueueDBO as Reading_QueueDBO;
use \model\reading\Reading_Item as Reading_Item;
use \model\reading\Reading_ItemDBO as Reading_ItemDBO;

class Reading_Queue_Item extends _Reading_Queue_Item
{
	public function notifyKeypaths() { return array( "reading_queue" ); }

	/**
	 *	Create/Update functions
	 */
	public function createObject( array $values = array())
	{
		if ( isset($values) ) {
		}

		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Reading_Queue_ItemDBO ) {
			// massage values as necessary
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		$attrFor = array(
			Reading_Queue_Item::reading_item_id,
			Reading_Queue_Item::reading_queue_id
		);
		return array_intersect_key($this->attributesMap(),array_flip($attrFor));
	}

	/*
	public function attributeIsEditable($object = null, $type = null, $attr)
	{
		// add customization here
		return parent::attributeIsEditable($object, $type, $attr);
	}
	*/

	/*
	public function attributeRestrictionMessage($object = null, $type = null, $attr)	{ return null; }
	public function attributePlaceholder($object = null, $type = null, $attr)	{ return null; }
	*/

	/*
	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		return parent::attributeDefaultValue($object, $type, $attr);
	}
	*/

	/*
	public function attributeEditPattern($object = null, $type = null, $attr)
	{
		return null;
	}
	*/

	public function attributeOptions($object = null, $type = null, $attr)
	{
		if ( Reading_Queue_Item::reading_queue_id == $attr ) {
			$model = Model::Named('Reading_Queue');
			return $model->allObjects();
		}
		if ( Reading_Queue_Item::reading_item_id == $attr ) {
			$model = Model::Named('Reading_Item');
			return $model->allObjects();
		}
		return null;
	}

	/** Validation */
/*
	function validate_reading_item_id($object = null, $value)
	{
		return parent::validate_reading_item_id($object, $value);
	}
*/

/*
	function validate_reading_queue_id($object = null, $value)
	{
		return parent::validate_reading_queue_id($object, $value);
	}
*/
	public function createJoin( Reading_QueueDBO $queue, Reading_ItemDBO $item )
	{
		if (isset($queue, $queue->id, $item, $item->id)) {
			$join = $this->objectForQueueAndItem($queue, $item);
			if ($join == false) {
				$order = 0;
				if ( isset( $queue->series_id, $item->publication_id )) {
					$order = $item->publication()->issue_order;
				}
				else if ( isset( $queue->story_arc_id, $item->publication_id )) {
					// FIXME: this should calculate the story arc order
					$order = $item->publication()->issue_order;
				}

				list( $join, $errorList ) = $this->createObject(array(
					Reading_Queue_Item::reading_queue => $queue,
					Reading_Queue_Item::reading_item => $item,
					Reading_Queue_Item::issue_order => $order
					)
				);

				if ( is_array($errorList) ) {
					return $errorList;
				}
			}
			return $join;
		}
		return false;
	}

	public function deleteObject( DataObject $object = null)
	{
		if ( $object instanceof Reading_Queue_ItemDBO )
		{
			$reading_item = $object->reading_item();
			$success = parent::deleteObject($object);
			if ( $success == true ) {
				$count = $this->countForReading_item($reading_item);
				if ( $count == 0 ) {
					$success = Model::Named( "Reading_Item" )->deleteObject($reading_item);
				}
			}
			return $success;
		}

		return false;
	}

	public function allForReadingQueue( Reading_QueueDBO $queue, $unreadOnly = true, $limit = 50 )
	{
		if ( $queue != null ) {
			$select = \SQL::SelectJoin( $this, $this->allColumnNames(), Qualifier::Equals(Reading_Queue_Item::reading_queue_id, $queue->pkValue()) );
			if ( $unreadOnly == true ) {
				$select->joinOn( $this, Model::Named("Reading_Item"), null, Qualifier::IsNull( Reading_Item::read_date ));
			}
			$select->limit = $limit;
			return $select->fetchAll();
		}
		return false;
	}
}

?>
