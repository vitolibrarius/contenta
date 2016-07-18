<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \model\media\Story_Arc_PublicationDBO as Story_Arc_PublicationDBO;

/* import related objects */
use \model\media\Story_Arc as Story_Arc;
use \model\media\Story_ArcDBO as Story_ArcDBO;
use \model\media\Publication as Publication;
use \model\media\PublicationDBO as PublicationDBO;

class Story_Arc_Publication extends _Story_Arc_Publication
{
	public function notifyKeypaths() { return array( "story_arc" ); }

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
		if (isset($object) && $object instanceof Story_Arc_PublicationDBO ) {
			// massage values as necessary
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		$attrFor = array(
			Story_Arc_Publication::story_arc_id,
			Story_Arc_Publication::publication_id
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
		if ( Story_Arc_Publication::story_arc_id == $attr ) {
			$model = Model::Named('Story_Arc');
			return $model->allObjects();
		}
		if ( Story_Arc_Publication::publication_id == $attr ) {
			$model = Model::Named('Publication');
			return $model->allObjects();
		}
		return null;
	}

	/** Validation */
/*
	function validate_story_arc_id($object = null, $value)
	{
		return parent::validate_story_arc_id($object, $value);
	}
*/

/*
	function validate_publication_id($object = null, $value)
	{
		return parent::validate_publication_id($object, $value);
	}
*/
	public function createJoin( Story_ArcDBO $storyArcObj, PublicationDBO $pubObj )
	{
		if (isset($storyArcObj, $storyArcObj->id, $pubObj, $pubObj->id)) {
			$join = $this->objectForStoryArcAndPublication($storyArcObj, $pubObj);
			if ($join == false) {
				list( $join, $errorList ) = $this->createObject(array(
					"story_arc" => $storyArcObj,
					"publication" => $pubObj
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
