<?php

namespace model;

use \Session as Session;
use \DataObject as DataObject;
use \Model as Model;
use \Localized as Localized;

use db\Qualifier as Qualifier;

class Series extends Model
{
	const TABLE =			'series';
	const id =				'id';
	const publisher_id =	'publisher_id';
	const parent_id =		'parent_id';
	const name =			'name';
	const desc =			'desc';
	const created =			'created';
	const start_year =		'start_year';
	const issue_count =		'issue_count';
	const xurl =			'xurl';
	const xsource =			'xsource';
	const xid =				'xid';


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
			Series::id, Series::parent_id, Series::publisher_id, Series::name, Series::desc, Series::created,
			Series::start_year, Series::issue_count,
			Series::xurl, Series::xsource, Series::xid
		);
	}

	public function allForPublisher($obj)
	{
		return $this->allObjectsForFK(Series::publisher_id, $obj);
	}

	public function allForName($name)
	{
		return $this->allObjectsForKeyValue(Series::name, $name);
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
			->where( Qualifier::LikeQualifier( Series::name, $partialName . '*' ))
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
				Series::desc => $desc,
				Series::start_year => (isset($year) ? intval($year) : null),
				Series::issue_count => $count,
				Series::xurl => $xurl,
				Series::xsource => $xsrc,
				Series::xid => $xid
			);

			if ( isset($publishObj)  && is_a($publishObj, '\model\PublisherDBO')) {
				$params[Series::publisher_id] = $publishObj->id;
			}

			$objectOrErrors = $this->createObject($params);
			if ( is_array($objectOrErrors) ) {
				return $objectOrErrors;
			}
			else if ($objectOrErrors != false) {
				$obj = $this->objectForId( (string)$objectOrErrors);
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
			if ( $series_alias_model->deleteAllForSeries($object) ) {
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
			Series::start_year => Model::INT_TYPE,
			Series::desc => Model::TEXTAREA_TYPE,
			Series::publisher_id => Model::TO_ONE_TYPE
		);
	}

	public function attributeOptions($object = null, $type = null, $attr) {
		switch ($attr) {
			case Series::publisher_id:
				$model = Model::Named('Publisher');
				return $model->allObjects();
			default:
				return null;
		}
		return null;
	}

	public function attributeRestrictionMessage($object = null, $type = null, $attr)
	{
		return null;
	}
}

?>
