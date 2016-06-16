<?php

namespace model\pull_list;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \utilities\MediaFilename as MediaFilename;

use \model\pull_list\Pull_List_ItemDBO as Pull_List_ItemDBO;

/* import related objects */
use \model\pull_list\Pull_List as Pull_List;
use \model\pull_list\Pull_ListDBO as Pull_ListDBO;

class Pull_List_Item extends _Pull_List_Item
{
	/**
	 *	Create/Update functions
	 */
	public function createObject( array $values = array())
	{
		if ( isset($values) ) {
			// massage values as necessary
			if ( isset($values[Pull_List_Item::data]) ) {
				$mediaFilename = new MediaFilename($values[Pull_List_Item::data]);
				$meta = $mediaFilename->updateFileMetaData(null);
				$values[Pull_List_Item::name] = $meta['name'];
				$values[Pull_List_Item::issue] = (isset($meta['issue']) ? $meta['issue'] : null);
				$values[Pull_List_Item::year] = (isset($meta['year']) ? $meta['year'] : null);

				$values[Pull_List_Item::search_name] = normalizeSearchString($meta['name']);

				$existing = $this->objectsForNameIssueYear(
					$values[Pull_List_Item::name],
					$values[Pull_List_Item::issue],
					$values[Pull_List_Item::year]
				);
				if ( is_array($existing) && count($existing) > 0 ) {
					return array( $existing[0], null);
				}
			}
		}

		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Pull_List_Item ) {
			// massage values as necessary
			if ( isset($values[Pull_List_Item::data]) ) {
				$mediaFilename = new MediaFilename($values[Pull_List_Item::data]);
				$meta = $mediaFilename->updateFileMetaData(null);
				$values[Pull_List_Item::name] = $meta['name'];
				$values[Pull_List_Item::issue] = (isset($meta['issue']) ? $meta['issue'] : null);
				$values[Pull_List_Item::year] = (isset($meta['year']) ? $meta['year'] : null);
			}

			if ( isset($values[Pull_List_Item::name]) ) {
				$values[Pull_List_Item::search_name] = normalizeSearchString($values[Pull_List_Item::name]);
			}
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		return array(
			Pull_List_Item::group_name => Model::TEXT_TYPE,
			Pull_List_Item::data => Model::TEXT_TYPE,
			Pull_List_Item::created => Model::DATE_TYPE,
			Pull_List_Item::name => Model::TEXT_TYPE,
			Pull_List_Item::issue => Model::TEXT_TYPE,
			Pull_List_Item::year => Model::INT_TYPE,
			Pull_List_Item::pull_list_id => Model::INT_TYPE
		);
	}

	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Pull_List_Item::data,
				Pull_List_Item::name
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributeIsEditable($object = null, $type = null, $attr)
	{
		// add customization here
		return parent::attributeIsEditable($object, $type, $attr);
	}

	/*
	public function attributeRestrictionMessage($object = null, $type = null, $attr)	{ return null; }
	public function attributePlaceholder($object = null, $type = null, $attr)	{ return null; }
	*/

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) === false || is_null($object) == true) {
			switch ($attr) {
			}
		}
		return parent::attributeDefaultValue($object, $type, $attr);
	}

	public function attributeEditPattern($object = null, $type = null, $attr)
	{
		return null;
	}

	public function attributeOptions($object = null, $type = null, $attr)
	{
		if ( $attr == Pull_List_Item::pull_list_id ) {
			$model = Model::Named('Pull_List');
			return $model->allObjects();
		}
		return null;
	}

	/** Validation */
	function validate_group_name($object = null, $value)
	{
		return parent::validate_group_name($object, $value);
	}

	function validate_data($object = null, $value)
	{
		return parent::validate_data($object, $value);
	}

	function validate_created($object = null, $value)
	{
		return parent::validate_created($object, $value);
	}

	function validate_name($object = null, $value)
	{
		return parent::validate_name($object, $value);
	}

	function validate_issue($object = null, $value)
	{
		return parent::validate_issue($object, $value);
	}

	function validate_year($object = null, $value)
	{
		return parent::validate_year($object, $value);
	}

	function validate_pull_list_id($object = null, $value)
	{
		return parent::validate_pull_list_id($object, $value);
	}

}

?>
