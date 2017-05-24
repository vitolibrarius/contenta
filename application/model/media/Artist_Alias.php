<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \model\media\Artist_AliasDBO as Artist_AliasDBO;

/* import related objects */
use \model\media\Artist as Artist;
use \model\media\ArtistDBO as ArtistDBO;

class Artist_Alias extends _Artist_Alias
{
	public function searchQualifiers( array $query )
	{
		$qualifiers = parent::searchQualifiers($query);
		return $qualifiers;
	}

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
		if (isset($object) && $object instanceof Artist_AliasDBO ) {
			// massage values as necessary
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		$attrFor = array(
			Artist_Alias::name,
			Artist_Alias::artist_id
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
		if ( Artist_Alias::artist_id == $attr ) {
			$model = Model::Named('Artist');
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
	function validate_artist_id($object = null, $value)
	{
		return parent::validate_artist_id($object, $value);
	}
*/

	public function createAlias( ArtistDBO $object, $name )
	{
		if (isset($object, $object->id, $name)) {
			$alias = $this->objectForArtistAndAlias($object, $name);
			if ($alias == false) {
				list( $alias, $errorList ) = $this->createObject(array(
					Artist_Alias::artist_id => $object->id,
					Artist_Alias::name => $name
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
