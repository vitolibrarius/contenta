<?php

namespace model\network;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \model\network\EndpointDBO as EndpointDBO;

/* import related objects */
use \model\network\Endpoint_Type as Endpoint_Type;
use \model\network\Endpoint_TypeDBO as Endpoint_TypeDBO;
use \model\pull_list\Pull_List as Pull_List;
use \model\pull_list\Pull_ListDBO as Pull_ListDBO;
use \model\network\Rss as Rss;
use \model\network\RssDBO as RssDBO;
use \model\network\Flux as Flux;
use \model\network\FluxDBO as FluxDBO;
use \model\jobs\Job as Job;
use \model\jobs\JobDBO as JobDBO;

class Endpoint extends _Endpoint
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
		if (isset($object) && $object instanceof EndpointDBO ) {
			// massage values as necessary
		}

		return parent::updateObject($object, $values);
	}

	public function allForTypeCode($code = null, $enabledOnly = true)
	{
		if ( $code != null ) {
			$type_model = Model::Named('Endpoint_Type');
			$type = $type_model->objectForCode($code);
			if ( $type != false ) {
				return $this->allForType($type, $enabledOnly);
			}
			die("no TYPE OBJ");
		}
		die("no code");
		return false;
	}

	public function allForType(Endpoint_TypeDBO $obj, $enabledOnly = true)
	{
		if ( $enabledOnly == true ) {
			return $this->allObjectsForFKWithValue(
				Endpoint::type_code, $obj,
				Endpoint::enabled, Model::TERTIARY_TRUE,
				$this->sortOrder(), -1
			);
		}
		return $this->allObjectsForFK(Endpoint::type_code, $obj, $this->sortOrder(), -1);
	}

	public function attributesFor($object = null, $type = null) {
		$attrFor = array(
			Endpoint::name,
			Endpoint::base_url,
			Endpoint::api_key,
			Endpoint::username,
			Endpoint::error_count,
			Endpoint::enabled,
			Endpoint::compressed
		);
		return array_intersect_key($this->attributesMap(),array_flip($attrFor));
	}

	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Endpoint::name,
				Endpoint::base_url
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributeIsEditable($object = null, $type = null, $attr)
	{
		if (isset($object) && $object instanceof EndpointDBO ) {
			if ( $attr == Endpoint::error_count ) {
				return false;
			}
		}
		return parent::attributeIsEditable($object, $type, $attr);
	}

	public function attributeRestrictionMessage($object = null, $type = null, $attr)
	{
		if ( $attr == Endpoint::type_code ) {
			if ( isset($type, $type->comments) ) {
				return $type->comments;
			}
		}

		return null;
	}
	/*
	public function attributePlaceholder($object = null, $type = null, $attr)	{ return null; }
	*/

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) === false || is_null($object) == true) {
			switch ($attr) {
				case Endpoint::base_url:
					return (isset($type) ? $type->api_url : null);
				case Endpoint::name:
					return (isset($type) ? $type->name : null);
			}
		}
		return parent::attributeDefaultValue($object, $type, $attr);
	}

	/*
	public function attributeEditPattern($object = null, $type = null, $attr)
	{
		return null;
	}
	*/

	public function attributeOptions($object = null, $type = null, $attr)
	{
		if ( Endpoint::type_code == $attr ) {
			$model = Model::Named('Endpoint_Type');
			return $model->allObjects();
		}
		return null;
	}

	/** Validation */
/*
	function validate_type_code($object = null, $value)
	{
		return parent::validate_type_code($object, $value);
	}
*/

/*
	function validate_name($object = null, $value)
	{
		return parent::validate_name($object, $value);
	}
*/

/*
	function validate_base_url($object = null, $value)
	{
		return parent::validate_base_url($object, $value);
	}
*/

/*
	function validate_api_key($object = null, $value)
	{
		return parent::validate_api_key($object, $value);
	}
*/

/*
	function validate_username($object = null, $value)
	{
		return parent::validate_username($object, $value);
	}
*/

/*
	function validate_enabled($object = null, $value)
	{
		return parent::validate_enabled($object, $value);
	}
*/

/*
	function validate_compressed($object = null, $value)
	{
		return parent::validate_compressed($object, $value);
	}
*/

}

?>
