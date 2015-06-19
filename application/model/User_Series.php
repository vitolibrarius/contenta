<?php

namespace model;

use \Session as Session;
use \DataObject as DataObject;
use \Model as Model;
use \Localized as Localized;

class User_Series extends Model
{
	const TABLE =		'user_series';
	const id =			'id';
	const user_id =		'user_id';
	const series_id =	'series_id';
	const favorite =	'favorite';
	const read =		'read';
	const mislabeled =	'mislabeled';


	public function tableName() { return User_Series::TABLE; }
	public function tablePK() { return User_Series::id; }
	public function sortOrder() { return array(User_Series::user_id, User_Series::series_id); }

	public function allColumnNames()
	{
		return array(User_Series::id, User_Series::user_id, User_Series::series_id,
			User_Series::favorite, User_Series::read, User_Series::mislabeled);
	}

	public function joinForUserAndSeries($user, $series)
	{
		if (isset($user, $user->id, $series, $series->id)) {
			$join = db\Qualifier::AndQualifier(
				db\Qualifier::FK( User_Series::user_id, $user ),
				db\Qualifier::FK( User_Series::series_id, $series )
			);
			return $this->singleObject( $join );
		}

		return false;
	}

	public function allForUser(model\UsersDBO $obj)
	{
		return $this->allObjectsForFK(User_Series::user_id, $obj);
	}

	public function allFavoritesForUser(model\UsersDBO $obj)
	{
		return $this->allObjectsForFKWithValue(User_Series::user_id, $obj, User_Series::favorite, Model::TERTIARY_TRUE);
	}

	public function allSeriesForUser(model\UsersDBO $obj)
	{
		$joins = $this->allForUser($obj);

		if ( $joins != false ) {
			$series_model = Model::Named('Series');
			return $series_model->fetchAllJoin(
				Series::TABLE,
				$series_model->allColumns(),
				Series::id, User_Series::series_id, $joins, null, array(Series::name));
		}

		return false;
	}

	public function allForSeries(model\SeriesDBO $obj)
	{
		return $this->allObjectsForFK(User_Series::series_id, $obj);
	}

	public function allMislabled()
	{
		return $this->allForUserSeriesWithFlag( null, null, null, Model::TERTIARY_TRUE);
	}

	public function allForUserSeriesWithFlag($user = null, $series  = null, $favorite = null, $read = null, $label = null)
	{
		$qualifiers = array();
		if ( is_a($user, "usersDataObject") ) {
			$qualifiers[] = db\Qualifier::Equals( User_Series::user_id, $user->id );
		}

		if ( is_a($series, "seriesDataObject") ) {
			$qualifiers[] = db\Qualifier::Equals( User_Series::series_id, $series->id);
		}

		if ( is_null($favorite) != true ) {
			$qualifiers[] = db\Qualifier::Equals( User_Series::favorite, $favorite);
		}

		if ( is_null($read) != true ) {
			$qualifiers[] = db\Qualifier::Equals( User_Series::read, $read);
		}

		if ( is_null($label) != true ) {
			$qualifiers[] = db\Qualifier::Equals( User_Series::mislabeled, $label);
		}

		return \SQL::Select( $this)->where( db\Qualifier::AndQualifier( $qualifiers ))->fetchAll();
	}

	public function create($user, $series, $favorite = null, $read = null, $label = null)
	{
		if (isset($user, $user->id, $series, $series->id)) {
			$join = $this->joinForUserAndSeries($user, $series);
			if ($join == false) {
				$newObjId = $this->createObj(User_Series::TABLE, array(
					User_Series::series_id => $series->id,
					User_Series::user_id => $user->id,
					User_Series::favorite => (is_null($favorite) ? Model::TERTIARY_UNSET : $favorite),
					User_Series::read => (is_null($read) ? Model::TERTIARY_UNSET : $read),
					User_Series::mislabeled => (is_null($label) ? Model::TERTIARY_UNSET : $label)
					)
				);
				$join = ($newObjId != false ? $this->objectForId($newObjId) : false);
			}
			else {
				$join = $this->flagJoin($join, $favorite, $read, $label);
			}

			return $join;
		}

		return false;
	}

	public function flagJoin($join = null, $favorite = null, $read = null, $label = null)
	{
		if ( isset($join) && is_a($join, "user_seriesDataObject") ) {
			$updates = array();
			if ( is_null($favorite) == false && $join->favorite != $favorite ) {
				$updates[User_Series::favorite]  = $favorite;
			}

			if ( is_null($read) == false && $join->read != $read ) {
				$updates[User_Series::read]  = $read;
			}

			if ( is_null($label) == false && $join->mislabeled != $label ) {
				$updates[User_Series::mislabeled]  = $label;
			}

			if ( array_count_values($updates) > 0 ) {
				if ($this->update(User_Series::TABLE, $updates, array(User_Series::id => $join->id)))
				{
					return $this->refresh($join);
				}
			}
		}
		return $join;
	}

	public function deleteObject( \DataObject $obj = null)
	{
		if ( $obj instanceof model\User_SeriesDBO )
		{
			return $this->deleteObj($obj, User_Series::TABLE, User_Series::id );
		}

		return false;
	}

	public function deleteAllForUser($obj)
	{
		$success = true;
		if ( $obj != false )
		{
			$array = $this->allForUser($obj);
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
