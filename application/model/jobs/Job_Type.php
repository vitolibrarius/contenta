<?php

namespace model\jobs;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \model\jobs\Job_TypeDBO as Job_TypeDBO;

/* import related objects */
use \model\jobs\Job_Running as Job_Running;
use \model\jobs\Job_RunningDBO as Job_RunningDBO;
use \model\jobs\Job as Job;
use \model\jobs\JobDBO as JobDBO;

class Job_Type extends _Job_Type
{
	const character = "character";
	const publication = "publication";
	const publisher = "publisher";
	const series = "series";
	const story_arc = "story_arc";

	const newznab_search = "newznab_search";
	const previewsworld = "previewsworld";
	const reprocessor = "reprocessor";
	const rss = "rss";
	const sabnzbd = "sabnzbd";

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
		if (isset($object) && $object instanceof Job_TypeDBO ) {
			// massage values as necessary
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		$attrFor = array(
			Job_Type::code,
			Job_Type::name,
			Job_Type::desc,
			Job_Type::processor,
			Job_Type::parameter,
			Job_Type::scheduled,
			Job_Type::requires_endpoint
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
		return null;
	}

	/** Validation */
/*
	function validate_code($object = null, $value)
	{
		return parent::validate_code($object, $value);
	}
*/

/*
	function validate_name($object = null, $value)
	{
		return parent::validate_name($object, $value);
	}
*/

/*
	function validate_desc($object = null, $value)
	{
		return parent::validate_desc($object, $value);
	}
*/

/*
	function validate_processor($object = null, $value)
	{
		return parent::validate_processor($object, $value);
	}
*/

/*
	function validate_parameter($object = null, $value)
	{
		return parent::validate_parameter($object, $value);
	}
*/

/*
	function validate_scheduled($object = null, $value)
	{
		return parent::validate_scheduled($object, $value);
	}
*/

/*
	function validate_requires_endpoint($object = null, $value)
	{
		return parent::validate_requires_endpoint($object, $value);
	}
*/

}

?>
