<?php

namespace model\version;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\version\PatchDBO as PatchDBO;

/* import related objects */
use \model\version\Version as Version;
use \model\version\VersionDBO as VersionDBO;

/** Generated class, do not edit.
 */
abstract class _Patch extends Model
{
	const TABLE = 'patch';

	// attribute keys
	const id = 'id';
	const name = 'name';
	const created = 'created';
	const version_id = 'version_id';

	// relationship keys
	const version = 'version';

	public function modelName()
	{
		return "Patch";
	}

	public function dboName()
	{
		return '\model\version\PatchDBO';
	}

	public function tableName() { return Patch::TABLE; }
	public function tablePK() { return Patch::id; }

	public function sortOrder()
	{
		return array(
			array( 'desc' => Patch::created),
			array( 'desc' => Patch::name)
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

	public function allAttributes()
	{
		return array(
			Patch::name,
			Patch::created,
		);
	}

	public function allForeignKeys()
	{
		return array(Patch::version_id);
	}

	public function allRelationshipNames()
	{
		return array(
			Patch::version
		);
	}

	public function attributes()
	{
		return array(
			Patch::name => array('length' => 256,'type' => 'TEXT'),
			Patch::created => array('type' => 'DATE'),
		);
	}

	public function relationships()
	{
		return array(
			Patch::version => array(
				'destination' => 'Version',
				'ownsDestination' => false,
				'isMandatory' => true,
				'isToMany' => false,
				'joins' => array( 'version_id' => 'id')
			)
		);
	}

	public function searchQualifiers( array $query )
	{
		$qualifiers = array();
		if ( is_array($query) ) {
			foreach( $query as $attr => $value ) {
				switch ($attr) {
			// Patch::id == INTEGER

			// Patch::name == TEXT
				case Patch::name:
					if (strlen($value) > 0) {
						$qualifiers[Patch::name] = Qualifier::Like(Patch::name, $value);
					}
					break;

			// Patch::created == DATE

			// Patch::version_id == INTEGER
				case Patch::version_id:
					if ( intval($value) > 0 ) {
						$qualifiers[Patch::version_id] = Qualifier::Equals( Patch::version_id, intval($value) );
					}
					break;

				default:
					/* no type specified for Patch::version_id */
					break;
				}
			}
		}
		return $qualifiers;
	}

	/**
	 *	Simple fetches
	 */

	public function objectForName($value)
	{
		return $this->singleObjectForKeyValue(Patch::name, $value);
	}

	public function allLikeName($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return SQL::Select( $this )
			->where( Qualifier::Like( Patch::name, $value, SQL::SQL_LIKE_AFTER ))
			->orderBy( $this->sortOrder() )
			->limit( $limit )
			->fetchAll();
	}




	/**
	 * Simple relationship fetches
	 */
	public function allForVersion($obj, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForFK(Patch::version_id, $obj, $this->sortOrder(), $limit);
	}

	public function countForVersion($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Patch::version_id, $obj );
		}
		return false;
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
	public function createObject( array $values = array() )
	{
		if ( isset($values) ) {

			// default values for attributes
			if ( isset($values['name']) == false ) {
				$default_name = $this->attributeDefaultValue( null, null, Patch::name);
				if ( is_null( $default_name ) == false ) {
					$values['name'] = $default_name;
				}
			}
			if ( isset($values['created']) == false ) {
				$default_created = $this->attributeDefaultValue( null, null, Patch::created);
				if ( is_null( $default_created ) == false ) {
					$values['created'] = $default_created;
				}
			}

			// default conversion for relationships
			if ( isset($values['version']) ) {
				$local_version = $values['version'];
				if ( $local_version instanceof VersionDBO) {
					$values[Patch::version_id] = $local_version->id;
				}
				else if ( is_integer( $local_version) ) {
					$params[Patch::version_id] = $local_version;
				}
			}
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Patch ) {
			if ( isset($values['version']) ) {
				$local_version = $values['version'];
				if ( $local_version instanceof VersionDBO) {
					$values[Patch::version_id] = $local_version->id;
				}
				else if ( is_integer( $local_version) ) {
					$params[Patch::version_id] = $values['version'];
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
		if ( $object instanceof PatchDBO )
		{
			// does not own version Version
			return parent::deleteObject($object);
		}

		return false;
	}

	public function deleteAllForVersion(VersionDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForVersion($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForVersion($obj);
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
				Patch::name
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesMap() {
		return array(
			Patch::name => Model::TEXT_TYPE,
			Patch::created => Model::DATE_TYPE,
			Patch::version_id => Model::TO_ONE_TYPE
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
				case Patch::version_id:
					$version_model = Model::Named('Version');
					$fkObject = $version_model->objectForId( $value );
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
	function validate_name($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
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
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// created date is not changeable
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
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
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
