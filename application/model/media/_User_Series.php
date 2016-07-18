<?php

namespace model\media;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\media\User_SeriesDBO as User_SeriesDBO;

/* import related objects */
use \model\user\Users as Users;
use \model\user\UsersDBO as UsersDBO;
use \model\media\Series as Series;
use \model\media\SeriesDBO as SeriesDBO;

/** Sample Creation script */
		/** USER_SERIES */
/*
		$sql = "CREATE TABLE IF NOT EXISTS user_series ( "
			. User_Series::id . " INTEGER PRIMARY KEY, "
			. User_Series::user_id . " INTEGER, "
			. User_Series::series_id . " INTEGER, "
			. User_Series::favorite . " INTEGER, "
			. User_Series::read . " INTEGER, "
			. User_Series::mislabeled . " INTEGER, "
			. "FOREIGN KEY (". User_Series::user_id .") REFERENCES " . Users::TABLE . "(" . Users::id . "),"
			. "FOREIGN KEY (". User_Series::series_id .") REFERENCES " . Series::TABLE . "(" . Series::id . ")"
		. ")";
		$this->sqlite_execute( "user_series", $sql, "Create table user_series" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS user_series_user_idseries_id on user_series (user_id,series_id)';
		$this->sqlite_execute( "user_series", $sql, "Index on user_series (user_id,series_id)" );
*/
abstract class _User_Series extends Model
{
	const TABLE = 'user_series';

	// attribute keys
	const id = 'id';
	const user_id = 'user_id';
	const series_id = 'series_id';
	const favorite = 'favorite';
	const read = 'read';
	const mislabeled = 'mislabeled';

	// relationship keys
	const user = 'user';
	const series = 'series';

