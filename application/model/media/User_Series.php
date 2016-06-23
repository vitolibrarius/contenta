<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \model\media\User_SeriesDBO as User_SeriesDBO;

/* import related objects */
use \model\user\Users as Users;
use \model\user\UsersDBO as UsersDBO;
use \model\media\Series as Series;
use \model\media\SeriesDBO as SeriesDBO;

class User_Series extends _User_Series
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
		if (isset($object) && $object instanceof User_SeriesDBO ) {
			// massage values as necessary
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		$attrFor = array(
			User_Series::user_id,
			User_Series::series_id,
			User_Series::favorite,
			User_Series::read,
			User_Series::mislabeled
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
		if ( User_Series::user_id == $attr ) {
			$model = Model::Named('Users');
			return $model->allObjects();
		}
		if ( User_Series::series_id == $attr ) {
			$model = Model::Named('Series');
			return $model->allObjects();
		}
		return null;
	}

	/** Validation */
/*
	function validate_user_id($object = null, $value)
	{
		return parent::validate_user_id($object, $value);
	}
*/

/*
	function validate_series_id($object = null, $value)
	{
		return parent::validate_series_id($object, $value);
	}
*/

/*
	function validate_favorite($object = null, $value)
	{
		return parent::validate_favorite($object, $value);
	}
*/

/*
	function validate_read($object = null, $value)
	{
		return parent::validate_read($object, $value);
	}
*/

/*
	function validate_mislabeled($object = null, $value)
	{
		return parent::validate_mislabeled($object, $value);
	}
*/
	public function createJoin( UsersDBO $UsersObj, SeriesDBO $seriesObj )
	{
		if (isset($UsersObj, $UsersObj->id, $seriesObj, $seriesObj->id)) {
			$join = $this->objectForUserAndSeries($UsersObj, $seriesObj);
			if ($join == false) {
				list( $join, $errorList ) = $this->createObject(array(
					"story_arc" => $UsersObj,
					"series" => $seriesObj
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
