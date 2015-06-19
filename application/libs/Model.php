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

	const IconName = 'Icon';
	const ThumbnailName = 'Thumbnail';

	const TERTIARY_UNSET = -1;
	const TERTIARY_TRUE = 1;
	const TERTIARY_FALSE = 0;

	const TEXT_TYPE = 'text';
	const PASSWORD_TYPE = 'password';
	const TEXTAREA_TYPE = 'textarea';
	const INT_TYPE = 'number';
	const DATE_TYPE = 'date';
	const FLAG_TYPE = 'flag';

	const TO_ONE_TYPE = 'toOneRelationship';

	public static function Named($name)
	{
		// converts table names like "log_level" to "Log_Level" to match the classname
		$parts = explode("_", $name);
		$parts = array_map('ucfirst', $parts);
		$className = "model\\" . implode("_", $parts);
		return new $className();
	}

	public function __construct()
	{
	}

	/* Common model methods */
	abstract public function tableName();
	abstract public function tablePK();
	abstract public function sortOrder();
	abstract public function allColumnNames();

	public function allColumns() {
		return implode(",", $this->allColumnNames() );
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

	public function allObjectsForFK($relatedAttribute, DataObject $sourceObject, array $sortOrder = null)
	{
		return SQL::Select( $this, null, db\Qualifier::FK( $relatedAttribute, $sourceObject ) )
			->orderBy($sortOrder)
			->fetchAll();
	}

	public function allObjectsForFKWithValue($relatedAttribute, DataObject $sourceObject, $key, $value = null, array $sortOrder = null)
	{
		if ( is_null($value) ) {
			return $this->allObjectsForFKAndQualifier($relatedAttribute, $sourceObject, db\Qualifier::IsNull( $key ), $sortOrder);
		}

		return $this->allObjectsForFKAndQualifier($relatedAttribute, $sourceObject, db\Qualifier::Equals( $key, $value ), $sortOrder);
	}

	public function allObjectsForFKAndQualifier($relatedAttribute, DataObject $sourceObject, db\Qualifier $qual = null, array $sortOrder = null)
	{
		if ( is_null($qual) ) {
			throw new \Exception( "Qualifier cannot be null" );
		}
		$fkQual = db\Qualifier::FK( $relatedAttribute, $sourceObject );

		$select = SQL::Select( $this );
		$select->where( db\Qualifier::AndQualifier($qual, $fkQual));
		$select->orderBy($sortOrder);
		return $select->fetchAll();
	}

	/** FIXME: rewrite */
	public function allObjectsLike(array $search = array(), $limit = 50) {
		return $this->fetchAllLike($this->tableName(), $this->allColumns(), $search, null, $this->sortOrder(), $limit);
	}

	/** FIXME: */
	public function deleteObject(DataObject $object = null) {
		if (isset($object) ) {
			$mediaPurged = true;
			if ( $object->hasAdditionalMedia() == true ) {
				Logger::logWarning("Purging " . $object->displayName() . " directory " . $object->mediaPath(), $model->tableName(), $object->id);
				$mediaPurged = (file_exists($object->mediaPath()) == false || destroy_dir($object->mediaPath()) );
			}

			$model = $object->model();
			return ($mediaPurged) && $this->deleteObj( $object, $model->tableName(), $model->tablePK() );
		}
		return false;
	}

	/** FIXME: */
	public function updateObject(DataObject $object = null, array $values) {
		if (isset($object) ) {
			$model = $object->model();

			$allColumns = $model->allColumnNames();
			if ( is_array($allColumns) && in_array('updated', $allColumns)) {
				$values['updated'] = time();
			}

			if ( isset($values['desc']) ) {
				$values['desc'] = strip_tags($values['desc']);
			}

			$qual = array( $model->tablePK() => $object->pkValue() );
			$validation = $this->validateForSave($object, $values);
			if ( count($validation) == 0 ) {
				// passed validation, remove key/value is not in column list
				$allkeys = array_keys($values);
				foreach( $allkeys as $key ) {
					if ( in_array($key, $allColumns) == false ) {
						unset($values[$key]);
					}
				}
				return $this->update( $this->tableName(), $values, $qual );
			}

			// create failed, log validation errors
			$logMsg = "Validation errors creating " . $this->tableName();
			foreach ($objectOrErrors as $attr => $errMsg ) {
				$logMsg .= "\n\t" . $errMsg;
			}
			Logger::LogWarning( $logMsg, __METHOD__, $this->tableName() );
			return $validation;
		}

		return false;
	}

	/** FIXME: */
	public function createObject(array $values = array()) {
		$tableName = $this->tableName();
		if ( count($values) > 0 ) {
			$allColumns = $this->allColumnNames();
			if ( is_array($allColumns) && in_array('created', $allColumns)) {
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
					if ( in_array($key, $allColumns) == false ) {
						unset($values[$key]);
					}
				}
				return $this->createObj( $this->tableName(), $values, $this->tablePK() );
			}

			// create failed, log validation errors
			$logMsg = "Validation errors creating " . $this->tableName();
			foreach ($validation as $attr => $errMsg ) {
				$logMsg .= "\n\t" . $errMsg;
			}
			Logger::LogWarning( $logMsg, __METHOD__, $this->tableName() );
			return $validation;
		}
		else {
			Logger::logError( "Failed to create record for empty values", __METHOD__, $this->tableName() );
		}

		return false;
	}


	/** FIXME: */
	public function reportSQLError( $clazz = 'Model', $method = 'unknown', $pdocode, $pdoError, $sql, $params = null)
	{
		$msg = 'PDO Error(' . $pdocode . ') ' . $pdoError . ' for [' . $sql . '] ' . (isset($params) ? var_export($params, true) : 'No Parameters');
		Logger::logError($msg, $clazz, $method);
	}

	/** FIXME: */
	public function keyValueClause($glue = " AND ", $qualifiers = null, $prefix = '', $valueQual = '=')
	{
		$sql = '';
		if ( is_array($qualifiers) && count($qualifiers) > 0 ) {
			foreach ($qualifiers as $key => $value) {
				// key = :prefixKey
				// key like :prefixKey
				$placeholders[] = $key . ' ' . $valueQual . ' :' . $prefix . sanitize($key, true, true);
			}
			$sql .= implode(' ' . $glue . ' ', $placeholders);
		}
		return $sql;
	}

