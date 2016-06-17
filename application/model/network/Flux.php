<?php

namespace model\network;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \model\network\FluxDBO as FluxDBO;

/* import related objects */
use \model\Endpoint as Endpoint;
use \model\EndpointDBO as EndpointDBO;

/** Synonym for torrent ..
	flux
	noun state of constant change
 */
class Flux extends _Flux
{
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
		if (isset($object) && $object instanceof FluxDBO ) {
			// massage values as necessary
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		return array(
			Flux::created => Model::DATE_TYPE,
			Flux::name => Model::TEXT_TYPE,
			Flux::flux_hash => Model::TEXT_TYPE,
			Flux::flux_error => Model::TEXT_TYPE,
			Flux::src_endpoint => Model::INT_TYPE,
			Flux::src_guid => Model::TEXT_TYPE,
			Flux::src_status => Model::TEXT_TYPE,
			Flux::src_pub_date => Model::DATE_TYPE,
			Flux::dest_endpoint => Model::INT_TYPE,
			Flux::dest_guid => Model::TEXT_TYPE,
			Flux::dest_status => Model::TEXT_TYPE,
			Flux::dest_submission => Model::DATE_TYPE
		);
	}

	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Flux::name
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
		if ( Flux::src_endpoint == $attr ) {
			$model = Model::Named('Endpoint');
			return $model->allObjects();
		}
		if ( Flux::dest_endpoint == $attr ) {
			$model = Model::Named('Endpoint');
			return $model->allObjects();
		}
		return null;
	}

	/** Validation */
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
	function validate_flux_hash($object = null, $value)
	{
		return parent::validate_flux_hash($object, $value);
	}
*/
/*
	function validate_flux_error($object = null, $value)
	{
		return parent::validate_flux_error($object, $value);
	}
*/
/*
	function validate_src_endpoint($object = null, $value)
	{
		return parent::validate_src_endpoint($object, $value);
	}
*/
/*
	function validate_src_guid($object = null, $value)
	{
		return parent::validate_src_guid($object, $value);
	}
*/
/*
	function validate_src_status($object = null, $value)
	{
		return parent::validate_src_status($object, $value);
	}
*/
/*
	function validate_src_pub_date($object = null, $value)
	{
		return parent::validate_src_pub_date($object, $value);
	}
*/
/*
	function validate_dest_endpoint($object = null, $value)
	{
		return parent::validate_dest_endpoint($object, $value);
	}
*/
/*
	function validate_dest_guid($object = null, $value)
	{
		return parent::validate_dest_guid($object, $value);
	}
*/
/*
	function validate_dest_status($object = null, $value)
	{
		return parent::validate_dest_status($object, $value);
	}
*/
/*
	function validate_dest_submission($object = null, $value)
	{
		return parent::validate_dest_submission($object, $value);
	}
*/
}

?>
