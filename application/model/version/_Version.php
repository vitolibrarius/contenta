<?php

namespace model\version;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \model\version\VersionDBO as VersionDBO;

/* import related objects */
use \model\version\Patch as Patch;
use \model\version\PatchDBO as PatchDBO;

/** Sample Creation script */
		/** VERSION */
/*
		$sql = "CREATE TABLE IF NOT EXISTS version ( "
			. Version::id . " INTEGER PRIMARY KEY, "
			. Version::code . " TEXT, "
			. Version::major . " INTEGER, "
			. Version::minor . " INTEGER, "
			. Version::patch . " INTEGER, "
			. Version::created . " INTEGER "
		. ")";
		$this->sqlite_execute( "version", $sql, "Create table version" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS version_code on version (code)';
		$this->sqlite_execute( "version", $sql, "Index on version (code)" );
		$sql = 'CREATE  INDEX IF NOT EXISTS version_majorminorpatch on version (major,minor,patch)';
		$this->sqlite_execute( "version", $sql, "Index on version (major,minor,patch)" );
*/
abstract class _Version extends Model
{
	const TABLE = 'version';
	const id = 'id';
	const code = 'code';
	const major = 'major';
	const minor = 'minor';
	const patch = 'patch';
	const created = 'created';

	public function tableName() { return Version::TABLE; }
	public function tablePK() { return Version::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Version::code)
		);
	}

	public function allColumnNames()
	{
		return array(
			Version::id,
			Version::code,
			Version::major,
			Version::minor,
			Version::patch,
			Version::created
		);
	}

	/**
	 *	Simple fetches
	 */
	public function objectForCode($value)
	{
		return $this->singleObjectForKeyValue(Version::code, $value);
	}

	public function allLikeCode($value)
	{
		return SQL::Select( $this )
			->where( Qualifier::Like( Version::code, $value, SQL::SQL_LIKE_AFTER ))
			->orderBy( $this->sortOrder() )
			->limit( 50 )
			->fetchAll();
	}


	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "patch":
					return array( Version::id, "version_id"  );
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
	public function base_create( $code, $major, $minor, $patch)
	{
		$obj = false;
		if ( isset($code) ) {
			$params = array(
				Version::code => (isset($code) ? $code : null),
				Version::major => (isset($major) ? $major : null),
				Version::minor => (isset($minor) ? $minor : null),
				Version::patch => (isset($patch) ? $patch : null),
				Version::created => time(),
			);


			list( $obj, $errorList ) = $this->createObject($params);
			if ( is_array($errorList) ) {
				return $errorList;
			}
		}
		return $obj;
	}

	public function base_update( VersionDBO $obj,
		$code, $major, $minor, $patch)
	{
		if ( isset( $obj ) && is_null($obj) == false ) {
			$updates = array();

			if (isset($code) && (isset($obj->code) == false || $code != $obj->code)) {
				$updates[Version::code] = $code;
			}
			if (isset($major) && (isset($obj->major) == false || $major != $obj->major)) {
				$updates[Version::major] = $major;
			}
			if (isset($minor) && (isset($obj->minor) == false || $minor != $obj->minor)) {
				$updates[Version::minor] = $minor;
			}
			if (isset($patch) && (isset($obj->patch) == false || $patch != $obj->patch)) {
				$updates[Version::patch] = $patch;
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
		if ( $object instanceof Version )
		{
			$patch_model = Model::Named('Patch');
			if ( $patch_model->deleteAllForKeyValue(Patch::version_id, $this->id) == false ) {
				return false;
			}
			return parent::deleteObject($object);
		}

		return false;
	}


	/**
	 *	Named fetches
	 */
	public function latestVersion(  )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		$qualifiers[] = Qualifier::InSubQuery( 'code', SQL::Aggregate( 'max', Model::Named('Version'), 'code', null, null ), null);

		if ( count($qualifiers) > 0 ) {
			$select->where( Qualifier::Combine( 'AND', $qualifiers ));
		}

		$result = $select->fetchAll();
		if ( is_array($result) && count($result) > 1 ) {
			throw new \Exception( latestVersion . " expected 1 result, but fetched " . count($result) );
		}

		return (is_array($result) ? $result[0] : false );
	}


	/** Set attributes */
	public function setCode( VersionDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Version::code => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setMajor( VersionDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Version::major => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setMinor( VersionDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Version::minor => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setPatch( VersionDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Version::patch => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setCreated( VersionDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Version::created => $value)) ) {
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
				Version::code,
				"FIELD_EMPTY"
			);
		}
		// make sure Code is unique
		$existing = $this->objectForCode($value);
		if ( $existing != false && ( is_null($object) || $existing->id != $object->id)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Version::code,
				"UNIQUE_FIELD_VALUE"
			);
		}
		return null;
	}
	function validate_major($object = null, $value)
	{
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Version::major,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
	function validate_minor($object = null, $value)
	{
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Version::minor,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
	function validate_patch($object = null, $value)
	{
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Version::patch,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
	function validate_created($object = null, $value)
	{
		if ( isset($object, $object->created) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Version::created,
				"IMMUTABLE"
			);
		}
		return null;
	}
}

?>
