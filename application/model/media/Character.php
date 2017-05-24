<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \model\media\CharacterDBO as CharacterDBO;

/* import related objects */
use \model\media\Character_Alias as Character_Alias;
use \model\media\Character_AliasDBO as Character_AliasDBO;
use \model\media\Publisher as Publisher;
use \model\media\PublisherDBO as PublisherDBO;

class Character extends _Character
{
	public function searchQualifiers( array $query )
	{
		$qualifiers = parent::searchQualifiers($query);
		if ( isset($query[Character::popularity])) {
			unset($qualifiers[Character::popularity]);
			$qualifiers[Character::popularity] = Qualifier::GreaterThan( Character::popularity, intval($query[Character::popularity]) );
		}
		return $qualifiers;
	}

	/**
	 *	Create/Update functions
	 */
	public function createObject( array $values = array())
	{
		if ( isset($values) ) {
			if ( isset($values['desc']) && strlen($values['desc']) > 0 ) {
				$values['desc'] = strip_tags($values['desc']);
			}
		}

		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof CharacterDBO ) {
			if ( isset($values['desc']) && strlen($values['desc']) > 0 ) {
				$values['desc'] = strip_tags($values['desc']);
			}
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		$attrFor = array(
			Character::publisher_id,
			Character::name,
			Character::realname,
			Character::desc,
			Character::gender
		);
		return array_intersect_key($this->attributesMap(),array_flip($attrFor));
	}

	public function attributeIsEditable($object = null, $type = null, $attr)
	{
		if (isset($object) && $object instanceof CharacterDBO ) {
			if ( isset($object->xid, $object->xsource) && is_null($object->xid) == false ) {
				return false;
			}
		}
		// add customization here
		return parent::attributeIsEditable($object, $type, $attr);
	}

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
		if ( Character::publisher_id == $attr ) {
			$model = Model::Named('Publisher');
			return $model->allObjects();
		}
		return null;
	}

	/** Validation */
/*
	function validate_publisher_id($object = null, $value)
	{
		return parent::validate_publisher_id($object, $value);
	}
*/

/*
	function validate_created($object = null, $value)
	{
		return parent::validate_created($object, $value);
	}
*/

/*
	function validate_name($object = null, $value)
	{
		return parent::validate_name($object, $value);
	}
*/

/*
	function validate_realname($object = null, $value)
	{
		return parent::validate_realname($object, $value);
	}
*/

/*
	function validate_desc($object = null, $value)
	{
		return parent::validate_desc($object, $value);
	}
*/

/*
	function validate_popularity($object = null, $value)
	{
		return parent::validate_popularity($object, $value);
	}
*/

/*
	function validate_gender($object = null, $value)
	{
		return parent::validate_gender($object, $value);
	}
*/

/*
	function validate_xurl($object = null, $value)
	{
		return parent::validate_xurl($object, $value);
	}
*/

/*
	function validate_xsource($object = null, $value)
	{
		return parent::validate_xsource($object, $value);
	}
*/

/*
	function validate_xid($object = null, $value)
	{
		return parent::validate_xid($object, $value);
	}
*/

/*
	function validate_xupdated($object = null, $value)
	{
		return parent::validate_xupdated($object, $value);
	}
*/
	public function findExternalOrCreate($publishObj, $name, $realname, $gender, $desc, $aliases, $xid, $xsrc, $xurl = null )
	{
		if ( isset($name, $xid, $xsrc) && strlen($name) && strlen($xid) && strlen($xsrc)) {
			$obj = $this->objectForExternal($xid, $xsrc);
			if ( $obj == false )
			{
				list($obj, $errors) = $this->createObject(array(
					"publisher" => $publishObj,
					"name" => $name,
					"realname" => $realname,
					"gender" => $gender,
					"desc" => $desc,
					"xid" => $xid,
					"xsource" => $xsrc,
					"xurl" => $xurl
					)
				);
				if ( is_array($errors) && count($errors) > 0) {
					throw \Exception("Errors creating new Character " . var_export($errors, true) );
				}
			}
			else {
				$updates = array();

				if ( isset($publishObj, $publishObj->id) && (isset($obj->publisher_id) == false || $publishObj->id != $obj->publisher_id) ) {
					$updates["publisher"] = $publishObj;
				}

				if (isset($name) && (isset($obj->name) == false || $name != $obj->name)) {
					$updates[Character::name] = $name;
				}

				if (isset($realname) && $realname != $obj->realname ) {
					$updates[Character::realname] = $realname;
				}

				if (isset($gender) && $gender != $obj->gender ) {
					$updates[Character::gender] = $gender;
				}

				if (isset($desc) && $desc != $obj->desc ) {
					$updates[Character::desc] = $desc;
				}

				if ( isset($xid) ) {
					$updates["xid"] = $xid;
				}

				if ( isset($xsrc) ) {
					$updates["xsource"] = $xsrc;
				}

				if ((isset($xurl) && strlen($xurl) > 0) && (isset($obj->xurl) == false || strlen($obj->xurl) == 0)) {
					$updates["xurl"] = $xurl;
				}

				if ( count($updates) > 0 ) {
					list($obj, $errors) = $this->updateObject($obj, $updates );
					if ( is_array($errors) && count($errors) > 0) {
						throw \Exception("Errors updating Character " . var_export($errors, true) );
					}
				}
			}

			if ( $obj != false && is_array($aliases) ) {
				$char_model = Model::Named("Character_Alias");
				foreach ($aliases as $key => $value) {
					$char_model->createAlias($obj, $value);
				}
			}
			return $obj;
		}

		return false;
	}

}

?>
