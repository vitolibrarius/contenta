<?php

namespace model\reading;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\reading\Reading_Queue_ItemDBO as Reading_Queue_ItemDBO;

/* import related objects */
use \model\reading\Reading_Queue as Reading_Queue;
use \model\reading\Reading_QueueDBO as Reading_QueueDBO;
use \model\reading\Reading_Item as Reading_Item;
use \model\reading\Reading_ItemDBO as Reading_ItemDBO;

/** Sample Creation script */
		/** READING_QUEUE_ITEM */
/*
		$sql = "CREATE TABLE IF NOT EXISTS reading_queue_item ( "
			. Reading_Queue_Item::id . " INTEGER PRIMARY KEY, "
			. Reading_Queue_Item::issue_order . " INTEGER, "
			. Reading_Queue_Item::reading_item_id . " INTEGER, "
			. Reading_Queue_Item::reading_queue_id . " INTEGER, "
			. "FOREIGN KEY (". Reading_Queue_Item::reading_queue_id .") REFERENCES " . Reading_Queue::TABLE . "(" . Reading_Queue::id . "),"
			. "FOREIGN KEY (". Reading_Queue_Item::reading_item_id .") REFERENCES " . Reading_Item::TABLE . "(" . Reading_Item::id . ")"
		. ")";
		$this->sqlite_execute( "reading_queue_item", $sql, "Create table reading_queue_item" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS reading_queue_item_reading_item_idreading_queue_id on reading_queue_item (reading_item_id,reading_queue_id)';
		$this->sqlite_execute( "reading_queue_item", $sql, "Index on reading_queue_item (reading_item_id,reading_queue_id)" );
*/
abstract class _Reading_Queue_Item extends Model
{
	const TABLE = 'reading_queue_item';

	// attribute keys
	const id = 'id';
	const issue_order = 'issue_order';
	const reading_item_id = 'reading_item_id';
	const reading_queue_id = 'reading_queue_id';

	// relationship keys
	const reading_queue = 'reading_queue';
	const reading_item = 'reading_item';

