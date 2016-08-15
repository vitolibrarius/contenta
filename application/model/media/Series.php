<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \model\media\SeriesDBO as SeriesDBO;

/* import related objects */
use \model\media\Series_Alias as Series_Alias;
use \model\media\Series_AliasDBO as Series_AliasDBO;
use \model\media\Publisher as Publisher;
use \model\media\PublisherDBO as PublisherDBO;
use \model\media\Publication as Publication;
use \model\media\PublicationDBO as PublicationDBO;
use \model\media\Series_Character as Series_Character;
use \model\media\Series_CharacterDBO as Series_CharacterDBO;
use \model\media\Story_Arc_Series as Story_Arc_Series;
use \model\media\Story_Arc_SeriesDBO as Story_Arc_SeriesDBO;
use \model\media\User_Series as User_Series;
use \model\media\User_SeriesDBO as User_SeriesDBO;

class Series extends _Series
{
	/**
	 *	Create/Update functions
	 */
	public function createObject( array $values = array())
	{
		if ( isset($values) ) {
			if ( isset($values[Series::name]) && isset($values[Series::search_name]) == false) {
				$values[Series::search_name] = normalizeSearchString($values[Series::name]);
			}

			if ( isset($values['desc']) && strlen($values['desc']) > 0 ) {
				$values['desc'] = strip_tags($values['desc']);
			}
		}

		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof SeriesDBO ) {
			if ( isset($values[Series::name]) && isset($values[Series::search_name]) == false) {
				$values[Series::search_name] = normalizeSearchString($values[Series::name]);
			}

			if ( isset($values['desc']) && strlen($values['desc']) > 0 ) {
				$values['desc'] = strip_tags($values['desc']);
			}
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		$attrFor = array(
			Series::publisher_id,
			Series::name,
			Series::search_name,
			Series::desc,
			Series::start_year,
			Series::pub_active,
			Series::pub_wanted
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
		if ( Series::publisher_id == $attr ) {
			$model = Model::Named('Publisher');
			return $model->allObjects();
		}
		return null;
	}

	/** Validation */
/*
	function validate_publisher_id($object = null, $value)
	{
		return parent::validate_publisher_id($object, $value);
	}
*/

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
	function validate_search_name($object = null, $value)
	{
		return parent::validate_search_name($object, $value);
	}
*/

/*
	function validate_desc($object = null, $value)
	{
		return parent::validate_desc($object, $value);
	}
*/

/*
	function validate_start_year($object = null, $value)
	{
		return parent::validate_start_year($object, $value);
	}
*/

/*
	function validate_issue_count($object = null, $value)
	{
		return parent::validate_issue_count($object, $value);
	}
*/

/*
	function validate_pub_active($object = null, $value)
	{
		return parent::validate_pub_active($object, $value);
	}
*/

/*
	function validate_pub_wanted($object = null, $value)
	{
		return parent::validate_pub_wanted($object, $value);
	}
*/

/*
	function validate_pub_available($object = null, $value)
	{
		return parent::validate_pub_available($object, $value);
	}
*/

/*
	function validate_pub_cycle($object = null, $value)
	{
		return parent::validate_pub_cycle($object, $value);
	}
*/

/*
	function validate_pub_count($object = null, $value)
	{
		return parent::validate_pub_count($object, $value);
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

	public function findExternalOrCreate( $publishObj = null, $name, $year = 0, $count = 0, $xid, $xsrc, $xurl = null, $desc = null, $aliases = null )
	{
		$obj = $this->objectForExternal($xid, $xsrc);
		if ( $obj == false ) {
			list($obj, $errors) = $this->createObject( array(
				"publisher" => $publishObj,
				Series::name => $name,
				Series::start_year => (isset($year) ? intval($year) : null),
				Series::issue_count => $count,
				Series::xurl => $xurl,
				Series::xsource => $xsrc,
				Series::xid => $xid,
				Series::desc => $desc
				)
			);
			if ( is_array($errors) && count($errors) > 0) {
				throw \Exception("Errors creating new Series " . var_export($errors, true) );
			}
		}
		else {
			$updates = array();

			if ( isset($publishObj, $publishObj->id) && (isset($obj->publisher_id) == false || $publishObj->id != $obj->publisher_id) ) {
				$updates["publisher"] = $publishObj;
			}

			if (isset($name) && (isset($obj->name) == false || $name != $obj->name)) {
				$updates[Series::name] = $name;
			}

			if (isset($count) && is_numeric($count)) {
				$intcount = intval($count);
				if ( $intcount > 0 && $intcount != $obj->issue_count ) {
					$updates[Series::issue_count] = $intcount;
				}
			}

			if (isset($year) && is_numeric($year)) {
				$intyear = intval($year);
				if ( $intyear > 0 && $intyear != $obj->start_year ) {
					$updates[Series::start_year] = $intyear;
				}
			}

			if (isset($desc) && strlen($desc) > 0) {
				$updates[Series::desc] = $desc;
			}

			if ( isset($xid) ) {
				$updates["xid"] = $xid;
			}

			if ( isset($xsrc) ) {
				$updates["xsource"] = $xsrc;
			}

			if ((isset($xurl) && strlen($xurl) > 0) && (isset($obj->xurl) == false || strlen($obj->xurl) == 0)) {
				$updates["xurl"] = $xurl;
			}

			if ( count($updates) > 0 ) {
				list($obj, $errors)  = $this->updateObject( $obj, $updates );
				if ( is_array($errors) && count($errors) > 0) {
					throw \Exception("Errors creating new Series " . var_export($errors, true) );
				}
			}
		}

		if ( $obj != false && is_array($aliases) ) {
			$alias_model = Model::Named("Series_Alias");
			foreach ($aliases as $key => $value) {
				$alias_model->createAlias($obj, $value);
			}
		}

		return $obj;
	}

	public function updateStatistics( $xid = 0, $xsource = null )
	{
		$object = $this->objectForExternal($xid, $xsource);
		if ( $object instanceof model\SeriesDBO ) {
			$params = array( ":series_id" => $object->id );
			\SQL::raw( "update publication set media_count = (select count(*) from media where media.publication_id = publication.id)"
				. " where series_id = :series_id", $params);

			\SQL::raw( "update series set pub_count = "
				. " (select count(*) from publication where publication.series_id = series.id)"
				. " where id = :series_id", $params);

			\SQL::raw( "update series set pub_available = "
				. " (select count(*) from publication where publication.series_id = series.id AND publication.media_count > 0)"
				. " where id = :series_id", $params);

			\SQL::raw( "update series set pub_cycle = "
				. "CAST( (select (julianday(max(pub_date), 'unixepoch') - julianday(min(pub_date), 'unixepoch')) / count(*)"
				. " from publication where publication.series_id = series.id) as INT)"
				. " where id = :series_id", $params);

			\SQL::raw( "update series set pub_active = "
				. "(select case when max(p.pub_date) is null AND series.start_year = strftime('%Y','now') then 1 "
				. " when julianday(max(p.pub_date), 'unixepoch') > julianday('now') then 1"
				. " when julianday(max(pub_date), 'unixepoch') + (series.pub_cycle * 2) > julianday('now') then 1"
				. " else 0 end from publication p where p.series_id = series.id)"
				. " where id = :series_id", $params);
		}
		return true;
	}

}

?>
