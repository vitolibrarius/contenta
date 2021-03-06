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
use \model\network\Endpoint_Type as Endpoint_Type;
use \model\network\Endpoint_TypeDBO as Endpoint_TypeDBO;

/** Generated class, do not edit.
 */
abstract class _Pull_List_Expansion extends Model
{
	const TABLE = 'pull_list_expansion';

	// attribute keys
	const id = 'id';
	const pattern = 'pattern';
	const replace = 'replace';
	const sequence = 'sequence';
	const created = 'created';
	const endpoint_type_code = 'endpoint_type_code';

	// relationship keys
	const endpoint_type = 'endpoint_type';

	public function modelName()
	{
		return "Pull_List_Expansion";
	}

	public function dboName()
	{
		return '\model\pull_list\Pull_List_ExpansionDBO';
	}

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
			Pull_List_Expansion::endpoint_type_code
		);
	}

	public function allAttributes()
	{
		return array(
			Pull_List_Expansion::pattern,
			Pull_List_Expansion::replace,
			Pull_List_Expansion::sequence,
			Pull_List_Expansion::created,
		);
	}

	public function allForeignKeys()
	{
		return array(Pull_List_Expansion::endpoint_type_code);
	}

	public function allRelationshipNames()
	{
		return array(
			Pull_List_Expansion::endpoint_type
		);
	}

	public function attributes()
	{
		return array(
			Pull_List_Expansion::pattern => array('length' => 256,'type' => 'TEXT'),
			Pull_List_Expansion::replace => array('length' => 256,'type' => 'TEXT'),
			Pull_List_Expansion::sequence => array('type' => 'INTEGER'),
			Pull_List_Expansion::created => array('type' => 'DATE'),
		);
	}

	public function relationships()
	{
		return array(
			Pull_List_Expansion::endpoint_type => array(
				'destination' => 'Endpoint_Type',
				'ownsDestination' => false,
				'isMandatory' => true,
				'isToMany' => false,
				'joins' => array( 'endpoint_type_code' => 'code')
			)
		);
	}

	public function searchQualifiers( array $query )
	{
		$qualifiers = array();
		if ( is_array($query) ) {
			foreach( $query as $attr => $value ) {
				switch ($attr) {
			// Pull_List_Expansion::id == INTEGER

			// Pull_List_Expansion::pattern == TEXT
				case Pull_List_Expansion::pattern:
					if (strlen($value) > 0) {
						$qualifiers[Pull_List_Expansion::pattern] = Qualifier::Equals( Pull_List_Expansion::pattern, $value );
					}
					break;

			// Pull_List_Expansion::replace == TEXT
				case Pull_List_Expansion::replace:
					if (strlen($value) > 0) {
						$qualifiers[Pull_List_Expansion::replace] = Qualifier::Equals( Pull_List_Expansion::replace, $value );
					}
					break;

			// Pull_List_Expansion::sequence == INTEGER
				case Pull_List_Expansion::sequence:
					if ( intval($value) > 0 ) {
						$qualifiers[Pull_List_Expansion::sequence] = Qualifier::Equals( Pull_List_Expansion::sequence, intval($value) );
					}
					break;

			// Pull_List_Expansion::created == DATE

			// Pull_List_Expansion::endpoint_type_code == TEXT
				case Pull_List_Expansion::endpoint_type_code:
					if (strlen($value) > 0) {
						$qualifiers[Pull_List_Expansion::endpoint_type_code] = Qualifier::Equals( Pull_List_Expansion::endpoint_type_code, $value );
					}
					break;

				default:
					/* no type specified for Pull_List_Expansion::endpoint_type_code */
					break;
				}
			}
		}
		return $qualifiers;
	}

	/**
	 *	Simple fetches
	 */

	public function allForPattern($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Pull_List_Expansion::pattern, $value, null, $limit);
	}


	public function allForReplace($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Pull_List_Expansion::replace, $value, null, $limit);
	}


	public function allForSequence($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Pull_List_Expansion::sequence, $value, null, $limit);
	}


	public function allForEndpoint_type_code($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Pull_List_Expansion::endpoint_type_code, $value, null, $limit);
	}



	/**
	 * Simple relationship fetches
	 */
	public function allForEndpoint_type($obj, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForFK(Pull_List_Expansion::endpoint_type_code, $obj, $this->sortOrder(), $limit);
	}

	public function countForEndpoint_type($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Pull_List_Expansion::endpoint_type_code, $obj );
		}
		return false;
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "endpoint_type":
					return array( Pull_List_Expansion::endpoint_type_code, "code"  );
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
				$default_pattern = $this->attributeDefaultValue( null, null, Pull_List_Expansion::pattern);
				if ( is_null( $default_pattern ) == false ) {
					$values['pattern'] = $default_pattern;
				}
			}
			if ( isset($values['replace']) == false ) {
				$default_replace = $this->attributeDefaultValue( null, null, Pull_List_Expansion::replace);
				if ( is_null( $default_replace ) == false ) {
					$values['replace'] = $default_replace;
				}
			}
			if ( isset($values['sequence']) == false ) {
				$default_sequence = $this->attributeDefaultValue( null, null, Pull_List_Expansion::sequence);
				if ( is_null( $default_sequence ) == false ) {
					$values['sequence'] = $default_sequence;
				}
			}
			if ( isset($values['created']) == false ) {
				$default_created = $this->attributeDefaultValue( null, null, Pull_List_Expansion::created);
				if ( is_null( $default_created ) == false ) {
					$values['created'] = $default_created;
				}
			}

			// default conversion for relationships
			if ( isset($values['endpoint_type']) ) {
				$local_endpoint_type = $values['endpoint_type'];
				if ( $local_endpoint_type instanceof Endpoint_TypeDBO) {
					$values[Pull_List_Expansion::endpoint_type_code] = $local_endpoint_type->code;
				}
				else if ( is_string( $local_endpoint_type) ) {
					$params[Pull_List_Expansion::endpoint_type_code] = $local_endpoint_type;
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
					$values[Pull_List_Expansion::endpoint_type_code] = $local_endpoint_type->code;
				}
				else if ( is_string( $local_endpoint_type) ) {
					$params[Pull_List_Expansion::endpoint_type_code] = $values['endpoint_type'];
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
	 * Named fetches
	 */

	/**
	 * Attribute editing
	 */
	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Pull_List_Expansion::pattern
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesMap() {
		return array(
			Pull_List_Expansion::pattern => Model::TEXT_TYPE,
			Pull_List_Expansion::replace => Model::TEXT_TYPE,
			Pull_List_Expansion::sequence => Model::INT_TYPE,
			Pull_List_Expansion::created => Model::DATE_TYPE,
			Pull_List_Expansion::endpoint_type_code => Model::TO_ONE_TYPE
		);
	}

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) === false || is_null($object) == true) {
			switch ($attr) {
				case Pull_List_Expansion::sequence:
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
				case Pull_List_Expansion::endpoint_type_code:
					$endpoint_type_model = Model::Named('Endpoint_Type');
					$fkObject = $endpoint_type_model->objectForId( $value );
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
	function validate_endpoint_type_code($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List_Expansion::endpoint_type_code,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
}

?>
