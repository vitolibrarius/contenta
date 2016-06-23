<?php

namespace model\version;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \model\version\VersionDBO as VersionDBO;

/* import related objects */
use \model\version\Patch as Patch;
use \model\version\PatchDBO as PatchDBO;

class Version extends _Version
{
	/**
	 *	Create/Update functions
	 */
	public function createObject( array $values = array())
	{
		if ( isset($values) ) {
			if ( isset($values[Version::code]) ) {
				$vers = explode(".", $values[Version::code] );
				$values[Version::major] = (isset($vers[0]) ? intval($vers[0]) : 0);
				$values[Version::minor] = (isset($vers[1]) ? intval($vers[1]) : 0);
				$values[Version::patch] = (isset($vers[2]) ? intval($vers[2]) : 0);
			}
		}

		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof VersionDBO ) {
			if ( isset($values[Version::code]) ) {
				$vers = explode(".", $code );
				$values[Version::major] = (isset($vers[0]) ? intval($vers[0]) : 0);
				$values[Version::minor] = (isset($vers[1]) ? intval($vers[1]) : 0);
				$values[Version::patch] = (isset($vers[2]) ? intval($vers[2]) : 0);
			}
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		$attrFor = array(
			Version::code,
			Version::major,
			Version::minor,
			Version::patch,
			Version::created
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
	function validate_code($object = null, $value)
	{
		return parent::validate_code($object, $value);
	}
*/

/*
	function validate_major($object = null, $value)
	{
		return parent::validate_major($object, $value);
	}
*/

/*
	function validate_minor($object = null, $value)
	{
		return parent::validate_minor($object, $value);
	}
*/

/*
	function validate_patch($object = null, $value)
	{
		return parent::validate_patch($object, $value);
	}
*/

/*
	function validate_created($object = null, $value)
	{
		return parent::validate_created($object, $value);
	}
*/

}

?>
