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

/** Generated class, do not edit.
 */
abstract class _Pull_List_Group extends Model
{
	const TABLE = 'pull_list_group';

	// attribute keys
	const id = 'id';
	const name = 'name';
	const data = 'data';
	const created = 'created';

	// relationship keys
	const pull_list_items = 'pull_list_items';

	public function modelName()
	{
		return "Pull_List_Group";
	}

	public function dboName()
	{
		return '\model\pull_list\Pull_List_GroupDBO';
	}

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

	public function allAttributes()
	{
		return array(
			Pull_List_Group::name,
			Pull_List_Group::data,
			Pull_List_Group::created
		);
	}

	public function allForeignKeys()
	{
		return array();
	}

	public function allRelationshipNames()
	{
		return array(
			Pull_List_Group::pull_list_items
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




	/**
	 * Simple relationship fetches
	 */

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

			// default values for attributes
			if ( isset($values['name']) == false ) {
				$default_name = $this->attributeDefaultValue( null, null, Pull_List_Group::name);
				if ( is_null( $default_name ) == false ) {
					$values['name'] = $default_name;
				}
			}
			if ( isset($values['data']) == false ) {
				$default_data = $this->attributeDefaultValue( null, null, Pull_List_Group::data);
				if ( is_null( $default_data ) == false ) {
					$values['data'] = $default_data;
				}
			}
			if ( isset($values['created']) == false ) {
				$default_created = $this->attributeDefaultValue( null, null, Pull_List_Group::created);
				if ( is_null( $default_created ) == false ) {
					$values['created'] = $default_created;
				}
			}

			// default conversion for relationships
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
			// does not own pull_list_items Pull_List_Item
			return parent::deleteObject($object);
		}

		return false;
	}


	/**
	 * Named fetches
	 */

	/**
	 * Attribute editing
	 */
	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Pull_List_Group::name,
				Pull_List_Group::data
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesMap() {
		return array(
			Pull_List_Group::name => Model::TEXT_TYPE,
			Pull_List_Group::data => Model::TEXT_TYPE,
			Pull_List_Group::created => Model::DATE_TYPE
		);
	}

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) === false || is_null($object) == true) {
			switch ($attr) {
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
				default:
					break;
			}
		}
		return $fkObject;
	}

	/**
	 * Validation
	 */
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
