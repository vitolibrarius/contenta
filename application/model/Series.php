<?php

namespace model;

use \http\Session as Session;;
use \DataObject as DataObject;
use \Model as Model;
use \Localized as Localized;
use \Logger as Logger;

use db\Qualifier as Qualifier;

class Series extends Model
{
	const TABLE =			'series';
	const id =				'id';
	const publisher_id =	'publisher_id';
	const parent_id =		'parent_id';
	const name =			'name';
	const search_name =		'search_name';
	const desc =			'desc';
	const created =			'created';
	const start_year =		'start_year';
	const issue_count =		'issue_count';
	const xurl =			'xurl';
	const xsource =			'xsource';
	const xid =				'xid';
	const xupdated =		'xupdated';

	const pub_active =		'pub_active';
	const pub_cycle =		'pub_cycle';
	const pub_count =		'pub_count';
	const pub_available = 	'pub_available';
	const pub_wanted =		'pub_wanted';

	public function tableName() { return Series::TABLE; }
	public function tablePK() { return Series::id; }
	public function sortOrder() { return array(Series::name, Series::start_year); }

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "publisher":
					return array( Series::publisher_id, "id" );
					break;
				case "publication":
				case "series_character":
				case "series_alias":
				case "story_arc":
				case "user_series":
					return array( Series::id, "series_id" );
					break;
				default:
					break;
			}
		}
		return parent::joinAttributes( $joinModel );
	}

	public function allColumnNames()
	{
		return array(
			Series::id, Series::parent_id, Series::publisher_id, Series::name, Series::search_name, Series::desc, Series::created,
			Series::start_year, Series::issue_count,
			Series::xurl, Series::xsource, Series::xid, Series::xupdated,
			Series::pub_active, Series::pub_cycle, Series::pub_count, Series::pub_available, Series::pub_wanted
		);
	}

	public function allForPublisher($obj)
	{
		return $this->allObjectsForFK(Series::publisher_id, $obj);
	}

	public function allForName($name)
	{
		return $this->allObjectsForKeyValue(Series::search_name, $name);
	}

	public function findExternalOrCreate( $publishObj = null, $name, $year = 0, $count = 0, $xid, $xsrc, $xurl = null, $desc = null, $aliases = null )
	{
		$obj = $this->objectForExternal($xid, $xsrc);
		if ( $obj == false ) {
			$obj = $this->create($publishObj, $name, $year, $count, $xid, $xsrc, $xurl, $desc);
		}
		else {
			$updates = array();

			if ( isset($publishObj, $publishObj->id) && (isset($obj->publisher_id) == false || $publishObj->id != $obj->publisher_id) ) {
				$updates[Series::publisher_id] = $publishObj->id;
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
				if ( $desc != $obj->desc ) {
					$updates[Series::desc] = strip_tags($desc);
				}
			}

			if ( count($updates) > 0 ) {
				$this->updateObject( $obj, $updates );
			}

			if ( $obj != false && is_array($aliases) ) {
				$alias_model = Model::Named("Series_Alias");
				foreach ($aliases as $key => $value) {
					$alias_model->create($obj, $value);
				}
			}

			return $this->refreshObject($obj);
		}
		return $obj;
	}

	public function seriesLike($partialName) {
		return \SQL::Select( $this )
			->where( Qualifier::Like( Series::search_name, normalizeSearchString($partialName), SQL::SQL_LIKE_AFTER ))
			->orderBy( $this->sortOrder() )
			->limit( 50 )
			->fetchAll();
	}

	public function create( $publishObj = null, $name, $year = null, $count = 0, $xid = null, $xsrc = null, $xurl = null, $desc = null, $aliases = null )
	{
		$obj = $this->objectForExternal($xid, $xsrc);
		if ( $obj == false )
		{
			$params = array(
				Series::created => time(),
				Series::name => $name,
				Series::search_name => normalizeSearchString($name),
				Series::desc => $desc,
				Series::start_year => (isset($year) ? intval($year) : null),
				Series::issue_count => $count,
				Series::xurl => $xurl,
				Series::xsource => $xsrc,
				Series::xid => $xid,
				Series::pub_active => Model::TERTIARY_TRUE,
				Series::pub_cycle => 0,
				Series::pub_count => 0,
				Series::pub_available => 0,
				Series::pub_wanted => Model::TERTIARY_FALSE
			);

			if ( isset($publishObj)  && is_a($publishObj, '\model\PublisherDBO')) {
				$params[Series::publisher_id] = $publishObj->id;
			}

			list( $obj, $errorList ) = $this->createObject($params);
			if ( is_array($errorList) ) {
				return $errorList;
			}
		}

		if ( $obj != false && is_array($aliases) ) {
			$alias_model = Model::Named("Series_Alias");
			foreach ($aliases as $key => $value) {
				$alias_model->create($obj, $value);
			}
		}

		return $obj;
	}

	public function deleteObject( \DataObject $object = null)
	{
		if ( $object instanceof model\SeriesDBO )
		{
			$series_alias_model = Model::Named('Series_Alias');
			if ( $series_alias_model->deleteAllForSeries($object) == false ) {
				return false;
			}

			$series_char_model = Model::Named('Series_Character');
			if ( $series_char_model->deleteAllForSeries($object) == false ) {
				return false;
			}

			$series_arc_model = Model::Named('Story_Arc_Series');
			if ( $series_arc_model->deleteAllForSeries($object) == false ) {
				return false;
			}

			$user_series_model = Model::Named('User_Series');
			if ( $user_series_model->deleteAllForSeries($object) == false ) {
				return false;
			}

			$pub_model = Model::Named('Publication');
			if ( $pub_model->deleteAllForSeries($object) ) {
				return parent::deleteObject($object);
			}
		}

		return false;
	}

	public function parentObject($seriesObj)
	{
		if ( $seriesObj != false && $seriesObj->parent_id != null)
		{
			return $this->objectForId($seriesObj->parent_id);
		}

		return false;
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


	/* EditableModelInterface */
	function validate_name($object = null, $value)
	{
		if (empty($value))
		{
			return Localized::ModelValidation($this->tableName(), Series::name, "FIELD_EMPTY");
		}
		else if (strlen($value) > 256 )
		{
			return Localized::ModelValidation($this->tableName(), Series::name, "FIELD_TOO_LONG" );
		}
		return null;
	}

	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Series::name
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesFor($object = null, $type = null ) {
		return array(
			Series::name => Model::TEXT_TYPE,
			Series::search_name => Model::TEXT_TYPE,
			Series::start_year => Model::INT_TYPE,
			Series::desc => Model::TEXTAREA_TYPE,
			Series::publisher_id => Model::TO_ONE_TYPE,
			Series::pub_active => Model::FLAG_TYPE,
			Series::pub_wanted => Model::FLAG_TYPE
		);
	}

	public function attributeOptions($object = null, $type = null, $attr) {
		switch ($attr) {
			case Series::publisher_id:
				$model = Model::Named('Publisher');
				return $model->allObjects(null, -1);
			default:
				return null;
		}
		return null;
	}

	public function attributeRestrictionMessage($object = null, $type = null, $attr)
	{
		return null;
	}

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) == false || is_null($object) == true) {
			switch ($attr) {
				case Series::pub_active:
					return Model::TERTIARY_TRUE;
				case Series::pub_wanted:
					return Model::TERTIARY_FALSE;
			}
		}
		return parent::attributeDefaultValue($object, $type, $attr);
	}

	public function attributeIsEditable($object = null, $type = null, $attr)
	{
		if ( is_null($object) == false && $object->hasExternalEndpoint()) {
			switch ($attr) {
				case Series::publisher_id:
				case Series::name:
				case Series::desc:
				case Series::start_year:
				case Series::issue_count:
				case Series::xurl:
				case Series::xsource:
				case Series::xid:
				case Series::xupdated:
					return false;
				default:
					break;
			}
		}
		return parent::attributeIsEditable($object, $type, $attr);
	}
}

?>
