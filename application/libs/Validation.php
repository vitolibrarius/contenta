<?php

use \Database as Database;
use \Localized as Localized;
use \Logger as Logger;
use \Model as Model;
use \Config as Config;
use \DataObject as DataObject;

abstract class Validation
{
	const TEXT_TYPE = 'text';
	const PASSWORD_TYPE = 'password';
	const TEXTAREA_TYPE = 'textarea';
	const INT_TYPE = 'number';
	const DATE_TYPE = 'date';
	const FLAG_TYPE = 'flag';

	public function modelName() {
		return substr(get_short_class($this), 0, strpos(get_short_class($this), '_Validation'));
	}

	/** validation */
	public function validateForSave($object = null, array &$values = array())
	{
		$validationErrors = array();

		$mandatoryKeys = $this->attributesMandatory($object);
		if ( is_array($mandatoryKeys) == false ) {
			$mandatoryKeys = array_keys($values);
		}
		else {
			$mandatoryKeys = array_merge_recursive($mandatoryKeys, array_keys($values) );
		}
		$mandatoryKeys = array_unique($mandatoryKeys);

		foreach( $mandatoryKeys as $key ) {
			$function = "validate_" . $key;
			if (method_exists($this, $function)) {
				$newvalue = (isset($values[$key]) ? $values[$key] : null);
				$failure = $this->$function($object, $newvalue);
				if ( is_null($failure) == false ) {
					$validationErrors[$key] = $failure;
				}
			}
		}
		return $validationErrors;
	}

	public abstract function tableName();

	public function attributesFor($object = null, $type = null) 				{ return array(); }
	public function attributesMandatory($object = null)				 			{ return array(); }
	public function attributeName($object = null, $type = null, $attr)			{ return $this->attributeId($attr); }
	public function attributeIsEditable($object = null, $type = null, $attr)	{ return true; }
	public function attributeRestrictionMessage($object = null, $type = null, $attr)	{ return null; }
	public function attributeEditPattern($object = null, $type = null, $attr)	{ return null; }
	public function attributePlaceholder($object = null, $type = null, $attr)	{ return null; }
	public function attributeOptions($object = null, $type = null, $attr)		{ return null; }

	public function attributeLabel($object = null, $type = null, $attr)
	{
		return Localized::ModelLabel($this->tableName(), $attr);
	}

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object, $object->{$attr}) && is_null($object->{$attr}) == false) {
			return $object->{$attr};
		}
		return null;
	}

	public function attributeId($attr)
	{
		return $this->tableName() . Model::HTML_ATTR_SEPARATOR . $attr;
	}

	public function attributeType($attr)
	{
		$attributeArray = $this->attributesFor(null);
		if ( is_array($attributeArray) && isset($attributeArray[$attr]) ) {
			return $attributeArray[$attr];
		}
		return null;
	}

	/* self test for consistency */
	public function consistencyTest()
	{
		return "ok";
	}
}
?>
