<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \model\media\MediaDBO as MediaDBO;

/* import related objects */
use \model\media\Media_Type as Media_Type;
use \model\media\Media_TypeDBO as Media_TypeDBO;
use \model\media\Publication as Publication;
use \model\media\PublicationDBO as PublicationDBO;

class Media extends _Media
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
		if (isset($object) && $object instanceof MediaDBO ) {
			// massage values as necessary
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		$attrFor = array(
			Media::publication_id,
			Media::type_id,
			Media::filename,
			Media::original_filename,
			Media::checksum,
			Media::created,
			Media::size
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
		if ( Media::type_id == $attr ) {
			$model = Model::Named('Media_Type');
			return $model->allObjects();
		}
		if ( Media::publication_id == $attr ) {
			$model = Model::Named('Publication');
			return $model->allObjects();
		}
		return null;
	}

	/** Validation */
	public function validateForSave($object = null, array &$values = array())
	{
		if ( is_null($object) && isset($values, $values[Media::filename]) == false) {
			$typeId = (isset($values[Media::type_id]) ? $values[Media::type_id] : null);
			$type = Model::Named('Media_Type')->objectForId($typeId);

			$pubId = (isset($values[Media::publication_id]) ? $values[Media::publication_id] : null);
			$publication = Model::Named('Publication')->objectForId($pubId);
			if ( $publication != false && $type != false) {
				$values[Media::filename] = $this->createFilenameForPublication( $publication, $type );
			}
		}

		return parent::validateForSave($object, $values);
	}
/*
	function validate_publication_id($object = null, $value)
	{
		return parent::validate_publication_id($object, $value);
	}
*/

/*
	function validate_type_id($object = null, $value)
	{
		return parent::validate_type_id($object, $value);
	}
*/

/*
	function validate_filename($object = null, $value)
	{
		return parent::validate_filename($object, $value);
	}
*/

/*
	function validate_original_filename($object = null, $value)
	{
		return parent::validate_original_filename($object, $value);
	}
*/

/*
	function validate_checksum($object = null, $value)
	{
		return parent::validate_checksum($object, $value);
	}
*/

/*
	function validate_created($object = null, $value)
	{
		return parent::validate_created($object, $value);
	}
*/

/*
	function validate_size($object = null, $value)
	{
		return parent::validate_size($object, $value);
	}
*/

	public function mostRecent( $limit = 20 )
	{
		return $this->allObjects( array(
			array("desc" => Media::created)
			), $limit);
	}

	private function createFilenameForPublication( PublicationDBO $publication = null, Media_TypeDBO $type = null)
	{
		if ( is_null($publication) ) {
			Logger::logError('Publication is a required parameter');
		}

		if ( is_null($type) ) {
			Logger::logError('Media_Type is a required parameter');
		}

		$filename_comp = array();

		$series = $publication->series();
		if ( $series != false ) {
			$filename_comp[] = sanitize_filename($series->{Series::name}, 100, false, false);
		}

		$filename_comp[] = $publication->{Publication::issue_num};

		$coverDate = $publication->publishedYear();
		if (isset($coverDate) && $coverDate > 0) {
			$filename_comp[] = $coverDate;
		}

		return implode(' - ', $filename_comp) . "." . $type->code;
	}

}

?>
