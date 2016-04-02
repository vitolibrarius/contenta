<?php

use \Localized as Localized;
use \Logger as Logger;
use \DataObject as DataObject;
use \SQL as SQL;

/**
 * This is the "base model class". All other "real" models extend this class.
 */
abstract class Model
{
	const HTML_ATTR_SEPARATOR = '-';

	const NotifyInserted = 'inserted';
	const NotifyUpdated = 'updated';
	const NotifyDeleted = 'deleted';

	const IconName = 'Icon';
	const ThumbnailName = 'Thumbnail';

	const TERTIARY_UNSET = -1;
	const TERTIARY_TRUE = 1;
	const TERTIARY_FALSE = 0;

	const TO_ONE_TYPE = 'toOneRelationship';

	private static $_named_models = null;

	public static function Named($name)
	{
		if ( is_null(Model::$_named_models) ) {
			Model::$_named_models = array();
		}

		$parts = explode('\\', $name);
		$name = array_pop($parts);
		// converts table names like "log_level" to "Log_Level" to match the classname
		$parts = explode("_", $name);
		$parts = array_map('ucfirst', $parts);
		$name = implode("_", $parts);
		$className = "model\\" . $name;

		if ( isset(Model::$_named_models[$name]) ) {
			return Model::$_named_models[$name];
		}

		$path = find_entry_with_name( appendPath( APPLICATION_PATH, "model"), $name . ".php" );
		if ( is_null($path) == false ) {
			$package = basename(dirname($path));
			if ( $package != "model" ) {
				$className = "model\\" . $package . "\\" . $name;
			}
		}

		$model = new $className();
		Model::$_named_models[$name] = $model;

		return $model;
	}

	public function __construct()
	{
	}

	/* Common model methods */
	abstract public function tableName();
	abstract public function tablePK();
	abstract public function sortOrder();
	abstract public function allColumnNames();

	public function updateStatistics( $xid = 0, $xsource = null ) { return true; }

	public function notifyKeypaths() { return array(); }
	public function processNotification( $type = 'none', $dbo )
	{
		$keypaths = $this->notifyKeypaths();
		foreach ( $keypaths as $kp ) {
			$target = dbo_valueForKeypath( $kp, $dbo );
			if ( $target instanceof DataObject ) {
				$target->notify( $type, $dbo );
			}
			else if ( is_array( $target ) ) {
				foreach( $target as $subtarget ) {
					if ( $subtarget instanceof DataObject ) {
						$subtarget->notify( $type, $dbo );
					}
				}
			}
		}
	}

	public function allColumns() {
		return implode(",", $this->allColumnNames() );
	}

