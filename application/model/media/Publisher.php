<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \model\media\PublisherDBO as PublisherDBO;

/* import related objects */
use \model\media\Series as Series;
use \model\media\SeriesDBO as SeriesDBO;
use \model\media\Character as Character;
use \model\media\CharacterDBO as CharacterDBO;
use \model\media\Story_Arc as Story_Arc;
use \model\media\Story_ArcDBO as Story_ArcDBO;

class Publisher extends _Publisher
{
	/**
	 *	Create/Update functions
	 */
	public function createObject( array $values = array())
	{
		if ( isset($values) ) {
			if ( isset($values['xid'], $values['xsrc']) && empty($values['xid']) == false && empty($values['xsrc']) == false ) {
				$obj = $this->objectForExternal($values['xid'], $values['xsrc']);
				if ( $obj != false ) {
					return $this->updateObject( $obj, $values );
				}
			}

			// massage values as necessary
			if ( isset($values["xid"]) ) {
				$values[Publisher::xupdated] = time();
			}
		}

		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof PublisherDBO ) {
			// massage values as necessary
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		$attrFor = array(
			Publisher::name
		);
		return array_intersect_key($this->attributesMap(),array_flip($attrFor));
	}

	public function attributeIsEditable($object = null, $type = null, $attr)
	{
		if (isset($object) && $object instanceof PublisherDBO ) {
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
		return null;
	}

	/** Validation */
/*
	function validate_name($object = null, $value)
	{
		return parent::validate_name($object, $value);
	}
*/

/*
	function validate_created($object = null, $value)
	{
		return parent::validate_created($object, $value);
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
	public function findExternalOrCreate( $name, $xid, $xsrc, $xurl = null )
	{
		if ( isset($name, $xid, $xsrc) && strlen($name) && strlen($xid) && strlen($xsrc)) {
			$obj = $this->objectForExternal($xid, $xsrc);
			if ( $obj == false ) {
				list($obj, $errors) = $this->createObject(array(
					"name" => $name,
					"xid" => $xid,
					"xsource" => $xsrc,
					"xurl" => $xurl
					)
				);
				if ( is_array($errors) && count($errors) > 0) {
					throw \Exception("Errors creating new Publisher " . var_export($errors, true) );
				}
			}
			else {
				$updates = array();

				if (isset($name) && $name != $obj->name ) {
					$updates[Publisher::name] = $name;
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
						throw \Exception("Errors creating new Publisher " . var_export($errors, true) );
					}
				}
			}

			return $obj;
		}
		return false;
	}

}

?>
