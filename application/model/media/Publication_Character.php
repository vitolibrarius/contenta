<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \model\media\Publication_CharacterDBO as Publication_CharacterDBO;

/* import related objects */
use \model\media\Publication as Publication;
use \model\media\PublicationDBO as PublicationDBO;
use \model\media\Character as Character;
use \model\media\CharacterDBO as CharacterDBO;

class Publication_Character extends _Publication_Character
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
		if (isset($object) && $object instanceof Publication_CharacterDBO ) {
			// massage values as necessary
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		$attrFor = array(
			Publication_Character::publication_id,
			Publication_Character::character_id
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
		if ( Publication_Character::publication_id == $attr ) {
			$model = Model::Named('Publication');
			return $model->allObjects();
		}
		if ( Publication_Character::character_id == $attr ) {
			$model = Model::Named('Character');
			return $model->allObjects();
		}
		return null;
	}

	/** Validation */
/*
	function validate_publication_id($object = null, $value)
	{
		return parent::validate_publication_id($object, $value);
	}
*/

/*
	function validate_character_id($object = null, $value)
	{
		return parent::validate_character_id($object, $value);
	}
*/
	public function publicationIdForCharacterIdArray( array $obj = null)
	{
		if ( is_array($obj) && count($obj) > 0 ) {
			$select = SQL::Select($this, array( Publication_Character::publication_id ));
			$select->where( Qualifier::IN( Publication_Character::character_id, $obj ));
			$select->groupBy( array( Publication_Character::publication_id ) );
			$select->having( array("count(" . Publication_Character::publication_id. ") = " . count($obj)) );

			$publication_idArray = $select->fetchAll();
			return array_map(function($stdClass) {return $stdClass->{Publication_Character::publication_id}; }, $publication_idArray);
		}
		return array();
	}

	public function createJoin( PublicationDBO $pubObj, CharacterDBO $characterObj )
	{
		if (isset($pubObj, $pubObj->id, $characterObj, $characterObj->id)) {
			$join = $this->objectForPublicationAndCharacter($pubObj, $characterObj);
			if ($join == false) {
				list( $join, $errorList ) = $this->createObject(array(
					"publication" => $pubObj,
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
