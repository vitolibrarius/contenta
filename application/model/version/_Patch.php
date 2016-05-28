<?php

namespace model\version;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \model\version\PatchDBO as PatchDBO;

/* import related objects */
use \model\version\Version as Version;
use \model\version\VersionDBO as VersionDBO;

/** Sample Creation script */
		/** PATCH */
/*
		$sql = "CREATE TABLE IF NOT EXISTS patch ( "
			. Patch::id . " INTEGER PRIMARY KEY, "
			. Patch::name . " TEXT, "
			. Patch::created . " INTEGER, "
			. Patch::version_id . " INTEGER, "
			. "FOREIGN KEY (". Patch::version_id .") REFERENCES " . Version::TABLE . "(" . Version::id . ")"
		. ")";
		$this->sqlite_execute( "patch", $sql, "Create table patch" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS patch_name on patch (name)';
		$this->sqlite_execute( "patch", $sql, "Index on patch (name)" );
*/
abstract class _Patch extends Model
{
	const TABLE = 'patch';
	const id = 'id';
	const name = 'name';
	const created = 'created';
	const version_id = 'version_id';

	public function tableName() { return Patch::TABLE; }
	public function tablePK() { return Patch::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Patch::name)
		);
	}

	public function allColumnNames()
	{
		return array(
			Patch::id,
			Patch::name,
			Patch::created,
			Patch::version_id
		);
	}

	/**
	 *	Simple fetches
	 */
	public function objectForName($value)
	{
		return $this->singleObjectForKeyValue(Patch::name, $value);
	}

	public function allLikeName($value)
	{
		return SQL::Select( $this )
			->where( Qualifier::Like( Patch::name, $value, SQL::SQL_LIKE_AFTER ))
			->orderBy( $this->sortOrder() )
			->limit( 50 )
			->fetchAll();
	}

	public function allForVersion($obj)
	{
		return $this->allObjectsForFK(Patch::version_id, $obj, $this->sortOrder(), 50);
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "version":
					return array( Patch::version_id, "id"  );
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
	public function base_create( $version, $name)
	{
		$obj = false;
		if ( isset($version, $name) ) {
			$params = array(
				Patch::name => (isset($name) ? $name : null),
				Patch::created => time(),
			);

			if ( isset($version) ) {
				if ( $version instanceof VersionDBO) {
					$params[Patch::version_id] = $version->id;
				}
				else if (  is_integer($version) ) {
					$params[Patch::version_id] = $version;
				}
			}

			list( $obj, $errorList ) = $this->createObject($params);
			if ( is_array($errorList) ) {
				return $errorList;
			}
		}
		return $obj;
	}

	public function base_update( PatchDBO $obj,
		$version, $name)
	{
		if ( isset( $obj ) && is_null($obj) == false ) {
			$updates = array();

			if (isset($name) && (isset($obj->name) == false || $name != $obj->name)) {
				$updates[Patch::name] = $name;
			}

			if ( isset($version) ) {
				if ( $version instanceof VersionDBO) {
					$updates[Patch::version_id] = $version->id;
				}
				else if (  is_integer($version) ) {
					$updates[Patch::version_id] = $version;
				}
			}

			if ( count($updates) > 0 ) {
				list($obj, $errorList) = $this->updateObject( $obj, $updates );
				if ( is_array($errorList) ) {
					return $errorList;
				}
			}
		}
		return $obj;
	}

	/**
	 *	Delete functions
	 */
	public function deleteObject( DataObject $object = null)
	{
		if ( $object instanceof Patch )
		{
			// does not own Version
			return parent::deleteObject($object);
		}

		return false;
	}

	public function deleteAllForVersion(VersionDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForVersion($obj);
			foreach ($array as $key => $value) {
				if ($this->deleteObject($value) == false) {
					$success = false;
					break;
				}
			}
		}
		return $success;
	}

	/**
	 *	Named fetches
	 */

	/** Set attributes */
	public function setName( PatchDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Patch::name => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setCreated( PatchDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Patch::created => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setVersion_id( PatchDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Patch::version_id => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}


	/** Validation */
	function validate_name($object = null, $value)
	{
		$value = trim($value);
		if (empty($value)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Patch::name,
				"FIELD_EMPTY"
			);
		}
		// make sure Name is unique
		$existing = $this->objectForName($value);
		if ( $existing != false && ( is_null($object) || $existing->id != $object->id)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Patch::name,
				"UNIQUE_FIELD_VALUE"
			);
		}
		return null;
	}
	function validate_created($object = null, $value)
	{
		if ( isset($object, $object->created) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Patch::created,
				"IMMUTABLE"
			);
		}
		return null;
	}
	function validate_version_id($object = null, $value)
	{
		if (isset($object->version_id) === false && empty($value) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Patch::version_id,
				"FIELD_EMPTY"
			);
		}
		return null;
	}
}

?>
