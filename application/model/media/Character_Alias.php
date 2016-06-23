<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \model\media\Character_AliasDBO as Character_AliasDBO;

/* import related objects */
use \model\media\Character as Character;
use \model\media\CharacterDBO as CharacterDBO;

class Character_Alias extends _Character_Alias
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
		if (isset($object) && $object instanceof Character_AliasDBO ) {
			// massage values as necessary
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		$attrFor = array(
			Character_Alias::name,
			Character_Alias::character_id
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
		if ( Character_Alias::character_id == $attr ) {
			$model = Model::Named('Character');
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
	function validate_character_id($object = null, $value)
	{
		return parent::validate_character_id($object, $value);
	}
*/
	public function createAlias( CharacterDBO $object, $name )
	{
		if (isset($object, $object->id, $name)) {
			$alias = $this->objectForCharacterAndAlias($object, $name);
			if ($alias == false) {
				list( $alias, $errorList ) = $this->createObject(array(
					Character_Alias::character_id => $object->id,
					Character_Alias::name => $name
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
