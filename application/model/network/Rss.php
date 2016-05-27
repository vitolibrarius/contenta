<?php

namespace model\network;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \model\network\RssDBO as RssDBO;

/* import related objects */
use \model\Endpoint as Endpoint;
use \model\EndpointDBO as EndpointDBO;
use \model\Flux as Flux;
use \model\FluxDBO as FluxDBO;

class Rss extends _Rss
{
	/**
	 *	Create/Update functions
	 */
	public function create( $endpoint, $title, $desc, $pub_date, $guid, $clean_name, $clean_issue, $clean_year, $enclosure_url, $enclosure_length, $enclosure_mime, $enclosure_hash, $enclosure_password)
	{
		return $this->base_create(
			$endpoint,
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

	public function update( RssDBO $obj,
		$endpoint, $title, $desc, $pub_date, $guid, $clean_name, $clean_issue, $clean_year, $enclosure_url, $enclosure_length, $enclosure_mime, $enclosure_hash, $enclosure_password)
	{
		if ( isset( $obj ) && is_null($obj) === false ) {
			return $this->base_update(
				$obj,
				$endpoint,
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
		return $obj;
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

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) === false || is_null($object) == true) {
			switch ($attr) {
			}
		}
		return parent::attributeDefaultValue($object, $type, $attr);
	}

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
		if ( $attr = Rss::guid ) {
			$model = Model::Named('Flux');
			return $model->allObjects();
		}
		return null;
	}

	/** Validation */
	function validate_endpoint_id($object = null, $value)
	{
		return parent::validate_endpoint_id($object, $value);
	}

	function validate_created($object = null, $value)
	{
		return parent::validate_created($object, $value);
	}

	function validate_title($object = null, $value)
	{
		return parent::validate_title($object, $value);
	}

	function validate_desc($object = null, $value)
	{
		return parent::validate_desc($object, $value);
	}

	function validate_pub_date($object = null, $value)
	{
		return parent::validate_pub_date($object, $value);
	}

	function validate_guid($object = null, $value)
	{
		return parent::validate_guid($object, $value);
	}

	function validate_clean_name($object = null, $value)
	{
		return parent::validate_clean_name($object, $value);
	}

	function validate_clean_issue($object = null, $value)
	{
		return parent::validate_clean_issue($object, $value);
	}

	function validate_clean_year($object = null, $value)
	{
		return parent::validate_clean_year($object, $value);
	}

	function validate_enclosure_url($object = null, $value)
	{
		return parent::validate_enclosure_url($object, $value);
	}

	function validate_enclosure_length($object = null, $value)
	{
		return parent::validate_enclosure_length($object, $value);
	}

	function validate_enclosure_mime($object = null, $value)
	{
		return parent::validate_enclosure_mime($object, $value);
	}

	function validate_enclosure_hash($object = null, $value)
	{
		return parent::validate_enclosure_hash($object, $value);
	}

	function validate_enclosure_password($object = null, $value)
	{
		return parent::validate_enclosure_password($object, $value);
	}

}

?>
