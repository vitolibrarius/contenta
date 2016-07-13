<?php

namespace model\media;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\media\Media_TypeDBO as Media_TypeDBO;

/* import related objects */

/** Sample Creation script */
		/** MEDIA_TYPE */
/*
		$sql = "CREATE TABLE IF NOT EXISTS media_type ( "
			. Media_Type::id . " INTEGER PRIMARY KEY, "
			. Media_Type::code . " TEXT, "
			. Media_Type::name . " TEXT "
		. ")";
		$this->sqlite_execute( "media_type", $sql, "Create table media_type" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS media_type_code on media_type (code)';
		$this->sqlite_execute( "media_type", $sql, "Index on media_type (code)" );
		$sql = 'CREATE  INDEX IF NOT EXISTS media_type_name on media_type (name)';
		$this->sqlite_execute( "media_type", $sql, "Index on media_type (name)" );
*/
abstract class _Media_Type extends Model
{
	const TABLE = 'media_type';
	const id = 'id';
	const code = 'code';
	const name = 'name';

	public function tableName() { return Media_Type::TABLE; }
	public function tablePK() { return Media_Type::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Media_Type::name)
		);
	}

	public function allColumnNames()
	{
		return array(
			Media_Type::id,
			Media_Type::code,
			Media_Type::name
		);
	}

	/**
	 *	Simple fetches
	 */

	public function objectForCode($value)
	{
		return $this->singleObjectForKeyValue(Media_Type::code, $value);
	}


	public function allForName($value)
	{
		return $this->allObjectsForKeyValue(Media_Type::name, $value);
	}




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
			if ( isset($values['code']) == false ) {
				$default_code = $this->attributeDefaultValue( null, null, Media_Type::code);
				if ( is_null( $default_code ) == false ) {
					$values['code'] = $default_code;
				}
			}
			if ( isset($values['name']) == false ) {
				$default_name = $this->attributeDefaultValue( null, null, Media_Type::name);
				if ( is_null( $default_name ) == false ) {
					$values['name'] = $default_name;
				}
			}

			// default conversion for relationships
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Media_Type ) {
		}

		return parent::updateObject($object, $values);
	}

	/**
	 *	Delete functions
	 */
	public function deleteObject( DataObject $object = null)
	{
		if ( $object instanceof Media_TypeDBO )
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
				Media_Type::code,
				Media_Type::name
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesMap() {
		return array(
			Media_Type::code => Model::TEXT_TYPE,
			Media_Type::name => Model::TEXT_TYPE
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

	/**
	 * Validation
	 */
	function validate_code($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Media_Type::code,
				"FIELD_EMPTY"
			);
		}

		// make sure Code is unique
		$existing = $this->objectForCode($value);
		if ( $existing != false && ( is_null($object) || $existing->id != $object->id)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Media_Type::code,
				"UNIQUE_FIELD_VALUE"
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
				Media_Type::name,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
}

?>
