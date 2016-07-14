<?php

namespace model\pull_list;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\pull_list\Pull_List_ExclusionDBO as Pull_List_ExclusionDBO;

/* import related objects */
use \model\network\Endpoint_Type as Endpoint_Type;
use \model\network\Endpoint_TypeDBO as Endpoint_TypeDBO;

/** Sample Creation script */
		/** PULL_LIST_EXCL */
/*
		$sql = "CREATE TABLE IF NOT EXISTS pull_list_excl ( "
			. Pull_List_Exclusion::id . " INTEGER PRIMARY KEY, "
			. Pull_List_Exclusion::pattern . " TEXT, "
			. Pull_List_Exclusion::type . " TEXT, "
			. Pull_List_Exclusion::created . " INTEGER, "
			. Pull_List_Exclusion::endpoint_type_code . " TEXT, "
			. "FOREIGN KEY (". Pull_List_Exclusion::endpoint_type_code .") REFERENCES " . Endpoint_Type::TABLE . "(" . Endpoint_Type::code . ")"
		. ")";
		$this->sqlite_execute( "pull_list_excl", $sql, "Create table pull_list_excl" );

*/
abstract class _Pull_List_Exclusion extends Model
{
	const TABLE = 'pull_list_excl';
	const id = 'id';
	const pattern = 'pattern';
	const type = 'type';
	const created = 'created';
	const endpoint_type_code = 'endpoint_type_code';

	public function tableName() { return Pull_List_Exclusion::TABLE; }
	public function tablePK() { return Pull_List_Exclusion::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Pull_List_Exclusion::pattern)
		);
	}

	public function allColumnNames()
	{
		return array(
			Pull_List_Exclusion::id,
			Pull_List_Exclusion::pattern,
			Pull_List_Exclusion::type,
			Pull_List_Exclusion::created,
			Pull_List_Exclusion::endpoint_type_code
		);
	}

	/**
	 *	Simple fetches
	 */

	public function allForPattern($value)
	{
		return $this->allObjectsForKeyValue(Pull_List_Exclusion::pattern, $value);
	}


	public function allForType($value)
	{
		return $this->allObjectsForKeyValue(Pull_List_Exclusion::type, $value);
	}



	public function allForEndpoint_type_code($value)
	{
		return $this->allObjectsForKeyValue(Pull_List_Exclusion::endpoint_type_code, $value);
	}



	/**
	 * Simple relationship fetches
	 */
	public function allForEndpoint_type($obj)
	{
		return $this->allObjectsForFK(Pull_List_Exclusion::endpoint_type_code, $obj, $this->sortOrder(), 50);
	}

	public function countForEndpoint_type($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Pull_List_Exclusion::endpoint_type_code, $obj );
		}
		return false;
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "endpoint_type":
					return array( Pull_List_Exclusion::endpoint_type_code, "code"  );
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
			if ( isset($values['pattern']) == false ) {
				$default_pattern = $this->attributeDefaultValue( null, null, Pull_List_Exclusion::pattern);
				if ( is_null( $default_pattern ) == false ) {
					$values['pattern'] = $default_pattern;
				}
			}
			if ( isset($values['type']) == false ) {
				$default_type = $this->attributeDefaultValue( null, null, Pull_List_Exclusion::type);
				if ( is_null( $default_type ) == false ) {
					$values['type'] = $default_type;
				}
			}
			if ( isset($values['created']) == false ) {
				$default_created = $this->attributeDefaultValue( null, null, Pull_List_Exclusion::created);
				if ( is_null( $default_created ) == false ) {
					$values['created'] = $default_created;
				}
			}

			// default conversion for relationships
			if ( isset($values['endpoint_type']) ) {
				$local_endpoint_type = $values['endpoint_type'];
				if ( $local_endpoint_type instanceof Endpoint_TypeDBO) {
					$values[Pull_List_Exclusion::endpoint_type_code] = $local_endpoint_type->code;
				}
				else if ( is_string( $local_endpoint_type) ) {
					$params[Pull_List_Exclusion::endpoint_type_code] = $local_endpoint_type;
				}
			}
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Pull_List_Exclusion ) {
			if ( isset($values['endpoint_type']) ) {
				$local_endpoint_type = $values['endpoint_type'];
				if ( $local_endpoint_type instanceof Endpoint_TypeDBO) {
					$values[Pull_List_Exclusion::endpoint_type_code] = $local_endpoint_type->code;
				}
				else if ( is_string( $local_endpoint_type) ) {
					$params[Pull_List_Exclusion::endpoint_type_code] = $values['endpoint_type'];
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
		if ( $object instanceof Pull_List_ExclusionDBO )
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
	 * Named fetches
	 */
	public function objectsForTypeAndEndpointType( $exclType, $endType )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		$qualifiers[] = Qualifier::Equals( 'type', $exclType);
		$qualifiers[] = Qualifier::Equals( 'endpoint_type_code', $endType);

		if ( count($qualifiers) > 0 ) {
			$select->where( Qualifier::Combine( 'AND', $qualifiers ));
		}

		$result = $select->fetchAll();
		return $result;
	}

	public function objectsForPatternTypeAndEndpointType( $pattern, $exclType, $endType )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		$qualifiers[] = Qualifier::Equals( 'pattern', $pattern);
		$qualifiers[] = Qualifier::Equals( 'type', $exclType);
		$qualifiers[] = Qualifier::Equals( 'endpoint_type_code', $endType);

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
				Pull_List_Exclusion::pattern
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesMap() {
		return array(
			Pull_List_Exclusion::pattern => Model::TEXT_TYPE,
			Pull_List_Exclusion::type => Model::TEXT_TYPE,
			Pull_List_Exclusion::created => Model::DATE_TYPE,
			Pull_List_Exclusion::endpoint_type_code => Model::TO_ONE_TYPE
		);
	}

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) === false || is_null($object) == true) {
			switch ($attr) {
				case Pull_List_Exclusion::type:
					return 'item';
			}
		}
		return parent::attributeDefaultValue($object, $type, $attr);
	}

	/**
	 * Validation
	 */
	function validate_pattern($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List_Exclusion::pattern,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_type($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
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
				Pull_List_Exclusion::created,
				"IMMUTABLE"
			);
		}
		return null;
	}
	function validate_endpoint_type_code($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List_Exclusion::endpoint_type_code,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
}

?>
