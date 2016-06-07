<?php

namespace model\logs;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

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
		if ( $object instanceof Log_Level )
		{
			return parent::deleteObject($object);
		}

		return false;
	}


	/**
	 *	Named fetches
	 */

	/** Set attributes */
	public function setCode( Log_LevelDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Log_Level::code => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setName( Log_LevelDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Log_Level::name => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}


	/** Validation */
	function validate_code($object = null, $value)
	{
		$value = trim($value);
		if (empty($value)) {
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
		$value = trim($value);
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
