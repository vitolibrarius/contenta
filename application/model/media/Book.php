<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \utilities\FileWrapper as FileWrapper;

use \model\media\BookDBO as BookDBO;

/* import related objects */
use \model\media\Media_Type as Media_Type;
use \model\media\Media_TypeDBO as Media_TypeDBO;

class Book extends _Book
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
		if (isset($object) && $object instanceof BookDBO ) {
			// massage values as necessary
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		$attrFor = array(
			Book::type_code,
			Book::filename,
			Book::original_filename,
			Book::checksum,
			Book::created,
			Book::size,
			Book::name,
			Book::author,
			Book::desc,
			Book::pub_date,
			Book::pub_order
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
		if ( Book::type_code == $attr ) {
			$model = Model::Named('Media_Type');
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

/*
	function validate_name($object = null, $value)
	{
		return parent::validate_name($object, $value);
	}
*/

/*
	function validate_author($object = null, $value)
	{
		return parent::validate_author($object, $value);
	}
*/

/*
	function validate_desc($object = null, $value)
	{
		return parent::validate_desc($object, $value);
	}
*/

/*
	function validate_pub_date($object = null, $value)
	{
		return parent::validate_pub_date($object, $value);
	}
*/

/*
	function validate_pub_order($object = null, $value)
	{
		return parent::validate_pub_order($object, $value);
	}
*/

}

?>
