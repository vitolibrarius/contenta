<?php

namespace model;

use \Session as Session;
use \DataObject as DataObject;
use \Model as Model;
use \Localized as Localized;
use \Logger as Logger;

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

	public function tableName() { return Publication::TABLE; }
	public function tablePK() { return Publication::id; }
	public function sortOrder() { return array(Publication::name); }

	public function allColumnNames()
	{
		return array(
			Publication::id, Publication::name, Publication::desc, Publication::series_id, Publication::created,
			Publication::pub_date, Publication::issue_num,
			Publication::xurl, Publication::xsource, Publication::xid, Publication::xupdated
		);
	}

	public function findExternalOrCreate( $series = null, $name, $desc, $xid, $xsrc, $xurl = null )
	{
		if ( isset($name, $xid, $xsrc) && strlen($name) && strlen($xid) && strlen($xsrc)) {
			$obj = $this->objectForExternal($xid, $xsrc);
			if ( $obj == false ) {
				$obj = $this->create($series, $name, $desc, $xid, $xsrc, $xurl);
			}
			else {
				$updates = array();

				if ( isset($series, $series->id) && (isset($obj->series_id) == false || $series->id != $obj->series_id) ) {
					$updates[Publication::series_id] = $series->id;
				}

				if (isset($name) && (isset($obj->name) == false || $name != $obj->name)) {
					$updates[Publication::name] = $name;
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
		return false;
	}

	public function create( $series = null, $name, $desc, $xid, $xsrc, $xurl = null )
	{
		$obj = $this->objectForExternal($xid, $xsrc);
		if ( $obj == false )
		{
			$params = array(
				Publication::created => time(),
				Publication::name => $name,
				Publication::desc => $desc,
				Publication::xurl => $xurl,
				Publication::xsource => $xsrc,
				Publication::xid => $xid
			);

			if ( isset($series)  && is_a($series, '\model\SeriesDBO')) {
				$params[Publication::series_id] = $series->id;
			}

			$objectOrErrors = $this->createObject($params);
			if ( is_array($objectOrErrors) ) {
				return $objectOrErrors;
			}
			else if ($objectOrErrors != false) {
				$obj = $this->objectForId( (string)$objectOrErrors);
			}
		}

		return $obj;
	}

	public function allForSeries(model\SeriesDBO $obj = null)
	{
		return $this->fetchAll(Publication::TABLE, $this->allColumns(), array(Publication::series_id => $obj->id), $this->sortOrder());
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
