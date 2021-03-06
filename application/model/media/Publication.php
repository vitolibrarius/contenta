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

use \model\reading\Reading_QueueDBO as Reading_QueueDBO;

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
			Qualifier::IsNull( Publication::media_count ),
			Qualifier::IsNull( Publication::pub_date )
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
		$sortOrder = array(
			array( 'asc' => Publication::xupdated),
			array( 'asc' => Publication::created),
			array( 'asc' => Publication::pub_date)
		);
		$select->orderBy( $sortOrder );
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

	public function allForReadingQueue( Reading_QueueDBO $queue, $unreadOnly = true, $limit = 50 )
	{
		if ( $queue != null ) {
			if ( isset($queue->series_id) ) {
				try {
					$result = \SQL::raw( "select p.id from publication p"
							. " left join reading_item r on p.id = r.publication_id and r.user_id = :usr"
							. " where p.series_id = :sid" . (boolValue($unreadOnly, true) ? " and r.read_date is null" : "")
						, array( ":usr" => $queue->user_id, ":sid" => $queue->series_id)
					);
				}
				catch( \Exception $e ) {
					Logger::logException($e);
				}
			}
			else if (isset($queue->story_arc_id)) {
				try {
					$result = \SQL::raw( "select p.id from publication p"
							. " join story_arc_publication sap on sap.publication_id = p.id"
							. " left join reading_item r on p.id = r.publication_id and r.user_id = :usr"
							. " where sap.story_arc_id = :said" . (boolValue($unreadOnly, true) ? " and r.read_date is null" : "")
						, array( ":usr" => $queue->user_id, ":said" => $queue->story_arc_id)
					);
				}
				catch( \Exception $e ) {
					Logger::logException($e);
				}
			}
			else {
				throw new \Exception("Bad queue object " . $queue->__toString());
			}

			if ( is_array($result) && count($result) > 0 ) {
				$publication_idArray = array_map(function($stdClass) {return $stdClass->{Publication::id}; }, $result);

				$select = \SQL::Select( $this )->where( Qualifier::IN( Publication::id, $publication_idArray ));
				$select->limit = $limit;
				return $select->fetchAll();
			}
		}
		return false;
	}

	public function countQueueList()
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

		$count = SQL::Count( $this, null, Qualifier::AndQualifier( $qualifiers ) )->fetch();
		return ($count == false ? 0 : $count->count);
	}

	private function searchQueueListForPublishedAge($ageInMonths = 0, $limit = SQL::SQL_DEFAULT_LIMIT) {
		$limit = max(intval( $limit ), 0);
		$months = intval($ageInMonths);
		$series_model = Model::Named('Series');
		$saj_model = Model::Named('Story_Arc_Publication');

		// base qualifiers
		$qualifiers[] = Qualifier::AndQualifier(
			Qualifier::IsNotNull( Publication::issue_num ),
			Qualifier::IsNotNull( Publication::pub_date )
		);
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

		if ( $months == 0 ) {
			$qualifiers[] = Qualifier::IsNull( Publication::search_date );
		}
		else {
			// don't repeat for at least 2 days
			$qualifiers[] = Qualifier::OrQualifier(
				Qualifier::LessThan( Publication::search_date, (time() - (3600 * 24 * 2)) ),
				Qualifier::IsNull( Publication::search_date )
			);

			// restrict to publications that are no more than X months old
			$qualifiers[] = Qualifier::GreaterThan( "pub_date", (time() - (3600 * 24 * (30 * $months))) );
		}

		$select = SQL::Select($this)
			->where( Qualifier::AndQualifier( $qualifiers ))
			->orderBy( array( array(SQL::SQL_ORDER_DESC => Publication::pub_date)))
			->limit( $limit );

		return $select->fetchAll();
	}

	public function searchQueueList( $limit = SQL::SQL_DEFAULT_LIMIT )
	{
		$limit = intval( $limit );
		$results = array();
		if ( $limit <= 0 ) {
			// fetch all
			$results = $this->searchQueueListForPublishedAge( -1, $limit);
		}
		else {
			// ranges are (never searched before, published in last 1,3,6,12,24 months, anything else)
			$pubRanges = array(0, 1, 3, 6, 12, 24, -1);
			foreach ( $pubRanges as $range ) {
				$more_results = $this->searchQueueListForPublishedAge( $range, $limit - count($results));
				//Logger::LogInfo( "Searching pubs $range, results so far: " . count($results) . " found more " . count($more_results));

				$results = array_unique(array_merge($results, $more_results));
				if (($limit - count($results)) <= 0 ) {
					break;
				}
			}
		}
		return $results;
	}
}

?>
