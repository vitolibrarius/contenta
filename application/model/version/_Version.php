<?php

namespace model\version;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

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

	// attribute keys
	const id = 'id';
	const code = 'code';
	const major = 'major';
	const minor = 'minor';
	const patch = 'patch';
	const created = 'created';

	// relationship keys
	const patches = 'patches';

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

	public function allForMajor($value)
	{
		return $this->allObjectsForKeyValue(Version::major, $value);
	}

	public function allForMinor($value)
	{
		return $this->allObjectsForKeyValue(Version::minor, $value);
	}

	public function allForPatch($value)
	{
		return $this->allObjectsForKeyValue(Version::patch, $value);
	}



	/**
	 * Simple relationship fetches
	 */

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
	public function createObject( array $values = array() )
	{
		if ( isset($values) ) {

			// default values for attributes
			if ( isset($values['code']) == false ) {
				$default_code = $this->attributeDefaultValue( null, null, Version::code);
				if ( is_null( $default_code ) == false ) {
					$values['code'] = $default_code;
				}
			}
			if ( isset($values['major']) == false ) {
				$default_major = $this->attributeDefaultValue( null, null, Version::major);
				if ( is_null( $default_major ) == false ) {
					$values['major'] = $default_major;
				}
			}
			if ( isset($values['minor']) == false ) {
				$default_minor = $this->attributeDefaultValue( null, null, Version::minor);
				if ( is_null( $default_minor ) == false ) {
					$values['minor'] = $default_minor;
				}
			}
			if ( isset($values['patch']) == false ) {
				$default_patch = $this->attributeDefaultValue( null, null, Version::patch);
				if ( is_null( $default_patch ) == false ) {
					$values['patch'] = $default_patch;
				}
			}
			if ( isset($values['created']) == false ) {
				$default_created = $this->attributeDefaultValue( null, null, Version::created);
				if ( is_null( $default_created ) == false ) {
					$values['created'] = $default_created;
				}
			}

			// default conversion for relationships
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Version ) {
		}

		return parent::updateObject($object, $values);
	}

	/**
	 *	Delete functions
	 */
	public function deleteObject( DataObject $object = null)
	{
		if ( $object instanceof VersionDBO )
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
	 * Named fetches
	 */
	public function latestVersion( )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		$qualifiers[] = Qualifier::InSubQuery( 'code', SQL::Aggregate( 'max', Model::Named('Version'), 'code', null, null ), null);

		if ( count($qualifiers) > 0 ) {
			$select->where( Qualifier::Combine( 'AND', $qualifiers ));
		}

		$result = $select->fetchAll();
		if ( is_array($result) ) {
			$result_size = count($result);
			if ( $result_size == 1 ) {
				return $result[0];
			}
			else if ($result_size > 1 ) {
				throw new \Exception( "latestVersion expected 1 result, but fetched " . count($result) );
			}
		}

		return false;
	}


	/**
	 * Attribute editing
	 */
	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Version::code
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesMap() {
		return array(
			Version::code => Model::TEXT_TYPE,
			Version::major => Model::INT_TYPE,
			Version::minor => Model::INT_TYPE,
			Version::patch => Model::INT_TYPE,
			Version::created => Model::DATE_TYPE
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
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
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
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
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
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
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
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// created date is not changeable
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
