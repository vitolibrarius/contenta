<?php

namespace model\media;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\media\Artist_RoleDBO as Artist_RoleDBO;

/* import related objects */

/** Generated class, do not edit.
 */
abstract class _Artist_Role extends Model
{
	const TABLE = 'artist_role';

	// attribute keys
	const code = 'code';
	const name = 'name';
	const enabled = 'enabled';

	// relationship keys

	public function modelName()
	{
		return "Artist_Role";
	}

	public function dboName()
	{
		return '\model\media\Artist_RoleDBO';
	}

	public function tableName() { return Artist_Role::TABLE; }
	public function tablePK() { return Artist_Role::code; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Artist_Role::name)
		);
	}

	public function allColumnNames()
	{
		return array(
			Artist_Role::code,
			Artist_Role::name,
			Artist_Role::enabled
		);
	}

	public function allAttributes()
	{
		return array(
			Artist_Role::name,
			Artist_Role::enabled
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
			Artist_Role::name => array('length' => 256,'type' => 'TEXT'),
			Artist_Role::enabled => array('type' => 'BOOLEAN')
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
			// Artist_Role::code == TEXT
				case Artist_Role::code:
					if (strlen($value) > 0) {
						$qualifiers[Artist_Role::code] = Qualifier::Equals( Artist_Role::code, $value );
					}
					break;

			// Artist_Role::name == TEXT
				case Artist_Role::name:
					if (strlen($value) > 0) {
						$qualifiers[Artist_Role::name] = Qualifier::Equals( Artist_Role::name, $value );
					}
					break;

			// Artist_Role::enabled == BOOLEAN
				case Artist_Role::enabled:
					$v = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
					if (is_null($v) == false) {
						$qualifiers[Artist_Role::enabled] = Qualifier::Equals( Artist_Role::enabled, $v );
					}
					break;

				default:
					/* no type specified for Artist_Role::enabled */
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
		return $this->singleObjectForKeyValue(Artist_Role::code, $value);
	}


	public function allForName($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Artist_Role::name, $value, null, $limit);
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
				$default_name = $this->attributeDefaultValue( null, null, Artist_Role::name);
				if ( is_null( $default_name ) == false ) {
					$values['name'] = $default_name;
				}
			}
			if ( isset($values['enabled']) == false ) {
				$default_enabled = $this->attributeDefaultValue( null, null, Artist_Role::enabled);
				if ( is_null( $default_enabled ) == false ) {
					$values['enabled'] = $default_enabled;
				}
			}

			// default conversion for relationships
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Artist_Role ) {
		}

		return parent::updateObject($object, $values);
	}

	/**
	 *	Delete functions
	 */
	public function deleteObject( DataObject $object = null)
	{
		if ( $object instanceof Artist_RoleDBO )
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
				Artist_Role::name
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesMap() {
		return array(
			Artist_Role::name => Model::TEXT_TYPE,
			Artist_Role::enabled => Model::FLAG_TYPE
		);
	}

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) === false || is_null($object) == true) {
			switch ($attr) {
				case Artist_Role::enabled:
					return Model::TERTIARY_FALSE;
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
				Artist_Role::name,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_enabled($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false  ) {
			return null;
		}

		// boolean

		// Returns TRUE for "1", "true", "on" and "yes"
		// Returns FALSE for "0", "false", "off" and "no"
		// Returns NULL otherwise.
		$v = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
		if (is_null($v)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Artist_Role::enabled,
				"FILTER_VALIDATE_BOOLEAN"
			);
		}
		return null;
	}
}

?>
