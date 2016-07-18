<?php

namespace model\network;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \model\network\FluxDBO as FluxDBO;

/* import related objects */
use \model\network\Endpoint as Endpoint;
use \model\network\EndpointDBO as EndpointDBO;

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
		$attrFor = array(
			Flux::created,
			Flux::name,
			Flux::flux_hash,
			Flux::flux_error,
			Flux::src_endpoint,
			Flux::src_guid,
			Flux::src_url,
			Flux::src_status,
			Flux::src_pub_date,
			Flux::dest_endpoint,
			Flux::dest_guid,
			Flux::dest_status,
			Flux::dest_submission
		);
		return array_intersect_key($this->attributesMap(),array_flip($attrFor));
	}

	public function allDestinationIncomplete($limit = 50)
	{
		$select = SQL::Select( $this )->where( Qualifier::AndQualifier(
				Qualifier::IsNotNull( Flux::dest_guid ),
				Qualifier::NotQualifier( Qualifier::IN( Flux::dest_status, array('Completed', 'Failed') ))
			)
		)->limit( $limit );
		return $select->fetchAll();
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
	function validate_src_url($object = null, $value)
	{
		return parent::validate_src_url($object, $value);
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
