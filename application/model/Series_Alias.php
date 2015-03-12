<?php

namespace model;

use \Session as Session;
use \DataObject as DataObject;
use \Model as Model;
use \Localized as Localized;

class Series_Alias extends Model
{
	const TABLE =		'series_alias';
	const id =			'id';
	const series_id =	'series_id';
	const name =		'name';


	public function tableName() { return Series_Alias::TABLE; }
	public function tablePK() { return Series_Alias::id; }
	public function sortOrder() { return array(Series_Alias::name); }

	public function dboClassName() { return 'model\\Series_AliasDBO'; }

	public function allColumnNames()
	{
		return array(Series_Alias::id, Series_Alias::series_id, Series_Alias::name);
	}

	public function allForSeries($obj)
	{
		return $this->fetchAll(Series_Alias::TABLE, $this->allColumns(), array(Series_Alias::series_id => $obj->id), array(Series_Alias::name));
	}

	public function allForName($name)
	{
		return $this->fetchAll(Series_Alias::TABLE,
			$this->allColumns(),
			array(Series_Alias::name => $name),
			array(Series_Alias::name));
	}

	public function forName($obj, $name)
	{
		return $this->fetch(Series_Alias::TABLE,
			$this->allColumns(),
			array(Series_Alias::series_id => $obj->id, Series_Alias::name => $name));
	}

	public function create($seriesObj, $name)
	{
		if (isset($seriesObj, $seriesObj->id, $name)) {
			$alias = $this->forName($seriesObj, $name);
			if ($alias == false) {
				$newObjId = $this->createObj(Series_Alias::TABLE, array(
					Series_Alias::series_id => $seriesObj->id,
					Series_Alias::name => $name
					)
				);
				$alias = ($newObjId != false ? $this->objectForId($newObjId) : false);
			}

			return $alias;
		}

		return false;
	}

	public function deleteAllForSeries($obj)
	{
		$success = true;
		if ( $obj != false )
		{
			$array = $this->allForSeries($obj);
			foreach ($array as $key => $value) {
				if ($this->deleteObject($value) == false) {
					$success = false;
					break;
				}
			}
		}
		return $success;
	}
}

?>
