<?php

namespace model\network;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use utilities\MediaFilename as MediaFilename;

use \model\network\RssDBO as RssDBO;

/* import related objects */
use \model\network\Endpoint as Endpoint;
use \model\network\EndpointDBO as EndpointDBO;
use \model\network\Flux as Flux;
use \model\network\FluxDBO as FluxDBO;

class Rss extends _Rss
{
	/**
	 *	Create/Update functions
	 */
	public function createObject( array $values = array())
	{
		if ( isset($values) ) {
			if ( isset($values[Rss::title]) ) {
				$mediaFilename = new MediaFilename($values[Rss::title]);
				$meta = $mediaFilename->updateFileMetaData(null);
				$values[Rss::clean_name] = $meta['name'];
				$values[Rss::clean_issue] = (isset($meta['issue']) ? $meta['issue'] : null);
				$values[Rss::clean_year] = (isset($meta['year']) ? $meta['year'] : null);
			}

			if ( isset($values[Rss::enclosure_password]) ) {
				$values[Rss::enclosure_password] = boolValue($values[Rss::enclosure_password], false);
			}
			else {
				$values[Rss::enclosure_password] = false;
			}
		}

		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof RssDBO ) {
			if ( isset($values[Rss::title]) ) {
				$mediaFilename = new MediaFilename($values[Rss::title]);
				$meta = $mediaFilename->updateFileMetaData(null);
				$values[Rss::clean_name] = $meta['name'];
				$values[Rss::clean_issue] = (isset($meta['issue']) ? $meta['issue'] : null);
				$values[Rss::clean_year] = (isset($meta['year']) ? $meta['year'] : null);
			}

			if ( isset($values[Rss::enclosure_password]) ) {
				$values[Rss::enclosure_password] = boolValue($values[Rss::enclosure_password], false);
			}
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		$attrFor = array(
			Rss::endpoint_id,
			Rss::created,
			Rss::title,
			Rss::desc,
			Rss::pub_date,
			Rss::guid,
			Rss::clean_name,
			Rss::clean_issue,
			Rss::clean_year,
			Rss::enclosure_url,
			Rss::enclosure_length,
			Rss::enclosure_mime,
			Rss::enclosure_hash,
			Rss::enclosure_password
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
		if ( Rss::endpoint_id == $attr ) {
			$model = Model::Named('Endpoint');
			return $model->allObjects();
		}
		return null;
	}

	/** Validation */
/*
	function validate_endpoint_id($object = null, $value)
	{
		return parent::validate_endpoint_id($object, $value);
	}
*/

/*
	function validate_created($object = null, $value)
	{
		return parent::validate_created($object, $value);
	}
*/

/*
	function validate_title($object = null, $value)
	{
		return parent::validate_title($object, $value);
	}
*/

/*
	function validate_desc($object = null, $value)
	{
		return parent::validate_desc($object, $value);
	}
*/

/*
	function validate_pub_date($object = null, $value)
	{
		return parent::validate_pub_date($object, $value);
	}
*/

/*
	function validate_guid($object = null, $value)
	{
		return parent::validate_guid($object, $value);
	}
*/

/*
	function validate_clean_name($object = null, $value)
	{
		return parent::validate_clean_name($object, $value);
	}
*/

/*
	function validate_clean_issue($object = null, $value)
	{
		return parent::validate_clean_issue($object, $value);
	}
*/

/*
	function validate_clean_year($object = null, $value)
	{
		return parent::validate_clean_year($object, $value);
	}
*/

/*
	function validate_enclosure_url($object = null, $value)
	{
		return parent::validate_enclosure_url($object, $value);
	}
*/

/*
	function validate_enclosure_length($object = null, $value)
	{
		return parent::validate_enclosure_length($object, $value);
	}
*/

/*
	function validate_enclosure_mime($object = null, $value)
	{
		return parent::validate_enclosure_mime($object, $value);
	}
*/

/*
	function validate_enclosure_hash($object = null, $value)
	{
		return parent::validate_enclosure_hash($object, $value);
	}
*/

/*
	function validate_enclosure_password($object = null, $value)
	{
		return parent::validate_enclosure_password($object, $value);
	}
*/
}

?>
