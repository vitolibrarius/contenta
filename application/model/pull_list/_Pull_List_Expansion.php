<?php

namespace model\pull_list;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\pull_list\Pull_List_ExpansionDBO as Pull_List_ExpansionDBO;

/* import related objects */
use \model\Endpoint_Type as Endpoint_Type;
use \model\Endpoint_TypeDBO as Endpoint_TypeDBO;

/** Sample Creation script */
		/** PULL_LIST_EXPANSION */
/*
		$sql = "CREATE TABLE IF NOT EXISTS pull_list_expansion ( "
			. Pull_List_Expansion::id . " INTEGER PRIMARY KEY, "
			. Pull_List_Expansion::pattern . " TEXT, "
			. Pull_List_Expansion::replace . " TEXT, "
			. Pull_List_Expansion::sequence . " INTEGER, "
			. Pull_List_Expansion::created . " INTEGER, "
			. Pull_List_Expansion::endpoint_type_id . " INTEGER, "
			. "FOREIGN KEY (". Pull_List_Expansion::endpoint_type_id .") REFERENCES " . Endpoint_Type::TABLE . "(" . Endpoint_Type::id . ")"
		. ")";
		$this->sqlite_execute( "pull_list_expansion", $sql, "Create table pull_list_expansion" );

*/
abstract class _Pull_List_Expansion extends Model
{
	const TABLE = 'pull_list_expansion';
	const id = 'id';
	const pattern = 'pattern';
	const replace = 'replace';
	const sequence = 'sequence';
	const created = 'created';
	const endpoint_type_id = 'endpoint_type_id';

	public function tableName() { return Pull_List_Expansion::TABLE; }
	public function tablePK() { return Pull_List_Expansion::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Pull_List_Expansion::sequence),
			array( 'asc' => Pull_List_Expansion::pattern)
		);
	}

	public function allColumnNames()
	{
		return array(
			Pull_List_Expansion::id,
			Pull_List_Expansion::pattern,
			Pull_List_Expansion::replace,
			Pull_List_Expansion::sequence,
			Pull_List_Expansion::created,
			Pull_List_Expansion::endpoint_type_id
		);
	}

	/**
	 *	Simple fetches
	 */

	public function allForPattern($value)
	{
		return $this->allObjectsForKeyValue(Pull_List_Expansion::pattern, $value);
	}


	public function allForReplace($value)
	{
		return $this->allObjectsForKeyValue(Pull_List_Expansion::replace, $value);
	}


	public function allForSequence($value)
	{
		return $this->allObjectsForKeyValue(Pull_List_Expansion::sequence, $value);
	}




	public function allForEndpoint_type($obj)
	{
		return $this->allObjectsForFK(Pull_List_Expansion::endpoint_type_id, $obj, $this->sortOrder(), 50);
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "endpoint_type":
					return array( Pull_List_Expansion::endpoint_type_id, "id"  );
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
			if ( isset($values['endpoint_type']) ) {
				$local_endpoint_type = $values['endpoint_type'];
				if ( $local_endpoint_type instanceof Endpoint_TypeDBO) {
					$values[Pull_List_Expansion::endpoint_type_id] = $local_endpoint_type->id;
				}
				else if ( is_integer( $local_endpoint_type) ) {
					$params[Pull_List_Expansion::endpoint_type_id] = $local_endpoint_type;
				}
			}
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Pull_List_Expansion ) {
			if ( isset($values['endpoint_type']) ) {
				$local_endpoint_type = $values['endpoint_type'];
				if ( $local_endpoint_type instanceof Endpoint_TypeDBO) {
					$values[Pull_List_Expansion::endpoint_type_id] = $local_endpoint_type->id;
				}
				else if ( is_integer( $local_endpoint_type) ) {
					$params[Pull_List_Expansion::endpoint_type_id] = $values['endpoint_type'];
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
		if ( $object instanceof Pull_List_ExpansionDBO )
		{
			// does not own endpoint_type Endpoint_Type
			return parent::deleteObject($object);
		}

		return false;
	}

	public function deleteAllForEndpoint_type(Endpoint_TypeDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForEndpoint_type($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForEndpoint_type($obj);
			}
		}
		return $success;
	}

	/**
	 *	Named fetches
	 */


	/** Validation */
	function validate_pattern($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List_Expansion::pattern,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_replace($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_sequence($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List_Expansion::sequence,
				"FILTER_VALIDATE_INT"
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
				Pull_List_Expansion::created,
				"IMMUTABLE"
			);
		}
		return null;
	}
	function validate_endpoint_type_id($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List_Expansion::endpoint_type_id,
				"FIELD_EMPTY"
			);
		}

		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List_Expansion::endpoint_type_id,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
}

?>
