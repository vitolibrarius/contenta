<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;
use \Localized as Localized;
use \Logger as Logger;

class Series_Alias extends Model
{
	const TABLE =		'series_alias';
	const id =			'id';
	const series_id =	'series_id';
	const name =		'name';


	public function tableName() { return Series_Alias::TABLE; }
	public function tablePK() { return Series_Alias::id; }
	public function sortOrder() { return array(Series_Alias::name); }

	public function allColumnNames()
	{
		return array(Series_Alias::id, Series_Alias::series_id, Series_Alias::name);
	}

	public function allForSeries($obj)
	{
		return $this->allObjectsForFK(Series_Alias::series_id, $obj);
	}

	public function allForName($name)
	{
		return $this->allObjectsForKeyValue(Series_Alias::name, $name);
	}

	public function forName($obj, $name)
	{
		return $this->allObjectsForFKWithValue(Series_Alias::series_id, $obj, Series_Alias::name, $name);
	}

	public function create($seriesObj, $name)
	{
		if (isset($seriesObj, $seriesObj->id, $name)) {
			$alias = $this->forName($seriesObj, $name);
			if ($alias == false) {
				$params = array(
					Series_Alias::series_id => $seriesObj->id,
					Series_Alias::name => $name
				);
				list( $alias, $errorList ) = $this->createObject($params);
				if ( is_array($errorList) ) {
					return $errorList;
				}
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
			while ( is_array($array) && count( $array) > 0 ) {
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
}

?>
