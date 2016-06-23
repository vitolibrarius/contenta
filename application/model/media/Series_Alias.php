<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \model\media\Series_AliasDBO as Series_AliasDBO;

/* import related objects */
use \model\media\Series as Series;
use \model\media\SeriesDBO as SeriesDBO;

class Series_Alias extends _Series_Alias
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
		if (isset($object) && $object instanceof Series_AliasDBO ) {
			// massage values as necessary
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		$attrFor = array(
			Series_Alias::name,
			Series_Alias::series_id
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
		if ( Series_Alias::series_id == $attr ) {
			$model = Model::Named('Series');
			return $model->allObjects();
		}
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
	function validate_series_id($object = null, $value)
	{
		return parent::validate_series_id($object, $value);
	}
*/

	public function createAlias( SeriesDBO $object, $name )
	{
		if (isset($object, $object->id, $name)) {
			$alias = $this->objectForSeriesAndAlias($object, $name);
			if ($alias == false) {
				list( $alias, $errorList ) = $this->createObject(array(
					Series_Alias::series_id => $object->id,
					Series_Alias::name => $name
					)
				);

				if ( is_array($errorList) ) {
					return $errorList;
				}
			}

			return $alias;
		}

		return false;
	}
}

?>
