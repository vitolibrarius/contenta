<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \model\media\PublicationDBO as PublicationDBO;

/* import related objects */
use \model\media\Series as Series;
use \model\media\SeriesDBO as SeriesDBO;
use \model\media\Media as Media;
use \model\media\MediaDBO as MediaDBO;
use \model\media\Story_Arc_Publication as Story_Arc_Publication;
use \model\media\Story_Arc_PublicationDBO as Story_Arc_PublicationDBO;
use \model\media\Publication_Character as Publication_Character;
use \model\media\Publication_CharacterDBO as Publication_CharacterDBO;

class Publication extends _Publication
{
	public function notifyKeypaths() { return array( "series", "story_arcs" ); }

	/**
	 *	Create/Update functions
	 */
	public function createObject( array $values = array())
	{
		if ( isset($values) ) {
			if ( isset($values['desc']) && strlen($values['desc']) > 0 ) {
				$values['desc'] = strip_tags($values['desc']);
			}

			if ( isset($values[Publication::name]) == false ) {
				$values[Publication::name] = "Issue" . (isset($values[Publication::issue_num]) ? " " . $values[Publication::issue_num] : "");
			}

			if ( isset($values[Publication::issue_num]) == true ) {
				$values[Publication::issue_order] = intval(floatval($values[Publication::issue_num]) * 10);
			}
		}

		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof PublicationDBO ) {
			if ( isset($values['desc']) && strlen($values['desc']) > 0 ) {
				$values['desc'] = strip_tags($values['desc']);
			}

			if ( isset($values[Publication::issue_num]) == true ) {
				$values[Publication::issue_order] = intval(floatval($values[Publication::issue_num]) * 10);
			}
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		$attrFor = array(
			Publication::series_id,
			Publication::name,
			Publication::desc,
			Publication::pub_date,
			Publication::issue_num
		);
		return array_intersect_key($this->attributesMap(),array_flip($attrFor));
	}

	public function attributeIsEditable($object = null, $type = null, $attr)
	{
		if (isset($object) && $object instanceof PublicationDBO ) {
			if ( isset($object->xid, $object->xsource) && is_null($object->xid) == false ) {
				return false;
			}
		}
		// add customization here
		return parent::attributeIsEditable($object, $type, $attr);
	}

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
		if ( Publication::series_id == $attr ) {
			$model = Model::Named('Series');
			return $model->allObjects();
		}
		return null;
	}

	/** Validation */
/*
	function validate_series_id($object = null, $value)
	{
		return parent::validate_series_id($object, $value);
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
	function validate_issue_num($object = null, $value)
	{
		return parent::validate_issue_num($object, $value);
	}
*/

