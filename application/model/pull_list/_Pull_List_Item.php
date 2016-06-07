<?php

namespace model\pull_list;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \model\pull_list\Pull_List_ItemDBO as Pull_List_ItemDBO;

/* import related objects */
use \model\pull_list\Pull_List as Pull_List;
use \model\pull_list\Pull_ListDBO as Pull_ListDBO;

/** Sample Creation script */
		/** PULL_LIST_ITEM */
/*
		$sql = "CREATE TABLE IF NOT EXISTS pull_list_item ( "
			. Pull_List_Item::id . " INTEGER PRIMARY KEY, "
			. Pull_List_Item::group_name . " TEXT, "
			. Pull_List_Item::data . " TEXT, "
			. Pull_List_Item::created . " INTEGER, "
			. Pull_List_Item::name . " TEXT, "
			. Pull_List_Item::issue . " TEXT, "
			. Pull_List_Item::year . " INTEGER, "
			. Pull_List_Item::pull_list_id . " INTEGER, "
			. "FOREIGN KEY (". Pull_List_Item::pull_list_id .") REFERENCES " . Pull_List::TABLE . "(" . Pull_List::id . ")"
		. ")";
		$this->sqlite_execute( "pull_list_item", $sql, "Create table pull_list_item" );

		$sql = 'CREATE  INDEX IF NOT EXISTS pull_list_item_name on pull_list_item (name)';
		$this->sqlite_execute( "pull_list_item", $sql, "Index on pull_list_item (name)" );
*/
abstract class _Pull_List_Item extends Model
{
	const TABLE = 'pull_list_item';
	const id = 'id';
	const group_name = 'group_name';
	const data = 'data';
	const created = 'created';
	const name = 'name';
	const issue = 'issue';
	const year = 'year';
	const pull_list_id = 'pull_list_id';

	public function tableName() { return Pull_List_Item::TABLE; }
	public function tablePK() { return Pull_List_Item::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Pull_List_Item::group),
			array( 'asc' => Pull_List_Item::name),
			array( 'asc' => Pull_List_Item::issue)
		);
	}

	public function allColumnNames()
	{
		return array(
			Pull_List_Item::id,
			Pull_List_Item::group_name,
			Pull_List_Item::data,
			Pull_List_Item::created,
			Pull_List_Item::name,
			Pull_List_Item::issue,
			Pull_List_Item::year,
			Pull_List_Item::pull_list_id
		);
	}

	/**
	 *	Simple fetches
	 */
	public function allForGroup_name($value)
	{
		return $this->allObjectsForKeyValue(Pull_List_Item::group_name, $value);
	}

	public function allForData($value)
	{
		return $this->allObjectsForKeyValue(Pull_List_Item::data, $value);
	}

	public function allForName($value)
	{
		return $this->allObjectsForKeyValue(Pull_List_Item::name, $value);
	}

	public function allForIssue($value)
	{
		return $this->allObjectsForKeyValue(Pull_List_Item::issue, $value);
	}


	public function allForPull_list($obj)
	{
		return $this->allObjectsForFK(Pull_List_Item::pull_list_id, $obj, $this->sortOrder(), 50);
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "pull_list":
					return array( Pull_List_Item::pull_list_id, "id"  );
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
			if ( isset($values['pull_list']) ) {
				$local_pull_list = $values['pull_list'];
				if ( $local_pull_list instanceof Pull_ListDBO) {
					$values[Pull_List_Item::pull_list_id] = $local_pull_list->id;
				}
				else if ( is_integer( $local_pull_list) ) {
					$params[Pull_List_Item::pull_list_id] = $local_pull_list;
				}
			}
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Pull_List_Item ) {
			if ( isset($values['pull_list']) ) {
				$local_pull_list = $values['pull_list'];
				if ( $local_pull_list instanceof Pull_ListDBO) {
					$values[Pull_List_Item::pull_list_id] = $local_pull_list->id;
				}
				else if ( is_integer( $local_pull_list) ) {
					$params[Pull_List_Item::pull_list_id] = $values['pull_list'];
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
		if ( $object instanceof Pull_List_Item )
		{
			// does not own Pull_List
			return parent::deleteObject($object);
		}

		return false;
	}

	public function deleteAllForPull_list(Pull_ListDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForPull_list($obj);
			foreach ($array as $key => $value) {
				if ($this->deleteObject($value) == false) {
					$success = false;
					break;
				}
			}
		}
		return $success;
	}

	/**
	 *	Named fetches
	 */

	/** Set attributes */
	public function setGroup_name( Pull_List_ItemDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Pull_List_Item::group_name => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setData( Pull_List_ItemDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Pull_List_Item::data => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setCreated( Pull_List_ItemDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Pull_List_Item::created => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setName( Pull_List_ItemDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Pull_List_Item::name => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setIssue( Pull_List_ItemDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Pull_List_Item::issue => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setYear( Pull_List_ItemDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Pull_List_Item::year => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setPull_list_id( Pull_List_ItemDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Pull_List_Item::pull_list_id => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}


	/** Validation */
	function validate_group_name($object = null, $value)
	{
		$value = trim($value);
		return null;
	}
	function validate_data($object = null, $value)
	{
		$value = trim($value);
		if (empty($value)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List_Item::data,
				"FIELD_EMPTY"
			);
		}
		return null;
	}
	function validate_created($object = null, $value)
	{
		if ( isset($object, $object->created) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List_Item::created,
				"IMMUTABLE"
			);
		}
		return null;
	}
	function validate_name($object = null, $value)
	{
		$value = trim($value);
		if (empty($value)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List_Item::name,
				"FIELD_EMPTY"
			);
		}
		return null;
	}
	function validate_issue($object = null, $value)
	{
		$value = trim($value);
		return null;
	}
	function validate_year($object = null, $value)
	{
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List_Item::year,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
	function validate_pull_list_id($object = null, $value)
	{
		if (isset($object->pull_list_id) === false && empty($value) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List_Item::pull_list_id,
				"FIELD_EMPTY"
			);
		}
		return null;
	}
}

?>
