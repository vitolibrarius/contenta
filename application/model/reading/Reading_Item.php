<?php

namespace model\reading;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \model\reading\Reading_ItemDBO as Reading_ItemDBO;

/* import related objects */
use \model\user\Users as Users;
use \model\user\UsersDBO as UsersDBO;
use \model\media\Publication as Publication;
use \model\media\PublicationDBO as PublicationDBO;
use \model\reading\Reading_Queue_Item as Reading_Queue_Item;
use \model\reading\Reading_Queue_ItemDBO as Reading_Queue_ItemDBO;

class Reading_Item extends _Reading_Item
{
	public function notifyKeypaths() { return array( "reading_queues" ); }

	/**
	 *	Create/Update functions
	 */
	public function createObject( array $values = array())
	{
		if ( isset($values) ) {
		}

		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Reading_ItemDBO ) {
			// massage values as necessary
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		$attrFor = array(
			Reading_Item::user_id,
			Reading_Item::publication_id,
			Reading_Item::created,
			Reading_Item::read_date,
			Reading_Item::mislabeled
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
		if ( Reading_Item::user_id == $attr ) {
			$model = Model::Named('Users');
			return $model->allObjects();
		}
		if ( Reading_Item::publication_id == $attr ) {
			$model = Model::Named('Publication');
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
	function validate_publication_id($object = null, $value)
	{
		return parent::validate_publication_id($object, $value);
	}
*/

/*
	function validate_created($object = null, $value)
	{
		return parent::validate_created($object, $value);
	}
*/

/*
	function validate_read_date($object = null, $value)
	{
		return parent::validate_read_date($object, $value);
	}
*/

/*
	function validate_mislabeled($object = null, $value)
	{
		return parent::validate_mislabeled($object, $value);
	}
*/

	public function createReadingItemPublication( UsersDBO $user, PublicationDBO $publication )
	{
		if ( is_null($user) == false && is_null($publication) == false ) {
			$readingItem = $this->objectForUserAndPublication($user, $publication);
			if ( $readingItem == false ) {
				list($readingItem, $errors) = $this->createObject( array(
					Reading_Item::user => $user,
					Reading_Item::publication => $publication
					)
				);
				if ( is_array($errors) && count($errors) > 0) {
					throw \Exception("Errors creating new Reading Item " . var_export($errors, true) );
				}
			}
			return $readingItem;
		}
		return false;
	}
}

?>
