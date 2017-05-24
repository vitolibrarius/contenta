<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \model\media\Artist_RoleDBO as Artist_RoleDBO;

/* import related objects */

class Artist_Role extends _Artist_Role
{
	const UNKNOWN_ROLE = "unknown";

	public function unknownRole()
	{
		$role = $this->objectForCode( Artist_Role::UNKNOWN_ROLE );
		if ( $role == false ) {
			$role = $this->createObject( array(
				Artist_Role::code => Artist_Role::UNKNOWN_ROLE,
				Artist_Role::name => "Unknown",
				Artist_Role::enabled => Model::TERTIARY_TRUE
				)
			);
		}
		return $role;
	}

	public function searchQualifiers( array $query )
	{
		$qualifiers = parent::searchQualifiers($query);
		return $qualifiers;
	}

	/**
	 *	Create/Update functions
	 */
	public function createObject( array $values = array())
	{
		if ( isset($values) ) {
			// massage values as necessary
		}

		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Artist_RoleDBO ) {
			// massage values as necessary
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		$attrFor = array(
			Artist_Role::name
		);
		return array_intersect_key($this->attributesMap(),array_flip($attrFor));
	}

	/*
	public function attributeIsEditable($object = null, $type = null, $attr)
	{
		// add customization here
		return parent::attributeIsEditable($object, $type, $attr);
	}
	*/

	/*
	public function attributeRestrictionMessage($object = null, $type = null, $attr)	{ return null; }
	public function attributePlaceholder($object = null, $type = null, $attr)	{ return null; }
	*/

	/*
	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		return parent::attributeDefaultValue($object, $type, $attr);
	}
	*/

	/*
	public function attributeEditPattern($object = null, $type = null, $attr)
	{
		return null;
	}
	*/

	public function attributeOptions($object = null, $type = null, $attr)
	{
		return null;
	}

	/** Validation */
/*
	function validate_name($object = null, $value)
	{
		return parent::validate_name($object, $value);
	}
*/

	public function findRoleOrCreate($code, $name, $enabled = null ) {
		if ( isset($name, $code) && strlen($code) && strlen($name)) {
			$obj = $this->objectForCode($code);
			if ( $obj == false )
			{
				list($obj, $errors) = $this->createObject(array(
					"code" => $code,
					"name" => $name,
					"enabled" => $enabled
					)
				);
				if ( is_array($errors) && count($errors) > 0) {
					throw \Exception("Errors creating new Artist Role " . var_export($errors, true) );
				}
			}
			else {
				$updates = array();
				if (isset($name) && (isset($obj->name) == false || $name != $obj->name)) {
					$updates[Artist_Role::name] = $name;
				}
				if (isset($enabled) && is_null($enabled) == false && (isset($obj->enabled) == false || $enabled != $obj->enabled)) {
					$updates[Artist_Role::enabled] = $enabled;
				}
				if ( count($updates) > 0 ) {
					list($obj, $errors) = $this->updateObject($obj, $updates );
					if ( is_array($errors) && count($errors) > 0) {
						throw new \Exception("Errors creating new Artist_Role " . var_export($errors, true) );
					}
				}
			}
			return $obj;
		}

		return false;
	}
}

?>
