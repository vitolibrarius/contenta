<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \model\media\Series_CharacterDBO as Series_CharacterDBO;

/* import related objects */
use \model\media\Series as Series;
use \model\media\SeriesDBO as SeriesDBO;
use \model\media\Character as Character;
use \model\media\CharacterDBO as CharacterDBO;

class Series_Character extends _Series_Character
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
		if (isset($object) && $object instanceof Series_CharacterDBO ) {
			// massage values as necessary
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		$attrFor = array(
			Series_Character::series_id,
			Series_Character::character_id
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
		if ( Series_Character::series_id == $attr ) {
			$model = Model::Named('Series');
			return $model->allObjects();
		}
		if ( Series_Character::character_id == $attr ) {
			$model = Model::Named('Character');
			return $model->allObjects();
		}
		return null;
	}

	/** Validation */
/*
	function validate_series_id($object = null, $value)
	{
		return parent::validate_series_id($object, $value);
	}
*/

/*
	function validate_character_id($object = null, $value)
	{
		return parent::validate_character_id($object, $value);
	}
*/
	public function createJoin( SeriesDBO $seriesObj, CharacterDBO $characterObj )
	{
		if (isset($seriesObj, $seriesObj->id, $characterObj, $characterObj->id)) {
			$join = $this->objectForSeriesAndCharacter($seriesObj, $characterObj);
			if ($join == false) {
				list( $join, $errorList ) = $this->createObject(array(
					"series" => $seriesObj,
					"character" => $characterObj
					)
				);

				if ( is_array($errorList) ) {
					return $errorList;
				}
			}

			return $join;
		}

		return false;
	}

}

?>
