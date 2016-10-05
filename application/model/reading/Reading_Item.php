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

class Reading_Item extends _Reading_Item
{
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

	public function processNotification( $type = 'none', DataObject $dbo )
	{
		if ( $dbo instanceof Reading_ItemDBO ) {
			$queueIds = \SQL::raw(
				"select distinct q.id from reading_queue q, reading_item r, publication p"
				. " left join story_arc_publication sap on sap.publication_id = p.id"
				. " where ( p.series_id = q.series_id or sap.story_arc_id = q.story_arc_id )"
				. " and q.user_id = r.user_id and r.publication_id = p.id and r.id = :itemId;",
				array( ":itemId" => $dbo->pkValue() )
			);

			if ( is_array($queueIds) && count($queueIds) > 0 ) {
				$queueIdArray = array_map(function($stdClass) {return $stdClass->{Reading_Queue::id}; }, $queueIds);
				\SQL::raw(
					"update reading_queue set pub_read = ( select count(r.read_date) from reading_item r, publication p "
					. "  left join story_arc_publication sap on sap.publication_id = p.id "
					. "  where ( p.series_id = reading_queue.series_id or sap.story_arc_id = reading_queue.story_arc_id ) "
					. "  and reading_queue.user_id = r.user_id and r.publication_id = p.id) "
					. "where id in (" . implode(",", $queueIdArray) . ");"
				);
			}

		}
		return parent::processNotification( $type, $dbo );
	}
}

?>