	public function hasColumn($columnName) {
		return in_array($columnName, $this->allColumnNames());
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			$fkName = $this->tableName() . "_id";
			if ( $joinModel->hasColumn( $fkName ) ) {
				return array($this->tablePK(), $fkName);
			}

			$localName = $joinModel->tableName() . "_id";
			if ( $this->hasColumn( $localName ) ) {
				return array($localName, $joinModel->tablePK());
			}
		}
		return array("unknown", "bad relationship");
	}

	public function refreshObject($object)
	{
		return SQL::SelectObject( $this, $object )->fetch();
	}

	public function objectForId($id = 0)
	{
		return SQL::Select( $this, null, db\Qualifier::Equals( $this->tablePK(), $id) )->fetch();
	}

	public function singleObjectForKeyValue($key, $value = null)
	{
		if ( is_null($value) ) {
			return $this->singleObject( db\Qualifier::IsNull( $key ));
		}

		return $this->singleObject( db\Qualifier::Equals( $key, $value ));
	}

	public function singleObject( db\Qualifier $qualifier = null)
	{
		if ( is_null($qualifier) == false) {
			return SQL::Select( $this )->where( $qualifier )->fetch();
		}

		return false;
	}

	public function singleObjectForKeyValues( array $kvArray = null, $sortColumns = null, $and = true)
	{
		if ( count($kvArray > 0 ) ) {
			$select = SQL::Select( $this );
			$qArray = array();
			foreach($kvArray as $key => $value ) {
				if ( is_null($value) ) {
					$qArray[] = db\Qualifier::IsNull( $key );
				}
				else {
					$qArray[] = db\Qualifier::Equals( $key, $value );
				}
			}

			if ( $and ) {
				$select->where( db\Qualifier::AndQualifier( $qArray ));
			}
			else {
				$select->where( db\Qualifier::OrQualifier( $qArray ));
			}
			$select->orderBy( ($sortColumns == null ? $this->sortOrder() : $sortColumns) );
			return $select->fetch();
		}
		return false;
	}

	public function objectForExternal($xid, $xsrc)
	{
		$allColumns = $this->allColumnNames();
		if ( in_array("xid", $allColumns) == false || in_array("xsource", $allColumns) == false )  {
			throw new \Exception("External ID is not supported by " . var_export($this, true));
		}

		if ( isset($xid, $xsrc) ) {
			return SQL::Select( $this, null, db\Qualifier::XID($xid, $xsrc))->fetch();
		}

		return false;
	}

	public function allObjects($sortColumns = null, $limit = 50)
	{
		$select = SQL::Select( $this );
		$select->orderBy( ($sortColumns == null ? $this->sortOrder() : $sortColumns) );
		$select->limit($limit);
		return $select->fetchAll();
	}

	public function allObjectsForKeyValue($key, $value = null, $sortColumns = null, $limit = 50)
	{
		$select = SQL::Select( $this );
		if ( is_null($value) ) {
			$select->where( db\Qualifier::IsNull( $key ));
		}
		else {
			$select->where( db\Qualifier::Equals( $key, $value ));
		}
		$select->orderBy( ($sortColumns == null ? $this->sortOrder() : $sortColumns) );
		$select->limit($limit);
		return $select->fetchAll();
	}

	public function allObjectsForKeyValues( array $kvArray = null, $sortColumns = null, $limit = 50, $and = true)
	{
		if ( count($kvArray > 0 ) ) {
			$select = SQL::Select( $this );
			$qArray = array();
			foreach($kvArray as $key => $value ) {
				if ( is_null($value) ) {
					$qArray[] = db\Qualifier::IsNull( $key );
				}
				else {
					$qArray[] = db\Qualifier::Equals( $key, $value );
				}
			}

			if ( $and ) {
				$select->where( db\Qualifier::AndQualifier( $qArray ));
			}
			else {
				$select->where( db\Qualifier::OrQualifier( $qArray ));
			}
			$select->orderBy( ($sortColumns == null ? $this->sortOrder() : $sortColumns) );
			$select->limit($limit);
			return $select->fetchAll();
		}
		return false;
	}

	public function allObjectsForQualifier(db\Qualifier $qualifier = null, $sortColumns = null, $limit = 50)
	{
		$select = SQL::Select( $this );
		if ( is_null($qualifier) == false ) {
			$select->where( $qualifier );
		}
		$select->orderBy( ($sortColumns == null ? $this->sortOrder() : $sortColumns) );
		$select->limit($limit);
		return $select->fetchAll();
	}

	public function allObjectsForFK($relatedAttribute, DataObject $sourceObject, array $sortColumns = null, $limit = 0)
	{
		return SQL::Select( $this, null, db\Qualifier::FK( $relatedAttribute, $sourceObject ) )
			->orderBy( ($sortColumns == null ? $this->sortOrder() : $sortColumns) )
			->limit($limit)
			->fetchAll();
	}

	public function allObjectsForFKWithValue($relatedAttribute, DataObject $sourceObject, $key, $value = null, array $sortOrder = null, $limit = 0)
	{
		if ( is_null($value) ) {
			return $this->allObjectsForFKAndQualifier($relatedAttribute, $sourceObject, db\Qualifier::IsNull( $key ), $sortOrder, $limit);
		}

		return $this->allObjectsForFKAndQualifier($relatedAttribute, $sourceObject, db\Qualifier::Equals( $key, $value ), $sortOrder, $limit);
	}

	public function allObjectsForFKAndQualifier($relatedAttribute, DataObject $sourceObject, db\Qualifier $qual = null, array $sortOrder = null, $limit = 0)
	{
		if ( is_null($qual) ) {
			throw new \Exception( "Qualifier cannot be null" );
		}
		$fkQual = db\Qualifier::FK( $relatedAttribute, $sourceObject );

		$select = SQL::Select( $this );
		$select->where( db\Qualifier::AndQualifier($qual, $fkQual));
		$select->orderBy($sortOrder);
		$select->limit($limit);
		return $select->fetchAll();
	}

	/** special functions */
	public function countForFK($relatedAttribute, DataObject $sourceObject)
	{
		$result = SQL::Count( $this, null, db\Qualifier::FK( $relatedAttribute, $sourceObject ) )->fetchAll();
		return ( isset($result, $result['count']) ? $result['count'] : false );
	}

	public function countForKeyValue($key, $value = null)
	{
		$select = SQL::Count( $this );
		if ( is_null($value) ) {
			$select->where( db\Qualifier::IsNull( $key ));
		}
		else {
			$select->where( db\Qualifier::Equals( $key, $value ));
		}
		$result = $select->fetchAll();
		return ( isset($result, $result['count']) ? $result['count'] : false );
	}

	public function randomObjects( $limit = 1)
	{
		$select = SQL::Select( $this );
		$select->orderBy( array( "random()") );
		$select->limit($limit);
		return $select->fetchAll();
	}
	public function allObjectsNeverExternalUpdate($limit = -1)
	{
		$limit = intval( $limit );
		/** select items never updated, ie a null xupdated or pub_active */
		$hasXID = db\Qualifier::IsNotNull( "xid" );
		$needsQualifiers = array();
		$needsQualifiers[] = db\Qualifier::IsNull( "xupdated" );
		if ( $this->hasColumn('pub_active') ) {
			$needsQualifiers[] = db\Qualifier::IsNull( "pub_active" );
		}
		$needsUpdate = db\Qualifier::OrQualifier($needsQualifiers);

		$select = SQL::Select( $this );
		$select->where( db\Qualifier::AndQualifier( $hasXID, $needsUpdate ));
		$select->orderBy( array("xupdated") );
		$select->limit( $limit );

		return $select->fetchAll();
	}

	public function allObjectsForExternalUpdate($activeOnly = true, $ageInDays = 7, $startYearCutoff = 0, $limit = -1)
	{
		$activeOnly = boolval( $activeOnly );
		// ageInDays >= 0 <= 365
		$ageInDays = min(max(intval($ageInDays), 0), 365);
		$limit = intval( $limit );
		$startYearCutoff = intval($startYearCutoff);

		$qualifiers = array();
		$qualifiers[] = db\Qualifier::IsNotNull( "xid" );
		$qualifiers[] = db\Qualifier::LessThan( "xupdated", (time() - (3600 * 24 * $ageInDays)) );
		if ( $startYearCutoff > 0 && $this->hasColumn('start_year')) {
			$cutoff = intval(date("Y")) - $startYearCutoff;
			$qualifiers[] = db\Qualifier::GreaterThan( "start_year", $cutoff);
		}
		if ( $activeOnly && $this->hasColumn('pub_active') ) {
			$qualifiers[] = db\Qualifier::Equals( "pub_active", 1 );
		}

		$order = array();
		$order[] = array(SQL::SQL_ORDER_ASC => "date(xupdated, 'unixepoch')");
		if ( $this->hasColumn('pub_active') ) {
			$order[] = array(SQL::SQL_ORDER_DESC => "pub_active");
		}
		if ( $this->hasColumn('start_year') ) {
			$order[] = array(SQL::SQL_ORDER_DESC => "start_year");
		}

		$select = SQL::Select( $this );
		$select->where( db\Qualifier::AndQualifier( $qualifiers ));
		$select->orderBy( $order );
		$select->limit( $limit );

// 		\Session::addNegativeFeedback( $select->sqlStatement() . "; " . var_export($select->sqlParameters(), true));

		return $select->fetchAll();
	}

	public function allObjectsNeedingExternalUpdate($limit = -1)
	{
		/**
select date(xupdated, 'unixepoch'), start_year, pub_active, name from series where (xupdated is null OR xupdated < strftime('%s','now') - (3600*24*7)) order by pub_active, xupdated desc;

		*/
		$limit = intval( $limit );
		$unlimited = ( $limit == -1 );
		// update new items
		$results = $this->allObjectsNeverExternalUpdate($limit);
		if ( is_array($results) && $unlimited == false ) {
			$limit = $limit - count($results);
		}

		if ( $unlimited || $limit > 0 ) {
			/* all actively published every 14 days */
			$more_results = $this->allObjectsForExternalUpdate(true, 14, 0, $limit);
			if ( is_array($more_results) && $unlimited == false ) {
				$limit = $limit - count($more_results);
			}
			$results = array_unique(array_merge($results, $more_results));

			if ( $unlimited || $limit > 0 ) {
				// any record not updated in the last month, and started less than 5 years
				$more_results = $this->allObjectsForExternalUpdate(false, 30, 5, $limit);
				$results = array_unique(array_merge($results, $more_results));

				// any record not updated in the last 6 months, and started anytime
				if ( $unlimited || $limit > 0 ) {
					$more_results = $this->allObjectsForExternalUpdate(false, 180, 0, $limit);
					$results = array_unique(array_merge($results, $more_results));
				}
			}
		}
		return $results;
	}

	/** CRUD - create, read, update, delete */
	public function createObject(array $values = array()) {
		if ( count($values) > 0 ) {
			if ( $this->hasColumn('created') ) {
				$values['created'] = time();
			}

			if ( isset($values['desc']) ) {
				$values['desc'] = strip_tags($values['desc']);
			}

			$validation = $this->validateForSave(null, $values);
			if ( count($validation) == 0 ) {
				// passed validation, remove key/value is not in column list
				$allkeys = array_keys($values);
				foreach( $allkeys as $key ) {
					if ( $this->hasColumn($key) == false ) {
						unset($values[$key]);
					}
				}

				$insert = \SQL::Insert($this)->addRecord($values);
				$obj = $insert->commitTransaction();

				$this->processNotification( Model::NotifyInserted, $obj );

				return array( $obj, null );
			}

			// create failed, log validation errors
			$logMsg = "Validation errors creating " . $this->tableName();
			foreach ($validation as $attr => $errMsg ) {
				$logMsg .= "\n\t" . $errMsg;
			}
			Logger::LogWarning( $logMsg, __METHOD__, $this->tableName() );
			return array( false, $validation);
		}
		else {
			Logger::logError( "Failed to create record for empty values", __METHOD__, $this->tableName() );
		}

		return array( false, array());
	}

	public function deleteAllForKeyValue($key, $value = null)
	{
		$success = true;
		if ( isset($key, $value) ) {
			$array = $this->allObjectsForKeyValue($key, $value);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $idx => $obj) {
					if ($this->deleteObject($obj) == false) {
						$success = false;
						throw new exceptions\DeleteObjectException("Failed to delete " . $obj, $obj->id );
					}
				}
				$array = $this->allObjectsForKeyValue($key, $value);
			}
		}
		return $success;
	}

	public function deleteObject(DataObject $object = null)
	{
		if (isset($object) ) {
			$mediaPurged = true;
			if ( $object->hasAdditionalMedia() == true ) {
				Logger::logWarning("Deleting " . $object->displayName(), $this->tableName(), $object->id);
				$mediaPurged = (file_exists($object->mediaPath()) == false || destroy_dir($object->mediaPath()) );
			}

			if ( $mediaPurged == true ) {
				$deleteTransaction = \SQL::DeleteObject( $object )->commitTransaction();
				if ( $deleteTransaction ) {
					$this->processNotification( Model::NotifyDeleted, $object );
					return $deleteTransaction;
				}
			}
		}
		return false;
	}

	public function updateObject(DataObject $object = null, array $values) {
		if ( is_null($object) == false && count($values) > 0 ) {
			if ( $this->hasColumn('updated') ) {
				$values['updated'] = time();
			}

			if ( isset($values['desc']) ) {
				$values['desc'] = strip_tags($values['desc']);
			}

			$validation = $this->validateForSave($object, $values);
			if ( count($validation) == 0 ) {
				// passed validation, remove key/value is not in column list
				$allkeys = array_keys($values);
				foreach( $allkeys as $key ) {
					if ( $this->hasColumn($key) == false ) {
						unset($values[$key]);
					}
				}

				$update = \SQL::UpdateObject($object, $values)->commitTransaction();
				if ( $update ) {
					$this->processNotification( Model::NotifyUpdated, $object );
				}
				return $update;
			}

			// create failed, log validation errors
			$logMsg = "Validation errors update " . $object;
			foreach ($validation as $attr => $errMsg ) {
				$logMsg .= "\n\t" . $errMsg;
			}
			Logger::LogWarning( $logMsg, __METHOD__, $this->tableName() );
			return $validation;
		}
		else {
			Logger::logError( "Failed to update $object for values " . var_export($values, true) , __METHOD__, $this->tableName() );
		}
		return false;
	}

	public function updateAgregate($target_table, $agg_table, $agg_target, $agg_function, $target_pk, $agg_fk)
	{
		if ( isset($target_table, $agg_table, $agg_target, $agg_function, $target_pk, $agg_fk) ) {
			$sql = "update " . $target_table
				. " set " . $agg_target . " = (select " . $agg_function . " from "
					. $agg_table . " where " . $agg_table . "." . $agg_fk . " = " . $target_table . "." . $target_pk . ")";

			return \SQL::raw( $sql, null, "test raw" );
		}
		return false;
	}

	/* self test for consistency */
	public function consistencyTest()
	{
		$allColumnNames = $this->allColumnNames();
		$pk = $this->tablePK();
		if ( $this->hasColumn($pk) == false ) {
			return "$pk not found in columns " . var_export($allColumnNames, true);
		}
		return "ok";
	}
}