	public function tableName() { return User_Series::TABLE; }
	public function tablePK() { return User_Series::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => User_Series::user_id)
		);
	}

	public function allColumnNames()
	{
		return array(
			User_Series::id,
			User_Series::user_id,
			User_Series::series_id,
			User_Series::favorite,
			User_Series::read,
			User_Series::mislabeled
		);
	}

	/**
	 *	Simple fetches
	 */







	/**
	 * Simple relationship fetches
	 */
	public function allForUser($obj)
	{
		return $this->allObjectsForFK(User_Series::user_id, $obj, $this->sortOrder(), 50);
	}

	public function countForUser($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( User_Series::user_id, $obj );
		}
		return false;
	}
	public function allForSeries($obj)
	{
		return $this->allObjectsForFK(User_Series::series_id, $obj, $this->sortOrder(), 50);
	}

	public function countForSeries($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( User_Series::series_id, $obj );
		}
		return false;
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "users":
					return array( User_Series::user_id, "id"  );
					break;
				case "series":
					return array( User_Series::series_id, "id"  );
					break;
				default:
					break;
			}
		}
		return parent::joinAttributes( $joinModel );
	}

	/**
	 *	Create/Update functions
	 */
	public function createObject( array $values = array() )
	{
		if ( isset($values) ) {

			// default values for attributes
			if ( isset($values['favorite']) == false ) {
				$default_favorite = $this->attributeDefaultValue( null, null, User_Series::favorite);
				if ( is_null( $default_favorite ) == false ) {
					$values['favorite'] = $default_favorite;
				}
			}
			if ( isset($values['read']) == false ) {
				$default_read = $this->attributeDefaultValue( null, null, User_Series::read);
				if ( is_null( $default_read ) == false ) {
					$values['read'] = $default_read;
				}
			}
			if ( isset($values['mislabeled']) == false ) {
				$default_mislabeled = $this->attributeDefaultValue( null, null, User_Series::mislabeled);
				if ( is_null( $default_mislabeled ) == false ) {
					$values['mislabeled'] = $default_mislabeled;
				}
			}

			// default conversion for relationships
			if ( isset($values['user']) ) {
				$local_user = $values['user'];
				if ( $local_user instanceof UsersDBO) {
					$values[User_Series::user_id] = $local_user->id;
				}
				else if ( is_integer( $local_user) ) {
					$params[User_Series::user_id] = $local_user;
				}
			}
			if ( isset($values['series']) ) {
				$local_series = $values['series'];
				if ( $local_series instanceof SeriesDBO) {
					$values[User_Series::series_id] = $local_series->id;
				}
				else if ( is_integer( $local_series) ) {
					$params[User_Series::series_id] = $local_series;
				}
			}
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof User_Series ) {
			if ( isset($values['user']) ) {
				$local_user = $values['user'];
				if ( $local_user instanceof UsersDBO) {
					$values[User_Series::user_id] = $local_user->id;
				}
				else if ( is_integer( $local_user) ) {
					$params[User_Series::user_id] = $values['user'];
				}
			}
			if ( isset($values['series']) ) {
				$local_series = $values['series'];
				if ( $local_series instanceof SeriesDBO) {
					$values[User_Series::series_id] = $local_series->id;
				}
				else if ( is_integer( $local_series) ) {
					$params[User_Series::series_id] = $values['series'];
				}
			}
		}

		return parent::updateObject($object, $values);
	}

	/**
	 *	Delete functions
	 */
	public function deleteObject( DataObject $object = null)
	{
		if ( $object instanceof User_SeriesDBO )
		{
			// does not own user Users
			// does not own series Series
			return parent::deleteObject($object);
		}

		return false;
	}

	public function deleteAllForUser(UsersDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForUser($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForUser($obj);
			}
		}
		return $success;
	}
	public function deleteAllForSeries(SeriesDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForSeries($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForSeries($obj);
			}
		}
		return $success;
	}

	/**
	 * Named fetches
	 */
	public function objectForUserAndSeries(UsersDBO $user,SeriesDBO $series )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		$qualifiers[] = Qualifier::FK( 'user_id', $user);
		$qualifiers[] = Qualifier::FK( 'series_id', $series);

		if ( count($qualifiers) > 0 ) {
			$select->where( Qualifier::Combine( 'AND', $qualifiers ));
		}

		$result = $select->fetchAll();
		if ( is_array($result) ) {
			$result_size = count($result);
			if ( $result_size == 1 ) {
				return $result[0];
			}
			else if ($result_size > 1 ) {
				throw new \Exception( "objectForUserAndSeries expected 1 result, but fetched " . count($result) );
			}
		}

		return false;
	}

	public function allForUserFavorites(UsersDBO $user, $favorite, $read )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		$qualifiers[] = Qualifier::FK( 'user_id', $user);
		if ( isset($favorite)) {
			$qualifiers[] = Qualifier::Equals( 'favorite', $favorite);
		}
		if ( isset($read)) {
			$qualifiers[] = Qualifier::Equals( 'read', $read);
		}

		if ( count($qualifiers) > 0 ) {
			$select->where( Qualifier::Combine( 'AND', $qualifiers ));
		}

		$result = $select->fetchAll();
		return $result;
	}


	/**
	 * Attribute editing
	 */

	public function attributesMap() {
		return array(
			User_Series::user_id => Model::TO_ONE_TYPE,
			User_Series::series_id => Model::TO_ONE_TYPE,
			User_Series::favorite => Model::FLAG_TYPE,
			User_Series::read => Model::FLAG_TYPE,
			User_Series::mislabeled => Model::FLAG_TYPE
		);
	}

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) === false || is_null($object) == true) {
			switch ($attr) {
				case User_Series::favorite:
					return Model::TERTIARY_TRUE;
				case User_Series::read:
					return Model::TERTIARY_FALSE;
				case User_Series::mislabeled:
					return Model::TERTIARY_FALSE;
			}
		}
		return parent::attributeDefaultValue($object, $type, $attr);
	}

	/**
	 * Validation
	 */
	function validate_user_id($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				User_Series::user_id,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_series_id($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				User_Series::series_id,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_favorite($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false  ) {
			return null;
		}

		// boolean

		// Returns TRUE for "1", "true", "on" and "yes"
		// Returns FALSE for "0", "false", "off" and "no"
		// Returns NULL otherwise.
		$v = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
		if (is_null($v)) {
			return Localized::ModelValidation(
				$this->tableName(),
				User_Series::favorite,
				"FILTER_VALIDATE_BOOLEAN"
			);
		}
		return null;
	}
	function validate_read($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false  ) {
			return null;
		}

		// boolean

		// Returns TRUE for "1", "true", "on" and "yes"
		// Returns FALSE for "0", "false", "off" and "no"
		// Returns NULL otherwise.
		$v = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
		if (is_null($v)) {
			return Localized::ModelValidation(
				$this->tableName(),
				User_Series::read,
				"FILTER_VALIDATE_BOOLEAN"
			);
		}
		return null;
	}
	function validate_mislabeled($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false  ) {
			return null;
		}

		// boolean

		// Returns TRUE for "1", "true", "on" and "yes"
		// Returns FALSE for "0", "false", "off" and "no"
		// Returns NULL otherwise.
		$v = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
		if (is_null($v)) {
			return Localized::ModelValidation(
				$this->tableName(),
				User_Series::mislabeled,
				"FILTER_VALIDATE_BOOLEAN"
			);
		}
		return null;
	}
}

?>
