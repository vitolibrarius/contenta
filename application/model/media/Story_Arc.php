<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \model\media\Story_ArcDBO as Story_ArcDBO;

/* import related objects */
use \model\media\Publisher as Publisher;
use \model\media\PublisherDBO as PublisherDBO;
use \model\media\Story_Arc_Characters as Story_Arc_Characters;
use \model\media\Story_Arc_CharactersDBO as Story_Arc_CharactersDBO;
use \model\media\Story_Arc_Publication as Story_Arc_Publication;
use \model\media\Story_Arc_PublicationDBO as Story_Arc_PublicationDBO;
use \model\media\Story_Arc_Series as Story_Arc_Series;
use \model\media\Story_Arc_SeriesDBO as Story_Arc_SeriesDBO;
use \model\reading\Reading_Queue as Reading_Queue;
use \model\reading\Reading_QueueDBO as Reading_QueueDBO;
use \model\reading\Reading_Item as Reading_Item;
use \model\reading\Reading_ItemDBO as Reading_ItemDBO;

class Story_Arc extends _Story_Arc
{
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
		if (isset($object) && $object instanceof Story_ArcDBO ) {
			if ( isset($values['desc']) && strlen($values['desc']) > 0 ) {
				$values['desc'] = strip_tags($values['desc']);
			}
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		$attrFor = array(
			Story_Arc::publisher_id,
			Story_Arc::name,
			Story_Arc::desc,
			Story_Arc::pub_active,
			Story_Arc::pub_wanted
		);
		return array_intersect_key($this->attributesMap(),array_flip($attrFor));
	}

	public function attributeIsEditable($object = null, $type = null, $attr)
	{
		if (isset($object) && $object instanceof Story_ArcDBO ) {
			if ( isset($object->xid, $object->xsource) && is_null($object->xid) == false ) {
				switch ( $attr ) {
					case Story_Arc::pub_active:
					case Story_Arc::pub_wanted:
						return true;
					default: break;
				}

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
		if ( Story_Arc::publisher_id == $attr ) {
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
	function validate_desc($object = null, $value)
	{
		return parent::validate_desc($object, $value);
	}
*/

/*
	function validate_pub_active($object = null, $value)
	{
		return parent::validate_pub_active($object, $value);
	}
*/

/*
	function validate_pub_wanted($object = null, $value)
	{
		return parent::validate_pub_wanted($object, $value);
	}
*/

/*
	function validate_pub_cycle($object = null, $value)
	{
		return parent::validate_pub_cycle($object, $value);
	}
*/

/*
	function validate_pub_available($object = null, $value)
	{
		return parent::validate_pub_available($object, $value);
	}
*/

/*
	function validate_pub_count($object = null, $value)
	{
		return parent::validate_pub_count($object, $value);
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
	public function findExternalOrCreate( $publisher = null, $name, $desc, $xid, $xsrc, $xurl = null )
	{
		if ( isset($name, $xid, $xsrc) && strlen($name) && strlen($xid) && strlen($xsrc)) {
			$obj = $this->objectForExternal($xid, $xsrc);
			if ( $obj == false ) {
				list($obj, $errors) = $this->createObject( array(
					"publisher" => $publisher,
					Story_Arc::name => $name,
					Story_Arc::desc => $desc,
					Story_Arc::xurl => $xurl,
					Story_Arc::xsource => $xsrc,
					Story_Arc::xid => $xid,
					)
				);
				if ( is_array($errors) && count($errors) > 0) {
					throw \Exception("Errors creating new Story Arc " . var_export($errors, true) );
				}
			}
			else {
				$updates = array();

				if ( isset($publisher, $publisher->id) && (isset($obj->publisher_id) == false || $publisher->id != $obj->publisher_id) ) {
					$updates["publisher"] = $publisher;
				}

				if (isset($name) && (isset($obj->name) == false || $name != $obj->name)) {
					$updates[Story_Arc::name] = $name;
				}

				if (isset($desc) && strlen($desc) > 0) {
					$updates[Story_Arc::desc] = $desc;
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
						throw \Exception("Errors creating new Story Arc " . var_export($errors, true) );
					}
				}
			}

			return $obj;
		}
		return false;
	}

}

?>
