<?php

namespace model;

use \Session as Session;
use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use db\Qualifier as Qualifier;

class Publication extends Model
{
	const TABLE =		'publication';
	const id =			'id';
	const series_id =	'series_id';
	const name =		'name';
	const desc =		'desc';
	const pub_date =	'pub_date';
	const created =		'created';
	const issue_num =	'issue_num';
	const xurl =		'xurl';
	const xsource =		'xsource';
	const xid =			'xid';
	const xupdated =	'xupdated';
	const media_count =	'media_count';

	public function tableName() { return Publication::TABLE; }
	public function tablePK() { return Publication::id; }
	public function sortOrder() { return array(Publication::name); }
	public function notifyKeypaths() { return array( "series", "story_arcs" ); }

	public function allColumnNames()
	{
		return array(
			Publication::id, Publication::name, Publication::desc, Publication::series_id, Publication::created,
			Publication::pub_date, Publication::issue_num,
			Publication::xurl, Publication::xsource, Publication::xid, Publication::xupdated,
			Publication::media_count
		);
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "media":
				case "story_arc_publication":
				case "publication_character":
					return array( Publication::id, "publication_id" );
					break;
				default:
					break;
			}
		}
		return parent::joinAttributes( $joinModel );
	}

	public function publicationForSeriesExternal(model\SeriesDBO $series = null, $issue_xid = null, $xsource = null)
	{
		$matches = $this->allObjectsForFKAndQualifier(Publication::series_id, $series, Qualifier::XID($issue_xid, $xsource));
		if ( is_array($matches) && count($matches) > 0 ) {
			return $matches[0];
		}
		return false;
	}

	public function findExternalOrCreate( $series = null, $name, $desc, $issue_num = null, $xid, $xsrc, $xurl = null )
	{
		if ( isset($name, $xid, $xsrc) && strlen($name) && strlen($xid) && strlen($xsrc)) {
			$obj = $this->objectForExternal($xid, $xsrc);
			if ( $obj == false ) {
				$obj = $this->create($series, $name, $desc, $issue_num, $xid, $xsrc, $xurl);
			}
			else {
				$updates = array();

				if ( isset($series, $series->id) && (isset($obj->series_id) == false || $series->id != $obj->series_id) ) {
					$updates[Publication::series_id] = $series->id;
				}

				if (isset($name) && (isset($obj->name) == false || $name != $obj->name)) {
					$updates[Publication::name] = $name;
				}

				if (isset($issue_num) && (isset($obj->issue_num) == false || $issue_num != $obj->issue_num)) {
					$updates[Publication::issue_num] = $issue_num;
				}

				if (isset($desc) && strlen($desc) > 0) {
					$desc = strip_tags($desc);
					if ( $desc != $obj->desc ) {
						$updates[Publication::desc] = $desc;
					}
				}

				if ( isset($xid) ) {
					$updates[Publication::xupdated] = time();

					if ((isset($xurl) && strlen($xurl) > 0) && (isset($obj->xurl) == false || strlen($obj->xurl) == 0)) {
						$updates[Publication::xurl] = $xurl;
					}
				}

				if ( count($updates) > 0 ) {
					$this->updateObject($obj, $updates );
				}
			}

			return $obj;
		}
		else {
			Logger::LogError( "Missing parameter $name, $xid, $xsrc" );
		}
		return false;
	}

	public function create( $series = null, $name, $desc, $issue_num = 0, $xid, $xsrc, $xurl = null )
	{
		$obj = $this->objectForExternal($xid, $xsrc);
		if ( $obj == false )
		{
			$params = array(
				Publication::created => time(),
				Publication::name => $name,
				Publication::desc => $desc,
				Publication::issue_num => $issue_num,
				Publication::xurl => $xurl,
				Publication::xsource => $xsrc,
				Publication::xid => $xid,
				Publication::media_count => 0
			);

			if ( isset($series)  && is_a($series, '\model\SeriesDBO')) {
				$params[Publication::series_id] = $series->id;
			}

			list( $obj, $errorList ) = $this->createObject($params);
			if ( is_array($errorList) ) {
				return $errorList;
			}
		}

		return $obj;
	}

	public function createObject(array $values = array())
	{
		if ( isset($values) ) {
			// no id (so not an update just in case) and no name set
			if ( isset($values[Publication::id]) == false && isset($values[Publication::name]) == false ) {
				if ( isset( $values[Publication::issue_num])) {
					$values[Publication::name] = "Issue " . $values[Publication::issue_num];
				}
				else {
					$values[Publication::name] = "Issue";
				}
			}
		}

		return parent::createObject($values);
	}

	public function allForSeries(model\SeriesDBO $obj = null)
	{
		return $this->allObjectsForFK(Publication::series_id, $obj);
	}

	public function deleteAllForSeries($obj)
	{
		$success = true;
		if ( $obj != false )
		{
			$array = $this->allForSeries($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new exceptions\DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForSeries($obj);
			}
		}
		return $success;
	}

	public function deleteObject( \DataObject $object = null)
	{
		if ( $object instanceof model\PublicationDBO )
		{
			$publication_char_model = Model::Named('Publication_Character');
			if ( $publication_char_model->deleteAllForPublication($object) == false ) {
				return false;
			}

			$publication_arc_model = Model::Named('Story_Arc_Publication');
			if ( $publication_arc_model->deleteAllForPublication($object) == false ) {
				return false;
			}

			$media_model = Model::Named('Media');
			if ( $media_model->deleteAllForPublication($object) ) {
				return parent::deleteObject($object);
			}
		}

		return false;
	}


	/* EditableModelInterface */
	function validate_name($object = null, $value)
	{
		if (empty($value))
		{
			return Localized::ModelValidation($this->tableName(), Publication::name, "FIELD_EMPTY");
		}
		else if (strlen($value) > 256 )
		{
			return Localized::ModelValidation($this->tableName(), Publication::name, "FIELD_TOO_LONG" );
		}
		return null;
	}

	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Publication::name,
				Publication::series_id
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesFor($object = null, $type = null ) {
		return array(
			Publication::name => Model::TEXT_TYPE,
			Publication::desc => Model::TEXTAREA_TYPE,
			Publication::issue_num => Model::INT_TYPE,
			Publication::series_id => Model::TO_ONE_TYPE
		);
	}

	public function attributeOptions($object = null, $type = null, $attr) {
		switch ($attr) {
			case Publication::series_id:
				$model = Model::Named('Series');
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