/*
	function validate_media_count($object = null, $value)
	{
		return parent::validate_media_count($object, $value);
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
	public function findExternalOrCreate( $series = null, $name, $desc, $issue_num = null, $xid, $xsrc, $xurl = null )
	{
		if ( isset($name, $xid, $xsrc) && strlen($name) && strlen($xid) && strlen($xsrc)) {
			$obj = $this->objectForExternal($xid, $xsrc);
			if ( $obj == false ) {
				list($obj, $errors) = $this->createObject(array(
					"series" => $series,
					"name" => $name,
					"desc" => $desc,
					"issue_num" => $issue_num,
					"xid" => $xid,
					"xsource" => $xsrc,
					"xurl" => $xurl
					)
				);
				if ( is_array($errors) && count($errors) > 0) {
					throw \Exception("Errors creating new Series " . var_export($errors, true) );
				}
			}
			else {
				$updates = array();

				if ( isset($series, $series->id) && (isset($obj->series_id) == false || $series->id != $obj->series_id) ) {
					$updates["series"] = $series;
				}

				if (isset($name) && (isset($obj->name) == false || $name != $obj->name)) {
					$updates[Publication::name] = $name;
				}

				if (isset($issue_num) && (isset($obj->issue_num) == false || $issue_num != $obj->issue_num)) {
					$updates[Publication::issue_num] = $issue_num;
				}

				if (isset($desc) && strlen($desc) > 0) {
					$updates[Publication::desc] = $desc;
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
					list($obj, $errors) = $this->updateObject($obj, $updates );
					if ( is_array($errors) && count($errors) > 0) {
						throw \Exception("Errors creating new Publication " . var_export($errors, true) );
					}
				}
			}

			return $obj;
		}
		else {
			Logger::LogError( "Missing parameter $name, $xid, $xsrc" );
		}
		return false;
	}

	public function allObjectsNeedingExternalUpdate($limit = -1)
	{
		$series_model = Model::Named('Series');
		$saj_model = Model::Named('Story_Arc_Publication');
		$qualifiers[] = Qualifier::OrQualifier(
			Qualifier::Equals( Publication::media_count, 0 ),
			Qualifier::IsNull( Publication::media_count )
		);
		$qualifiers[] = Qualifier::OrQualifier(
			Qualifier::InSubQuery( Publication::series_id,
				SQL::Select($series_model, array("id"))->where(Qualifier::Equals( "pub_wanted", Model::TERTIARY_TRUE ))->limit(0)
			),
			Qualifier::InSubQuery( Publication::id,
				SQL::SelectJoin($saj_model, array("publication_id"))
					->joinOn( $saj_model, Model::Named("Story_Arc"), null, Qualifier::Equals( "pub_wanted", Model::TERTIARY_TRUE))
					->limit(0)
			)
		);
		$qualifiers[] = Qualifier::IsNotNull( "xid" );
		$qualifiers[] = Qualifier::OrQualifier(
			Qualifier::IsNull( "xupdated" ),
			Qualifier::LessThan( "xupdated", (time() - (3600 * 24 * 7)) )
		);

		$select = SQL::Select($this);
		if ( count($qualifiers) > 0 ) {
			$select->where( Qualifier::AndQualifier( $qualifiers ));
		}
		$select->orderBy( $this->sortOrder() );
		$select->limit( $limit );
		$wantedFirst = $select->fetchAll();
		if ( is_array($wantedFirst) && count($wantedFirst) > 0) {
			return $wantedFirst;
		}

		return parent::allObjectsNeedingExternalUpdate($limit);
	}

	public function publicationsLike($seriesName = '', $issue = null, $year = null)
	{
		if ( is_string($seriesName) && strlen($seriesName) ) {
			$qualifiers = array();

			if ( isset($issue) && strlen($issue) > 0) {
				$qualifiers[] = Qualifier::Equals( Publication::issue_num, $issue );
			}
			if ( isset($year) && strlen($year) == 4 ) {
				$start = strtotime("01-01-" . $year . " 00:00");
				$end = strtotime("31-12-" . $year . " 23:59");
				$qualifiers[] = Qualifier::Between( Publication::pub_date, $start, $end );
			}

			if ( strlen($seriesName) > 0 ) {
				$searchName = normalizeSearchString( $seriesName );
				$qualifiers[] = Qualifier::InSubQuery( Publication::series_id,
					SQL::Select(Model::Named("Series"), array("id"))->where(Qualifier::Like( Series::search_name, $searchName ))->limit(0)
				);
			}

			if ( count($qualifiers) > 0 ) {
				$select = SQL::Select($this);
				$select->where( Qualifier::AndQualifier( $qualifiers ));
				$select->orderBy( $this->sortOrder() );
				return $select->fetchAll();
			}
		}
		return false;
	}

	public function activePublicationsForSeries(SeriesDBO $obj = null)
	{
		return $this->allObjectsForFKAndQualifier(Publication::series_id, $obj, Qualifier::GreaterThan(Publication::media_count, 0));
	}

	public function publicationForSeriesExternal(SeriesDBO $series = null, $issue_xid = null, $xsource = null)
	{
		$matches = $this->allObjectsForFKAndQualifier(Publication::series_id, $series, Qualifier::XID($issue_xid, $xsource));
		if ( is_array($matches) && count($matches) > 0 ) {
			return $matches[0];
		}
		return false;
	}
}

?>