	public function tableName() { return Reading_Queue_Item::TABLE; }
	public function tablePK() { return Reading_Queue_Item::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Reading_Queue_Item::issue_order)
		);
	}

	public function allColumnNames()
	{
		return array(
			Reading_Queue_Item::id,
			Reading_Queue_Item::issue_order,
			Reading_Queue_Item::reading_item_id,
			Reading_Queue_Item::reading_queue_id
		);
	}

	public function allAttributes()
	{
		return array(
			Reading_Queue_Item::issue_order,
		);
	}

	public function allForeignKeys()
	{
		return array(Reading_Queue_Item::reading_queue_id,
			Reading_Queue_Item::reading_item_id);
	}

	public function allRelationshipNames()
	{
		return array(
			Reading_Queue_Item::reading_queue,
			Reading_Queue_Item::reading_item
		);
	}

	/**
	 *	Simple fetches
	 */

	public function allForIssue_order($value)
	{
		return $this->allObjectsForKeyValue(Reading_Queue_Item::issue_order, $value);
	}




	/**
	 * Simple relationship fetches
	 */
	public function allForReading_queue($obj)
	{
		return $this->allObjectsForFK(Reading_Queue_Item::reading_queue_id, $obj, $this->sortOrder(), 50);
	}

	public function countForReading_queue($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Reading_Queue_Item::reading_queue_id, $obj );
		}
		return false;
	}
	public function allForReading_item($obj)
	{
		return $this->allObjectsForFK(Reading_Queue_Item::reading_item_id, $obj, $this->sortOrder(), 50);
	}

	public function countForReading_item($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Reading_Queue_Item::reading_item_id, $obj );
		}
		return false;
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "reading_queue":
					return array( Reading_Queue_Item::reading_queue_id, "id"  );
					break;
				case "reading_item":
					return array( Reading_Queue_Item::reading_item_id, "id"  );
					break;
				default:
					break;
			}
		}
		return parent::joinAttributes( $joinModel );
	}

	/**
	 *	Create/Update functions
	 */
	public function createObject( array $values = array() )
	{
		if ( isset($values) ) {

			// default values for attributes
			if ( isset($values['issue_order']) == false ) {
				$default_issue_order = $this->attributeDefaultValue( null, null, Reading_Queue_Item::issue_order);
				if ( is_null( $default_issue_order ) == false ) {
					$values['issue_order'] = $default_issue_order;
				}
			}

			// default conversion for relationships
			if ( isset($values['reading_queue']) ) {
				$local_reading_queue = $values['reading_queue'];
				if ( $local_reading_queue instanceof Reading_QueueDBO) {
					$values[Reading_Queue_Item::reading_queue_id] = $local_reading_queue->id;
				}
				else if ( is_integer( $local_reading_queue) ) {
					$params[Reading_Queue_Item::reading_queue_id] = $local_reading_queue;
				}
			}
			if ( isset($values['reading_item']) ) {
				$local_reading_item = $values['reading_item'];
				if ( $local_reading_item instanceof Reading_ItemDBO) {
					$values[Reading_Queue_Item::reading_item_id] = $local_reading_item->id;
				}
				else if ( is_integer( $local_reading_item) ) {
					$params[Reading_Queue_Item::reading_item_id] = $local_reading_item;
				}
			}
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Reading_Queue_Item ) {
			if ( isset($values['reading_queue']) ) {
				$local_reading_queue = $values['reading_queue'];
				if ( $local_reading_queue instanceof Reading_QueueDBO) {
					$values[Reading_Queue_Item::reading_queue_id] = $local_reading_queue->id;
				}
				else if ( is_integer( $local_reading_queue) ) {
					$params[Reading_Queue_Item::reading_queue_id] = $values['reading_queue'];
				}
			}
			if ( isset($values['reading_item']) ) {
				$local_reading_item = $values['reading_item'];
				if ( $local_reading_item instanceof Reading_ItemDBO) {
					$values[Reading_Queue_Item::reading_item_id] = $local_reading_item->id;
				}
				else if ( is_integer( $local_reading_item) ) {
					$params[Reading_Queue_Item::reading_item_id] = $values['reading_item'];
				}
			}
		}

		return parent::updateObject($object, $values);
	}

	/**
	 *	Delete functions
	 */
	public function deleteObject( DataObject $object = null)
	{
		if ( $object instanceof Reading_Queue_ItemDBO )
		{
			// does not own reading_queue Reading_Queue
			// does not own reading_item Reading_Item
			return parent::deleteObject($object);
		}

		return false;
	}

	public function deleteAllForReading_queue(Reading_QueueDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForReading_queue($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForReading_queue($obj);
			}
		}
		return $success;
	}
	public function deleteAllForReading_item(Reading_ItemDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForReading_item($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForReading_item($obj);
			}
		}
		return $success;
	}

	/**
	 * Named fetches
	 */
	public function objectForQueueAndItem(Reading_QueueDBO $queue,Reading_ItemDBO $item )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		$qualifiers[] = Qualifier::FK( 'reading_queue_id', $queue);
		$qualifiers[] = Qualifier::FK( 'reading_item_id', $item);

		if ( count($qualifiers) > 0 ) {
			$select->where( Qualifier::Combine( 'AND', $qualifiers ));
		}

		$result = $select->fetchAll();
		if ( is_array($result) ) {
			$result_size = count($result);
			if ( $result_size == 1 ) {
				return $result[0];
			}
			else if ($result_size > 1 ) {
				throw new \Exception( "objectForQueueAndItem expected 1 result, but fetched " . count($result) );
			}
		}

		return false;
	}


	/**
	 * Attribute editing
	 */

	public function attributesMap() {
		return array(
			Reading_Queue_Item::issue_order => Model::INT_TYPE,
			Reading_Queue_Item::reading_item_id => Model::TO_ONE_TYPE,
			Reading_Queue_Item::reading_queue_id => Model::TO_ONE_TYPE
		);
	}

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) === false || is_null($object) == true) {
			switch ($attr) {
				case Reading_Queue_Item::issue_order:
					return 0;
			}
		}
		return parent::attributeDefaultValue($object, $type, $attr);
	}

	/*
	 * return the foreign key object
	 */
	public function attributeObject($object = null, $type = null, $attr, $value)
	{
		$fkObject = false;
		if ( isset( $attr ) ) {
			switch ( $attr ) {
				case Reading_Queue_Item::reading_queue_id:
					$reading_queue_model = Model::Named('Reading_Queue');
					$fkObject = $reading_queue_model->objectForId( $value );
					break;
				case Reading_Queue_Item::reading_item_id:
					$reading_item_model = Model::Named('Reading_Item');
					$fkObject = $reading_item_model->objectForId( $value );
					break;
				default:
					break;
			}
		}
		return $fkObject;
	}

	/**
	 * Validation
	 */
	function validate_issue_order($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Reading_Queue_Item::issue_order,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
	function validate_reading_item_id($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Reading_Queue_Item::reading_item_id,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_reading_queue_id($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Reading_Queue_Item::reading_queue_id,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
}

?>
