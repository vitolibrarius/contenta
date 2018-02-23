<?php

namespace model\network;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \model\network\Endpoint_TypeDBO as Endpoint_TypeDBO;

/* import related objects */
use \model\network\Endpoint as Endpoint;
use \model\network\EndpointDBO as EndpointDBO;
use \model\pull_list\Pull_List_Expansion as Pull_List_Expansion;
use \model\pull_list\Pull_List_ExpansionDBO as Pull_List_ExpansionDBO;

class Endpoint_Type extends _Endpoint_Type
{
	const Newznab =		"Newznab";
	const RSS =			"RSS";
	const ComicVine =	"ComicVine";
	const SABnzbd =		"SABnzbd";
	const PreviewsWorld = "PreviewsWorld";

	public function Newznab() { return $this->objectForCode( Endpoint_Type::Newznab ); }
	public function RSS() { return $this->objectForCode( Endpoint_Type::RSS ); }
	public function ComicVine() { return $this->objectForCode( Endpoint_Type::ComicVine ); }
	public function SABnzbd() { return $this->objectForCode( Endpoint_Type::SABnzbd ); }
	public function PreviewsWorld() { return $this->objectForCode( Endpoint_Type::PreviewsWorld ); }

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
		if (isset($object) && $object instanceof Endpoint_TypeDBO ) {
			// massage values as necessary
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		$attrFor = array(
			Endpoint_Type::code,
			Endpoint_Type::name,
			Endpoint_Type::comments,
			Endpoint_Type::data_type,
			Endpoint_Type::site_url,
			Endpoint_Type::api_url,
			Endpoint_Type::favicon_url,
			Endpoint_Type::throttle_hits,
			Endpoint_Type::throttle_time
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
	function validate_name($object = null, $value)
	{
		return parent::validate_name($object, $value);
	}
*/

/*
	function validate_comments($object = null, $value)
	{
		return parent::validate_comments($object, $value);
	}
*/

/*
	function validate_data_type($object = null, $value)
	{
		return parent::validate_data_type($object, $value);
	}
*/

/*
	function validate_site_url($object = null, $value)
	{
		return parent::validate_site_url($object, $value);
	}
*/

/*
	function validate_api_url($object = null, $value)
	{
		return parent::validate_api_url($object, $value);
	}
*/

/*
	function validate_favicon_url($object = null, $value)
	{
		return parent::validate_favicon_url($object, $value);
	}
*/

/*
	function validate_throttle_hits($object = null, $value)
	{
		return parent::validate_throttle_hits($object, $value);
	}
*/

/*
	function validate_throttle_time($object = null, $value)
	{
		return parent::validate_throttle_time($object, $value);
	}
*/

}

?>
