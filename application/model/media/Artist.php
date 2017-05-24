<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \model\media\ArtistDBO as ArtistDBO;

/* import related objects */
use \model\media\Artist_Alias as Artist_Alias;
use \model\media\Artist_AliasDBO as Artist_AliasDBO;
use \model\media\Publication_Artists as Publication_Artists;
use \model\media\Publication_ArtistsDBO as Publication_ArtistsDBO;
use \model\media\Series_Artists as Series_Artists;
use \model\media\Series_ArtistsDBO as Series_ArtistsDBO;
use \model\media\Story_Arc_Artist as Story_Arc_Artist;
use \model\media\Story_Arc_ArtistDBO as Story_Arc_ArtistDBO;

class Artist extends _Artist
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
		if (isset($object) && $object instanceof ArtistDBO ) {
			// massage values as necessary
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		$attrFor = array(
			Artist::created,
			Artist::name,
			Artist::desc,
			Artist::gender,
			Artist::birth_date,
			Artist::death_date,
			Artist::pub_wanted,
			Artist::xurl,
			Artist::xsource,
			Artist::xid,
			Artist::xupdated
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
	function validate_created($object = null, $value)
	{
		return parent::validate_created($object, $value);
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
	function validate_gender($object = null, $value)
	{
		return parent::validate_gender($object, $value);
	}
*/

/*
	function validate_birth_date($object = null, $value)
	{
		return parent::validate_birth_date($object, $value);
	}
*/

/*
	function validate_death_date($object = null, $value)
	{
		return parent::validate_death_date($object, $value);
	}
*/

/*
	function validate_pub_wanted($object = null, $value)
	{
		return parent::validate_pub_wanted($object, $value);
	}
*/

/*
	function validate_xurl($object = null, $value)
	{
		return parent::validate_xurl($object, $value);
	}
*/

/*
	function validate_xsource($object = null, $value)
	{
		return parent::validate_xsource($object, $value);
	}
*/

/*
	function validate_xid($object = null, $value)
	{
		return parent::validate_xid($object, $value);
	}
*/

/*
	function validate_xupdated($object = null, $value)
	{
		return parent::validate_xupdated($object, $value);
	}
*/
/*	const created = 'created';
	const name = 'name';
	const desc = 'desc';
	const gender = 'gender';
	const birth_date = 'birth_date';
	const death_date = 'death_date';
	const pub_wanted = 'pub_wanted';
*/
	public function findExternalOrCreate($name, $desc, $gender, $birth_date, $death_date, $pub_wanted, $aliases, $xid, $xsrc, $xurl = null )
	{
		if ( isset($name, $xid, $xsrc) && strlen($name) && strlen($xid) && strlen($xsrc)) {
			$obj = $this->objectForExternal($xid, $xsrc);
			if ( $obj == false )
			{
				list($obj, $errors) = $this->createObject(array(
					"name" => $name,
					"desc" => $desc,
					"gender" => $gender,
					"birth_date" => $birth_date,
					"death_date" => $death_date,
					"pub_wanted" => $pub_wanted,
					"xid" => $xid,
					"xsource" => $xsrc,
					"xurl" => $xurl
					)
				);
				if ( is_array($errors) && count($errors) > 0) {
					throw \Exception("Errors creating new Artist " . var_export($errors, true) );
				}
			}
			else {
				$updates = array();

				if (isset($name) && is_null($name) == false && (isset($obj->name) == false || $name != $obj->name)) {
					$updates[Artist::name] = $name;
				}

				if (isset($gender) && is_null($name) == false && $gender != $obj->gender ) {
					$updates[Artist::gender] = $gender;
				}

				if (isset($desc) && is_null($gender) == false && $desc != $obj->desc ) {
					$updates[Artist::desc] = $desc;
				}

				if (isset($birth_date) && is_null($birth_date) == false && $birth_date != $obj->birth_date ) {
					$updates[Artist::birth_date] = $birth_date;
				}

				if (isset($death_date) && is_null($death_date) == false && $death_date != $obj->death_date ) {
					$updates[Artist::death_date] = $death_date;
				}

				if (isset($pub_wanted) && is_null($pub_wanted) == false && $pub_wanted != $obj->pub_wanted ) {
					$updates[Artist::pub_wanted] = $pub_wanted;
				}

				if ( isset($xid) && is_null($name) == false) {
					$updates["xid"] = $xid;
				}

				if ( isset($xsrc) && is_null($name) == false ) {
					$updates["xsource"] = $xsrc;
				}

				if ((isset($xurl) && strlen($xurl) > 0) && (isset($obj->xurl) == false || strlen($obj->xurl) == 0)) {
					$updates["xurl"] = $xurl;
				}

				if ( count($updates) > 0 ) {
					list($obj, $errors) = $this->updateObject($obj, $updates );
					if ( is_array($errors) && count($errors) > 0) {
						throw \Exception("Errors updating Artist " . var_export($errors, true) );
					}
				}
			}

			if ( $obj != false && is_array($aliases) ) {
				$alias_model = Model::Named("Artist_Alias");
				foreach ($aliases as $key => $value) {
					$alias_model->createAlias($obj, $value);
				}
			}
			return $obj;
		}

		return false;
	}
}

?>
