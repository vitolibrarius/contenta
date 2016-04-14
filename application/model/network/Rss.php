<?php

namespace model\network;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Validation as Validation;
use \utilities\MediaFilename as MediaFilename;

use \model\network\RssDBO as RssDBO;

/* import related objects */
use \model\Endpoint as Endpoint;
use \model\EndpointDBO as EndpointDBO;

class Rss extends _Rss
{
	/**
	 *	Create/Update functions
	 */
	public function create( EndpointDBO $endpoint = null, $title, $desc, $pub_date, $guid, $encl_url = null, $encl_length = 0, $encl_mime = 'application/x-nzb', $encl_hash = null, $encl_password = false )
	{
		if ( isset($title, $guid) && is_null($endpoint) == false ) {
			$mediaFilename = new MediaFilename($title);
			$meta = $mediaFilename->updateFileMetaData(null);

			return $this->base_create(
				$endpoint,
				$title,
				(isset($desc) ? strip_tags($desc) : null),
				$pub_date,
				$guid,
				$meta['name'], // clean_name
				(isset($meta['issue']) ? $meta['issue'] : null), // clean_issue
				(isset($meta['year']) ? $meta['year'] : null),  // clean_year
				$encl_url,
				$encl_length,
				$encl_mime,
				$encl_hash,
				($encl_password) ? 1 : 0 // encl_password
			);
		}
		return false;
	}

	public function update( RssDBO $obj = null, $title, $desc, $pub_date, $enclosure_url = null, $enclosure_length = 0, $enclosure_mime = 'application/x-nzb', $enclosure_hash = null, $enclosure_password = false )
	{
		if ( isset( $obj ) && is_null($obj) == false ) {
			$clean_name = null;
			$clean_issue = null;
			$clean_year = null;

			if (isset($title) && (isset($obj->title) == false || $title != $obj->title)) {
				$mediaFilename = new MediaFilename($title);
				$meta = $mediaFilename->updateFileMetaData(null);

				$clean_name = $meta['name'];
				$clean_issue = (isset($meta['issue']) ? $meta['issue'] : null);
				$clean_year = (isset($meta['year']) ? $meta['year'] : null);
			}

			if (isset($desc) && $desc != $obj->desc ) {
				$desc = strip_tags($desc);
			}

			if (isset($enclosure_password) && (isset($obj->enclosure_password) == false || boolval($enclosure_password) != boolval($obj->enclosure_password))) {
				$enclosure_password = boolval($encl_password);
			}

			return $this->base_update(
				$obj,
				null, // endpoint
				$title,
				$desc,
				$pub_date,
				$guid,
				$clean_name,
				$clean_issue,
				$clean_year,
				$enclosure_url,
				$enclosure_length,
				$enclosure_mime,
				$enclosure_hash,
				$enclosure_password
			);
		}
		return false;
	}


	public function attributesFor($object = null, $type = null) {
		return array(
			Rss::endpoint_id => Model::INT_TYPE,
			Rss::created => Model::DATE_TYPE,
			Rss::title => Model::TEXT_TYPE,
			Rss::desc => Model::TEXT_TYPE,
			Rss::pub_date => Model::DATE_TYPE,
			Rss::guid => Model::TEXT_TYPE,
			Rss::clean_name => Model::TEXT_TYPE,
			Rss::clean_issue => Model::TEXT_TYPE,
			Rss::clean_year => Model::INT_TYPE,
			Rss::enclosure_url => Model::TEXT_TYPE,
			Rss::enclosure_length => Model::INT_TYPE,
			Rss::enclosure_mime => Model::TEXT_TYPE,
			Rss::enclosure_hash => Model::TEXT_TYPE,
			Rss::enclosure_password => Model::FLAG_TYPE
		);
	}

	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Rss::title,
				Rss::pub_date,
				Rss::guid,
				Rss::clean_name,
				Rss::enclosure_url
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributeIsEditable($object = null, $type = null, $attr)
	{
		// add customization here
		return parent::attributeIsEditable($object, $type, $attr);
	}

	/*
	public function attributeRestrictionMessage($object = null, $type = null, $attr)	{ return null; }
	public function attributePlaceholder($object = null, $type = null, $attr)	{ return null; }
	*/

	public function attributeEditPattern($object = null, $type = null, $attr)
	{
		return null;
	}

	public function attributeOptions($object = null, $type = null, $attr)
	{
		if ( $attr = Rss::endpoint_id ) {
			$model = Model::Named('Endpoint');
			return $model->allObjects();
		}
		return null;
	}

	/** Validation */
	function validate_endpoint_id($object = null, $value)
	{
		return null;
	}
	function validate_created($object = null, $value)
	{
		return null;
	}
	function validate_title($object = null, $value)
	{
		if (empty($value)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Rss::title,
				"FIELD_EMPTY"
			);
		}
		return null;
	}
	function validate_desc($object = null, $value)
	{
		return null;
	}
	function validate_pub_date($object = null, $value)
	{
		return null;
	}
	function validate_guid($object = null, $value)
	{
		if (empty($value)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Rss::guid,
				"FIELD_EMPTY"
			);
		}
		// make sure Guid is unique
		$existing = $this->objectForGuid($value);
		if ( is_null($object) == false && $existing != false && $existing->id != $object->id) {
			return Localized::ModelValidation(
				$this->tableName(),
				Rss::guid,
				"UNIQUE_FIELD_VALUE"
			);
		}
		return null;
	}
	function validate_clean_name($object = null, $value)
	{
		if (empty($value)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Rss::clean_name,
				"FIELD_EMPTY"
			);
		}
		return null;
	}
	function validate_clean_issue($object = null, $value)
	{
		return null;
	}
	function validate_clean_year($object = null, $value)
	{
		return null;
	}
	function validate_enclosure_url($object = null, $value)
	{
		if (empty($value)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Rss::enclosure_url,
				"FIELD_EMPTY"
			);
		}
		return null;
	}
	function validate_enclosure_length($object = null, $value)
	{
		return null;
	}
	function validate_enclosure_mime($object = null, $value)
	{
		return null;
	}
	function validate_enclosure_hash($object = null, $value)
	{
		return null;
	}
	function validate_enclosure_password($object = null, $value)
	{
		return null;
	}
}

?>
