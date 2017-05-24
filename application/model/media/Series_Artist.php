<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \model\media\Series_ArtistDBO as Series_ArtistDBO;

/* import related objects */
use \model\media\Series as Series;
use \model\media\SeriesDBO as SeriesDBO;
use \model\media\artist as artist;
use \model\media\artistDBO as artistDBO;

class Series_Artist extends _Series_Artist
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
		if (isset($object) && $object instanceof Series_ArtistDBO ) {
			// massage values as necessary
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		$attrFor = array(
			Series_Artist::series_id,
			Series_Artist::artist_id
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
		if ( Series_Artist::series_id == $attr ) {
			$model = Model::Named('Series');
			return $model->allObjects();
		}
		if ( Series_Artist::artist_id == $attr ) {
			$model = Model::Named('artist');
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
	function validate_artist_id($object = null, $value)
	{
		return parent::validate_artist_id($object, $value);
	}
*/

}

?>
