<?php

namespace model\pull_list;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\pull_list\Pull_List_ItemDBO as Pull_List_ItemDBO;

/* import related objects */
use \model\pull_list\Pull_List_Group as Pull_List_Group;
use \model\pull_list\Pull_List_GroupDBO as Pull_List_GroupDBO;
use \model\pull_list\Pull_List as Pull_List;
use \model\pull_list\Pull_ListDBO as Pull_ListDBO;

/** Generated class, do not edit.
 */
abstract class _Pull_List_Item extends Model
{
	const TABLE = 'pull_list_item';

	// attribute keys
	const id = 'id';
	const data = 'data';
	const created = 'created';
	const search_name = 'search_name';
	const name = 'name';
	const issue = 'issue';
	const year = 'year';
	const pull_list_id = 'pull_list_id';
	const pull_list_group_id = 'pull_list_group_id';

	// relationship keys
	const pull_list_group = 'pull_list_group';
	const pull_list = 'pull_list';

	public function modelName()
	{
		return "Pull_List_Item";
	}

	public function dboName()
	{
		return '\model\pull_list\Pull_List_ItemDBO';
	}

	public function tableName() { return Pull_List_Item::TABLE; }
	public function tablePK() { return Pull_List_Item::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Pull_List_Item::pull_list_group_id),
			array( 'asc' => Pull_List_Item::search_name),
			array( 'asc' => Pull_List_Item::issue)
		);
	}

	public function allColumnNames()
	{
		return array(
			Pull_List_Item::id,
			Pull_List_Item::data,
			Pull_List_Item::created,
			Pull_List_Item::search_name,
			Pull_List_Item::name,
			Pull_List_Item::issue,
			Pull_List_Item::year,
			Pull_List_Item::pull_list_id,
			Pull_List_Item::pull_list_group_id
		);
	}

	public function allAttributes()
	{
		return array(
			Pull_List_Item::data,
			Pull_List_Item::created,
			Pull_List_Item::search_name,
			Pull_List_Item::name,
			Pull_List_Item::issue,
			Pull_List_Item::year,
		);
	}

	public function allForeignKeys()
	{
		return array(Pull_List_Item::pull_list_group_id,
			Pull_List_Item::pull_list_id);
	}

	public function allRelationshipNames()
	{
		return array(
			Pull_List_Item::pull_list_group,
			Pull_List_Item::pull_list
		);
	}

	public function searchQualifiers( array $query )
	{
		$qualifiers = array();
		if ( is_array($query) ) {
			foreach( $query as $attr => $value ) {
				switch ($attr) {
			// Pull_List_Item::id == INTEGER

			// Pull_List_Item::data == TEXT
				case Pull_List_Item::data:
					if (strlen($value) > 0) {
						$qualifiers[Pull_List_Item::data] = Qualifier::Equals( Pull_List_Item::data, $value );
					}
					break;

			// Pull_List_Item::created == DATE

			// Pull_List_Item::search_name == TEXT
				case Pull_List_Item::search_name:
					if (strlen($value) > 0) {
						$qualifiers[Pull_List_Item::search_name] = Qualifier::Equals( Pull_List_Item::search_name, $value );
					}
					break;

			// Pull_List_Item::name == TEXT
				case Pull_List_Item::name:
					if (strlen($value) > 0) {
						$qualifiers[Pull_List_Item::name] = Qualifier::Equals( Pull_List_Item::name, $value );
					}
					break;

			// Pull_List_Item::issue == TEXT
				case Pull_List_Item::issue:
					if (strlen($value) > 0) {
						$qualifiers[Pull_List_Item::issue] = Qualifier::Equals( Pull_List_Item::issue, $value );
					}
					break;

			// Pull_List_Item::year == INTEGER
				case Pull_List_Item::year:
					if ( intval($value) > 0 ) {
						$qualifiers[Pull_List_Item::year] = Qualifier::Equals( Pull_List_Item::year, intval($value) );
					}
					break;

			// Pull_List_Item::pull_list_id == INTEGER
				case Pull_List_Item::pull_list_id:
					if ( intval($value) > 0 ) {
						$qualifiers[Pull_List_Item::pull_list_id] = Qualifier::Equals( Pull_List_Item::pull_list_id, intval($value) );
					}
					break;

			// Pull_List_Item::pull_list_group_id == INTEGER
				case Pull_List_Item::pull_list_group_id:
					if ( intval($value) > 0 ) {
						$qualifiers[Pull_List_Item::pull_list_group_id] = Qualifier::Equals( Pull_List_Item::pull_list_group_id, intval($value) );
					}
					break;

				default:
					/* no type specified for Pull_List_Item::pull_list_group_id */
					break;
				}
			}
		}
		return $qualifiers;
	}

	/**
	 *	Simple fetches
	 */

	public function allForData($value)
	{
		return $this->allObjectsForKeyValue(Pull_List_Item::data, $value);
	}



	public function allForSearch_name($value)
	{
		return $this->allObjectsForKeyValue(Pull_List_Item::search_name, $value);
	}


	public function allForName($value)
	{
		return $this->allObjectsForKeyValue(Pull_List_Item::name, $value);
	}


	public function allForIssue($value)
	{
		return $this->allObjectsForKeyValue(Pull_List_Item::issue, $value);
	}


	public function allForYear($value)
	{
		return $this->allObjectsForKeyValue(Pull_List_Item::year, $value);
	}




	/**
	 * Simple relationship fetches
	 */
	public function allForPull_list_group($obj)
	{
		return $this->allObjectsForFK(Pull_List_Item::pull_list_group_id, $obj, $this->sortOrder(), 50);
	}

	public function countForPull_list_group($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Pull_List_Item::pull_list_group_id, $obj );
		}
		return false;
	}
	public function allForPull_list($obj)
	{
		return $this->allObjectsForFK(Pull_List_Item::pull_list_id, $obj, $this->sortOrder(), 50);
	}

	public function countForPull_list($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Pull_List_Item::pull_list_id, $obj );
		}
		return false;
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "pull_list_group":
					return array( Pull_List_Item::pull_list_group_id, "id"  );
					break;
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

			// default values for attributes
			if ( isset($values['data']) == false ) {
				$default_data = $this->attributeDefaultValue( null, null, Pull_List_Item::data);
				if ( is_null( $default_data ) == false ) {
					$values['data'] = $default_data;
				}
			}
			if ( isset($values['created']) == false ) {
				$default_created = $this->attributeDefaultValue( null, null, Pull_List_Item::created);
				if ( is_null( $default_created ) == false ) {
					$values['created'] = $default_created;
				}
			}
			if ( isset($values['search_name']) == false ) {
				$default_search_name = $this->attributeDefaultValue( null, null, Pull_List_Item::search_name);
				if ( is_null( $default_search_name ) == false ) {
					$values['search_name'] = $default_search_name;
				}
			}
			if ( isset($values['name']) == false ) {
				$default_name = $this->attributeDefaultValue( null, null, Pull_List_Item::name);
				if ( is_null( $default_name ) == false ) {
					$values['name'] = $default_name;
				}
			}
			if ( isset($values['issue']) == false ) {
				$default_issue = $this->attributeDefaultValue( null, null, Pull_List_Item::issue);
				if ( is_null( $default_issue ) == false ) {
					$values['issue'] = $default_issue;
				}
			}
			if ( isset($values['year']) == false ) {
				$default_year = $this->attributeDefaultValue( null, null, Pull_List_Item::year);
				if ( is_null( $default_year ) == false ) {
					$values['year'] = $default_year;
				}
			}

			// default conversion for relationships
			if ( isset($values['pull_list_group']) ) {
				$local_pull_list_group = $values['pull_list_group'];
				if ( $local_pull_list_group instanceof Pull_List_GroupDBO) {
					$values[Pull_List_Item::pull_list_group_id] = $local_pull_list_group->id;
				}
				else if ( is_integer( $local_pull_list_group) ) {
					$params[Pull_List_Item::pull_list_group_id] = $local_pull_list_group;
				}
			}
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
			if ( isset($values['pull_list_group']) ) {
				$local_pull_list_group = $values['pull_list_group'];
				if ( $local_pull_list_group instanceof Pull_List_GroupDBO) {
					$values[Pull_List_Item::pull_list_group_id] = $local_pull_list_group->id;
				}
				else if ( is_integer( $local_pull_list_group) ) {
					$params[Pull_List_Item::pull_list_group_id] = $values['pull_list_group'];
				}
			}
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
		if ( $object instanceof Pull_List_ItemDBO )
		{
			// does not own pull_list_group Pull_List_Group
			// does not own pull_list Pull_List
			return parent::deleteObject($object);
		}

		return false;
	}

	public function deleteAllForPull_list_group(Pull_List_GroupDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForPull_list_group($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForPull_list_group($obj);
			}
		}
		return $success;
	}
	public function deleteAllForPull_list(Pull_ListDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForPull_list($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForPull_list($obj);
			}
		}
		return $success;
	}

	/**
	 * Named fetches
	 */
	public function objectsForNameIssueYear( $name, $issue, $year )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		if ( isset($name)) {
			$qualifiers[] = Qualifier::Equals( 'name', $name);
		}
		if ( isset($issue)) {
			$qualifiers[] = Qualifier::Equals( 'issue', $issue);
		}
		if ( isset($year)) {
			$qualifiers[] = Qualifier::Equals( 'year', $year);
		}

		if ( count($qualifiers) > 0 ) {
			$select->where( Qualifier::Combine( 'AND', $qualifiers ));
		}

		$result = $select->fetchAll();
		return $result;
	}

	public function objectsForPullList_NameIssueYear(Pull_ListDBO $pull_list, $name, $issue, $year )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		$qualifiers[] = Qualifier::FK( 'pull_list_id', $pull_list);
		if ( isset($name)) {
			$qualifiers[] = Qualifier::Equals( 'name', $name);
		}
		if ( isset($issue)) {
			$qualifiers[] = Qualifier::Equals( 'issue', $issue);
		}
		if ( isset($year)) {
			$qualifiers[] = Qualifier::Equals( 'year', $year);
		}

		if ( count($qualifiers) > 0 ) {
			$select->where( Qualifier::Combine( 'AND', $qualifiers ));
		}

		$result = $select->fetchAll();
		return $result;
	}

	public function objectsForPullListId_NameIssueYear( $pull_list_id, $name, $issue, $year )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		if ( isset($pull_list_id)) {
			$qualifiers[] = Qualifier::Equals( 'pull_list_id', $pull_list_id);
		}
		if ( isset($name)) {
			$qualifiers[] = Qualifier::Equals( 'name', $name);
		}
		if ( isset($issue)) {
			$qualifiers[] = Qualifier::Equals( 'issue', $issue);
		}
		if ( isset($year)) {
			$qualifiers[] = Qualifier::Equals( 'year', $year);
		}

		if ( count($qualifiers) > 0 ) {
			$select->where( Qualifier::Combine( 'AND', $qualifiers ));
		}

		$result = $select->fetchAll();
		return $result;
	}


	/**
	 * Attribute editing
	 */
	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Pull_List_Item::data,
				Pull_List_Item::search_name,
				Pull_List_Item::name,
				Pull_List_Item::pull_list_group_id
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesMap() {
		return array(
			Pull_List_Item::data => Model::TEXT_TYPE,
			Pull_List_Item::created => Model::DATE_TYPE,
			Pull_List_Item::search_name => Model::TEXT_TYPE,
			Pull_List_Item::name => Model::TEXT_TYPE,
			Pull_List_Item::issue => Model::TEXT_TYPE,
			Pull_List_Item::year => Model::INT_TYPE,
			Pull_List_Item::pull_list_id => Model::TO_ONE_TYPE,
			Pull_List_Item::pull_list_group_id => Model::TO_ONE_TYPE
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
				case Pull_List_Item::pull_list_group_id:
					$pull_list_group_model = Model::Named('Pull_List_Group');
					$fkObject = $pull_list_group_model->objectForId( $value );
					break;
				case Pull_List_Item::pull_list_id:
					$pull_list_model = Model::Named('Pull_List');
					$fkObject = $pull_list_model->objectForId( $value );
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
	function validate_data($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
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
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// created date is not changeable
		if ( isset($object, $object->created) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List_Item::created,
				"IMMUTABLE"
			);
		}
		return null;
	}
	function validate_search_name($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List_Item::search_name,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_name($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
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
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_year($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
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
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List_Item::pull_list_id,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_pull_list_group_id($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List_Item::pull_list_group_id,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
}

?>