// 	/** FIXME: */
	public function orderbyClause($order = null) {
		$sql = '';
		if (is_null($order) == false) {
			$sql .= " ORDER BY ";
			if ( isset($order['asc']) || isset($order['desc']) ) {
				$allorder = array();
				if ( isset($order['asc']) ) {
					$allorder[] = implode(", ", $order['asc']);
				}
				if ( isset($order['desc']) ) {
					$allorder[] = implode(" DESC, ", $order['desc']) . ' DESC ';
				}

				$sql .= implode(", ", $allorder);
			}
			else {
				$sql .= implode(", ", $order);
			}
		}
		return $sql;
	}

	/** FIXME: */
	public function parameters(array $params = array(), array $arguments = null, $prefix = '', $valuePrefix = '', $valueSuffix = '')
	{
		if ( isset($arguments) && is_array($arguments)) {
			foreach ($arguments as $key => $value) {
				$idx = ':' . $prefix . sanitize($key, true, true);
 				$params[ $idx ] = $valuePrefix . $value . $valueSuffix;
			}
		}
		return $params;
	}

	/** FIXME: new aggregate SQl needed*/
	public function updateAgregate($target_table, $agg_table, $agg_target, $agg_function, $target_pk, $agg_fk)
	{
		if ( isset($target_table, $agg_table, $agg_target, $agg_function, $target_pk, $agg_fk) ) {
			$placeholders = array();
			$params = array();

			$sql = "update " . $target_table
				. " set " . $agg_target . " = (select " . $agg_function . " from "
					. $agg_table . " where " . $agg_table . "." . $agg_fk . " = " . $target_table . "." . $target_pk . ")";

			$statement = Database::instance()->prepare($sql);
			if ($statement && $statement->execute()) {
				return true;
			}

			$caller = callerClassAndMethod('updateAgregate');
			$errPoint = ($statement ? $statement : Database::instance());
			$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
			$this->reportSQLError($caller['class'], $caller['function'], $errPoint->errorCode(), $pdoError, $sql, $qualifiers);
		}
		return false;
	}

	/** FIXME: new aggregate SQl needed*/
	public function countForQualifier($table, $qualifiers = null)
	{
		if ( isset($table) ) {
			$placeholders = array();
			$params = array();

			$sql = "SELECT count(*) AS COUNT FROM " . $table;
			if ( isset($qualifiers) ) {
				$params = $this->parameters($params, $qualifiers);
				$sql .= " WHERE " . $this->keyValueClause(" AND ", $qualifiers);
			}

			$statement = Database::instance()->prepare($sql);
			if ($statement && $statement->execute($params)) {
				$dict = $statement->fetch();
				return (($dict != false) ? $dict->COUNT : 0);
			}

			$caller = callerClassAndMethod('countForQualifier');
			$errPoint = ($statement ? $statement : Database::instance());
			$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
			$this->reportSQLError($caller['class'], $caller['function'], $errPoint->errorCode(), $pdoError, $sql, $qualifiers);
		}
		return false;
	}

	/** FIXME: */
	public function randomObjects( $limit = 1)
	{
		$select = SQL::Select( $this );
		$select->orderBy( "random()" );
		$select->limit($limit);
		return $select->fetchAll();
	}


	public function allObjectsNeedingExternalUpdate($limit = -1)
	{
		$select = SQL::Select( $this );
		$hasXID = db\Qualifier::IsNotNull( "xid" );
		$needsUpdate = db\Qualifier::OrQualifier(
			db\Qualifier::IsNull( "xupdated" ),
			db\Qualifier::LessThan( "xupdated", (time() - (3600 * 24 * 7)) )
		);
		$select->where( db\Qualifier::AndQualifier( $hasXID, $needsUpdate ));
		$select->orderBy( "xupdated" );
		$select->limit( intval($limit) );
		return $select->fetchAll();
	}

	/** FIXME: */
	public function fetchAllJoin($table, $columns, $joinSource, $joinForeign, $foreignObjects, $qualifiers, $order = null, $limit = null)
	{
		if ( isset($table, $columns, $joinSource, $joinForeign, $foreignObjects) ) {
			$placeholders = array();
			$params = array();

			if ( is_array($columns) ) {
				$columns = implode(", ", $columns);
			}

			$sql = "SELECT " . $columns . " FROM " . $table . " WHERE ";
			foreach ($foreignObjects as $key => $obj) {
				$placeholders[] = ':join_' . $key;
				$params[':join_' . $key] = $obj->{$joinForeign};
			}
			$sql .= $joinSource . ' IN (' . implode(", ", $placeholders) . ')';

			if ( isset($qualifiers) ) {
				$params = $this->parameters($params, $qualifiers);
				$sql .= " AND " . $this->keyValueClause(" AND ", $qualifiers);
			}

			$sql .= $this->orderbyClause($order);
			if ( isset($limit) && intval($limit) > 0 ) {
				$sql .= " LIMIT " . $limit;
			}


			$statement = Database::instance()->prepare($sql);
			if ($statement && $statement->execute($params)) {
				$dboClassName = DataObject::NameForTable($table);
				try {
					if (class_exists($dboClassName)) {
						return $statement->fetchAll(PDO::FETCH_CLASS, $dboClassName);
					}
				}
				catch ( \ClassNotFoundException $e ) {
					return $statement->fetchAll();
				}
			}

			$caller = callerClassAndMethod('fetchAllJoin');
			$errPoint = ($statement ? $statement : Database::instance());
			$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
			$this->reportSQLError($caller['class'], $caller['function'], $errPoint->errorCode(), $pdoError, $sql, $qualifiers);
		}
		return false;
	}

	/** FIXME: */
	public function fetchJoin($table, $columns, $joinSource, $joinForeign, $foreignObject, $qualifiers, $order = null)
	{
		$results = fetchAllJoin($table, $columns, $joinSource, $joinForeign, array($foreignObject), $qualifiers, $order);
		if ( $results != false && count($results) == 1) {
			return $results[0];
		}
		return false;
	}

	/** FIXME: */
	public function update( $table, array $updates, $qualifiers = null )
	{
		if ( isset($table, $updates) && count($updates) > 0 ) {
			$placeholders = array();
			$params = array();
			$sql = "UPDATE " . $table;
			$sql .= " SET " . $this->keyValueClause(", ", $updates);
			$params = $this->parameters($params, $updates);

			if ( isset($qualifiers) ) {
				$params = $this->parameters($params, $qualifiers, 'q1_');
				$sql .= " WHERE " . $this->keyValueClause(" AND ", $qualifiers, 'q1_');
			}

			$statement = Database::instance()->prepare($sql);
			if ($statement && $statement->execute($params)) {
				return true;
			}

			$caller = callerClassAndMethod('update');
			$errPoint = ($statement ? $statement : Database::instance());
			$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
			$this->reportSQLError($caller['class'], $caller['function'], $errPoint->errorCode(), $pdoError, $sql, $params);
		}
		return false;
	}

	/** FIXME: */
	public function createObj( $table, $updates, $primaryKey = 'id' )
	{
		if ( isset($table, $updates) ) {
			$params = array();
			$params = $this->parameters($params, $updates);
			$sql = "INSERT INTO " . $table . " (" . implode(", ", array_keys($updates)) . ") "
				. "VALUES (" . implode(", ", array_keys($params)) . ")";

			$statement = Database::instance()->prepare($sql);
			if ($statement && $statement->execute($params)) {
				// convert the last inserted ROWID into the record primary key
				$rowId = Database::instance()->lastInsertId();

				$sql = "SELECT " . $primaryKey . " FROM " . $table . ' WHERE ROWID = :row' ;
				$statement = Database::instance()->prepare($sql);
				if ($statement && $statement->execute(array(':row' => $rowId))) {
					$record = $statement->fetch();
					return $record->{$primaryKey};
				}
			}

			$caller = callerClassAndMethod('createObj');
			$errPoint = ($statement ? $statement : Database::instance());
			$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
			$this->reportSQLError($caller['class'], $caller['function'], $errPoint->errorCode(), $pdoError, $sql, $params);
		}
		return false;
	}

	/** FIXME: */
	public function deleteObj( $obj, $table, $pkName = "id" )
	{
		if ( $obj != false && isset($table, $obj->{$pkName}) )
		{
			$sql = "delete from " . $table . " where " . $pkName . " = :id";
			$statement = Database::instance()->prepare($sql);
			$params = array( ":id" => $obj->{$pkName} );
			if ($statement && $statement->execute( $params ) ) {
				return true;
			}

			$caller = callerClassAndMethod('deleteObj');
			$errPoint = ($statement ? $statement : Database::instance());
			$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
			$this->reportSQLError($caller['class'], $caller['function'], $errPoint->errorCode(), $pdoError, $sql, $params);
		}
		else {
			Logger::logError("Unable to delete " . get_class($obj) . '(' . $table . ', ' . $obj->{$pkName} . ')', get_class($this) );
		}

	   return false;
	}

	/** FIXME: */
	public function deleteAllJoin( $table, $joinSource, $joinForeign, $foreignObject )
	{
		if ( $foreignObject != false && isset($table, $joinSource, $joinForeign) ) {
			$sql = "delete from " . $table . " where " . $joinSource . " = :id";
			$params = array( ":id" => $foreignObject->{$joinForeign} );

			echo $sql . PHP_EOL;
			echo var_dump($foreignObject->{$joinForeign}) . PHP_EOL;
			echo var_export($params, true) . PHP_EOL;

			$statement = Database::instance()->prepare($sql);
			$statement->execute( $params );

			echo 'error code "'. var_export(Database::instance()->errorCode(), true) . '"'. PHP_EOL;
			echo 'PDO error info ' . var_export( PDO::ERR_NONE, true) . PHP_EOL;
			return is_null(Database::instance()->errorCode()) || Database::instance()->errorCode() === PDO::ERR_NONE;
		}

	   return false;
	}

	/** validation */
	public function validateForSave($object = null, array &$values = array())
	{
		$validationErrors = array();

		$mandatoryKeys = $this->attributesMandatory($object);
		if ( is_array($mandatoryKeys) == false ) {
			$mandatoryKeys = array_keys($values);
		}
		else {
			$mandatoryKeys = array_merge_recursive($mandatoryKeys, array_keys($values) );
		}
		$mandatoryKeys = array_unique($mandatoryKeys);

		foreach( $mandatoryKeys as $key ) {
			$function = "validate_" . $key;
			if (method_exists($this, $function)) {
				$newvalue = (isset($values[$key]) ? $values[$key] : null);
				$failure = $this->$function($object, $newvalue);
				if ( is_null($failure) == false ) {
					$validationErrors[$key] = $failure;
				}
			}
		}
		return $validationErrors;
	}

	public function attributesFor($object = null, $type = null) 				{ return array(); }
	public function attributesMandatory($object = null)				 			{ return array(); }
	public function attributeName($object = null, $type = null, $attr)			{ return $this->attributeId($attr); }
	public function attributeIsEditable($object = null, $type = null, $attr)	{ return true; }
	public function attributeRestrictionMessage($object = null, $type = null, $attr)	{ return null; }
	public function attributeEditPattern($object = null, $type = null, $attr)	{ return null; }
	public function attributePlaceholder($object = null, $type = null, $attr)	{ return null; }
	public function attributeOptions($object = null, $type = null, $attr)		{ return null; }

	public function attributeLabel($object = null, $type = null, $attr)
	{
		return Localized::ModelLabel($this->tableName(), $attr);
	}

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object, $object->{$attr}) && is_null($object->{$attr}) == false) {
			return $object->{$attr};
		}
		return null;
	}

	public function attributeId($attr)
	{
		return $this->tableName() . Model::HTML_ATTR_SEPARATOR . $attr;
	}

	public function attributeType($attr)
	{
		$attributeArray = $this->attributesFor(null);
		if ( is_array($attributeArray) && isset($attributeArray[$attr]) ) {
			return $attributeArray[$attr];
		}
		return null;
	}

}
