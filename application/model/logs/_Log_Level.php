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

/** Sample Creation script */
		/** LOG_LEVEL */
/*
		$sql = "CREATE TABLE IF NOT EXISTS log_level ( "
			. Log_Level::id . " INTEGER PRIMARY KEY, "
			. Log_Level::code . " TEXT, "
			. Log_Level::name . " TEXT "
		. ")";
		$this->sqlite_execute( "log_level", $sql, "Create table log_level" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS log_level_code on log_level (code)';
		$this->sqlite_execute( "log_level", $sql, "Index on log_level (code)" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS log_level_name on log_level (name)';
		$this->sqlite_execute( "log_level", $sql, "Index on log_level (name)" );
*/
abstract class _Log_Level extends Model
{
	const TABLE = 'log_level';
	const id = 'id';
	const code = 'code';
	const name = 'name';

	public function tableName() { return Log_Level::TABLE; }
	public function tablePK() { return Log_Level::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Log_Level::name)
		);
	}

	public function allColumnNames()
	{
		return array(
			Log_Level::id,
			Log_Level::code,
			Log_Level::name
		);
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
				$default_code = $this->attributeDefaultValue( null, null, Log_Level::code);
				if ( is_null( $default_code ) == false ) {
					$values['code'] = $default_code;
				}
			}
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
				Log_Level::code
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesMap() {
		return array(
			Log_Level::code => Model::TEXT_TYPE,
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

	/**
	 * Validation
	 */
	function validate_code($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Log_Level::code,
				"FIELD_EMPTY"
			);
		}

		// make sure Code is unique
		$existing = $this->objectForCode($value);
		if ( $existing != false && ( is_null($object) || $existing->id != $object->id)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Log_Level::code,
				"UNIQUE_FIELD_VALUE"
			);
		}
		return null;
	}
	function validate_name($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
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
