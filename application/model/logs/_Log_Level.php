<?php

namespace model\logs;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\logs\Log_LevelDBO as Log_LevelDBO;

/* import related objects */

/** Generated class, do not edit.
 */
abstract class _Log_Level extends Model
{
	const TABLE = 'log_level';

	// attribute keys
	const code = 'code';
	const name = 'name';

	// relationship keys

	public function modelName()
	{
		return "Log_Level";
	}

	public function dboName()
	{
		return '\model\logs\Log_LevelDBO';
	}

	public function tableName() { return Log_Level::TABLE; }
	public function tablePK() { return Log_Level::code; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Log_Level::name)
		);
	}

	public function allColumnNames()
	{
		return array(
			Log_Level::code,
			Log_Level::name
		);
	}

	public function allAttributes()
	{
		return array(
			Log_Level::name
		);
	}

	public function allForeignKeys()
	{
		return array();
	}

	public function allRelationshipNames()
	{
		return array(
		);
	}

	public function attributes()
	{
		return array(
			Log_Level::name => array('length' => 256,'type' => 'TEXT')
		);
	}

	public function relationships()
	{
		return array(
		);
	}

	public function searchQualifiers( array $query )
	{
		$qualifiers = array();
		if ( is_array($query) ) {
			foreach( $query as $attr => $value ) {
				switch ($attr) {
			// Log_Level::code == TEXT
				case Log_Level::code:
					if (strlen($value) > 0) {
						$qualifiers[Log_Level::code] = Qualifier::Equals( Log_Level::code, $value );
					}
					break;

			// Log_Level::name == TEXT
				case Log_Level::name:
					if (strlen($value) > 0) {
						$qualifiers[Log_Level::name] = Qualifier::Equals( Log_Level::name, $value );
					}
					break;

				default:
					/* no type specified for Log_Level::name */
					break;
				}
			}
		}
		return $qualifiers;
	}

	/**
	 *	Simple fetches
	 */
	public function objectForCode($value)
	{
		return $this->singleObjectForKeyValue(Log_Level::code, $value);
	}


	public function objectForName($value)
	{
		return $this->singleObjectForKeyValue(Log_Level::name, $value);
	}



	/**
	 * Simple relationship fetches
	 */

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
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
				$default_name = $this->attributeDefaultValue( null, null, Log_Level::name);
				if ( is_null( $default_name ) == false ) {
					$values['name'] = $default_name;
				}
			}

			// default conversion for relationships
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Log_Level ) {
		}

		return parent::updateObject($object, $values);
	}

	/**
	 *	Delete functions
	 */
	public function deleteObject( DataObject $object = null)
	{
		if ( $object instanceof Log_LevelDBO )
		{
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
				Log_Level::name
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesMap() {
		return array(
			Log_Level::name => Model::TEXT_TYPE
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
				Log_Level::name,
				"FIELD_EMPTY"
			);
		}

		// make sure Name is unique
		$existing = $this->objectForName($value);
		if ( $existing != false && ( is_null($object) || $existing->id != $object->id)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Log_Level::name,
				"UNIQUE_FIELD_VALUE"
			);
		}
		return null;
	}
}

?>
