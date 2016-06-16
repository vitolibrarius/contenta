<?php

namespace model\pull_list;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\pull_list\Pull_List_GroupDBO as Pull_List_GroupDBO;

/* import related objects */
use \model\pull_list\Pull_List_Item as Pull_List_Item;
use \model\pull_list\Pull_List_ItemDBO as Pull_List_ItemDBO;

/** Sample Creation script */
		/** PULL_LIST_GROUP */
/*
		$sql = "CREATE TABLE IF NOT EXISTS pull_list_group ( "
			. Pull_List_Group::id . " INTEGER PRIMARY KEY, "
			. Pull_List_Group::name . " TEXT, "
			. Pull_List_Group::data . " TEXT, "
			. Pull_List_Group::created . " INTEGER "
		. ")";
		$this->sqlite_execute( "pull_list_group", $sql, "Create table pull_list_group" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS pull_list_group_data on pull_list_group (data)';
		$this->sqlite_execute( "pull_list_group", $sql, "Index on pull_list_group (data)" );
		$sql = 'CREATE  INDEX IF NOT EXISTS pull_list_group_name on pull_list_group (name)';
		$this->sqlite_execute( "pull_list_group", $sql, "Index on pull_list_group (name)" );
*/
abstract class _Pull_List_Group extends Model
{
	const TABLE = 'pull_list_group';
	const id = 'id';
	const name = 'name';
	const data = 'data';
	const created = 'created';

	public function tableName() { return Pull_List_Group::TABLE; }
	public function tablePK() { return Pull_List_Group::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Pull_List_Group::name)
		);
	}

	public function allColumnNames()
	{
		return array(
			Pull_List_Group::id,
			Pull_List_Group::name,
			Pull_List_Group::data,
			Pull_List_Group::created
		);
	}

	/**
	 *	Simple fetches
	 */

	public function allForName($value)
	{
		return $this->allObjectsForKeyValue(Pull_List_Group::name, $value);
	}

	public function allLikeName($value)
	{
		return SQL::Select( $this )
			->where( Qualifier::Like( Pull_List_Group::name, $value, SQL::SQL_LIKE_AFTER ))
			->orderBy( $this->sortOrder() )
			->limit( 50 )
			->fetchAll();
	}

	public function objectForData($value)
	{
		return $this->singleObjectForKeyValue(Pull_List_Group::data, $value);
	}





	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "pull_list_item":
					return array( Pull_List_Group::id, "pull_list_group_id"  );
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
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Pull_List_Group ) {
		}

		return parent::updateObject($object, $values);
	}

	/**
	 *	Delete functions
	 */
	public function deleteObject( DataObject $object = null)
	{
		if ( $object instanceof Pull_List_GroupDBO )
		{
			// does not own Pull_List_Item
			return parent::deleteObject($object);
		}

		return false;
	}


	/**
	 *	Named fetches
	 */

	/** Set attributes */
	public function setName( Pull_List_GroupDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Pull_List_Group::name => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setData( Pull_List_GroupDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Pull_List_Group::data => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setCreated( Pull_List_GroupDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Pull_List_Group::created => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}


	/** Validation */
	function validate_name($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List_Group::name,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_data($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List_Group::data,
				"FIELD_EMPTY"
			);
		}

		// make sure Data is unique
		$existing = $this->objectForData($value);
		if ( $existing != false && ( is_null($object) || $existing->id != $object->id)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List_Group::data,
				"UNIQUE_FIELD_VALUE"
			);
		}
		return null;
	}
	function validate_created($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// created date is not changeable
		if ( isset($object, $object->created) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List_Group::created,
				"IMMUTABLE"
			);
		}
		return null;
	}
}

?>
