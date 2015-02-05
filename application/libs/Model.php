<?php

use \Database as Database;

class ValidationException extends Exception
{
	private $reasons = null;
	public function __construct($message, $code = 0, $reasons) {
		$this->reasons = $reasons;
		parent::__construct($message, $code);
	}

	// custom string representation of object
	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}\n" . var_export($this->reasons);
	}

	public function reasons() {
		return $this->reasons;
	}
}

/**
 * This is the "base controller class". All other "real" controllers extend this class.
 * Whenever a controller is created, we also
 * 1. initialize a session
 * 2. check if the user is not logged in anymore (session timeout) but has a cookie
 * 3. create a database connection (that will be passed to all models that need a database connection)
 * 4. create a view object
 */
abstract class Model
{
	const HTML_ATTR_SEPARATOR = '-';

	const TERTIARY_UNSET = -1;
	const TERTIARY_TRUE = 1;
	const TERTIARY_FALSE = 0;

	const TEXT_TYPE = 'text';
	const TEXTAREA_TYPE = 'textarea';
	const INT_TYPE = 'number';
	const DATE_TYPE = 'date';
	const FLAG_TYPE = 'flag';

	const TO_ONE_TYPE = 'toOneRelationship';

	public static function Named($name)
	{
		$className = "model\\" . $name;
		return new $className(Database::instance());
	}

	public function __construct(Database $db)
	{
		isset($db) || die("No database object");
		$this->db = $db;
	}

	/* Common model methods */
	abstract public function tableName();
	abstract public function tablePK();
	abstract public function sortOrder();
	abstract public function allColumnNames();

	abstract public function dboClassName();

	public function allColumns() {
		return implode(",", $this->allColumnNames() );
	}

	public function refresh($object)
	{
		$dboClassName = 'model\\' . ucwords($object->tableName()) . 'DBO';
		if ( $object != false && is_a($object, $dboClassName) ) {
			return $this->objectForId($object->{$this->tablePK()});
		}
		return false;
	}

	public function objectForId($id = 0)
	{
		return $this->fetch($this->tableName(), $this->allColumns(), array($this->tablePK() => $id));
	}

	public function allObjectsForId(array $idArray, $sortColumns = null, $limit = null)
	{
		if ( isset($idArray) ) {
			$placeholders = array();
			$params = array();

			$sql = "SELECT " . $this->allColumns() . " FROM " . $this->tableName() . " WHERE ";
			foreach ($idArray as $key => $id) {
				$placeholders[] = ':id_' . $key;
				$params[':id_' . $key] = $id;
			}
			$sql .= $this->tablePK() . ' IN (' . implode(", ", $placeholders) . ')';

			$sql .= $this->orderbyClause($sortColumns);
			if ( isset($limit) ) {
				$sql .= " LIMIT " . $limit;
			}

			$statement = $this->db->prepare($sql);
			if ($statement && $statement->execute($params)) {
				$dboClassName = $this->dboClassName();
				try {
					if (class_exists($dboClassName)) {
						return $statement->fetchAll(PDO::FETCH_CLASS, $dboClassName);
					}
				}
				catch ( \ClassNotFoundException $e ) {
					return $statement->fetchAll();
				}
			}

			$caller = callerClassAndMethod('allObjectsForId');
			$errPoint = ($statement ? $statement : $this->db);
			$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
			$this->reportSQLError($caller['class'], $caller['function'], $errPoint->errorCode(), $pdoError, $sql, $params);
		}
		return false;
	}

	public function allObjects($sortColumns = null)
	{
		return $this->fetchAll($this->tableName(), $this->allColumns(), null, ($sortColumns == null ? $this->sortOrder() : $sortColumns));
	}

	public function validateForSave($object, $values) {
		return true;
	}

	public function updateObject($object = null, array $values) {
		if (isset($object) && is_a($object, "\\DataObject" )) {
			$model = $object->model();
			$qual = array( $model->tablePK() => $object->pkValue() );
			$tableValues = $values[$model->tableName()];
			$validation = $this->validateForSave($object, $tableValues);

			return ($validation != true) ? $validation : $this->update( $model->tableName(), $tableValues, $qual );
		}
		else {
			return $this->createObject($values);
		}
	}

	public function createObject($values) {
		$tableName = $this->tableName();
		if ( isset($values, $values[$tableName]) ) {
			$params = array();
			$tableValues = $values[$tableName];
			$validation = $this->validateForSave(null, $tableValues);

			foreach ($tableValues as $key => $value) {
				$params[$key] = $value;
			}

			$allColumns = $this->allColumnNames();
			if ( is_array($allColumns) && in_array('created', $allColumns)) {
				$params['created'] = time();
			}

			$newObjId = $this->createObj($tableName, $params);
			return ($newObjId != false ? $this->objectForId($newObjId) : false);
		}

		return false;
	}

	public function echoSQL( $sql, $params = null)
	{
		echo $sql . PHP_EOL . (isset($params) ? var_export($params, true) : 'No Parameters') . PHP_EOL;
	}

	public function reportSQLError( $clazz = 'Model', $method = 'unknown', $pdocode, $pdoError, $sql, $params = null)
	{
		$msg = 'PDO Error(' . $pdocode . ') ' . $pdoError . ' for [' . $sql . '] ' . (isset($params) ? var_export($params, true) : 'No Parameters');
		Logger::logError($msg, $clazz, $method);
	}

	public function pragma_TableInfo($table)
	{
		$sql = "PRAGMA table_info(" . $table . ")";
		$statement = $this->db->prepare( $sql );
		if ($statement && $statement->execute()) {
			$table_pragma = $statement->fetchAll();
			if ($table_pragma != false) {
				$table_fields = array();
				foreach($table_pragma as $key => $value) {
					$table_fields[ $value->name ] = $value;
				}
				return $table_fields;
			}
		}
		else {
			$caller = callerClassAndMethod('pragma_TableInfo');
			$errPoint = ($statement ? $statement : $this->db);
			$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
			$this->reportSQLError($caller['class'], $caller['function'], $errPoint->errorCode(), $pdoError, $sql, null);
		}
		return false;
	}

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

	public function parameters($params = array(), $arguments = null, $prefix = '', $valuePrefix = '', $valueSuffix = '')
	{
		if ( isset($arguments) && is_array($arguments)) {
			foreach ($arguments as $key => $value) {
				$params[':' . $prefix . sanitize($key, true, true)] = $valuePrefix . $value . $valueSuffix;
			}
		}
		return $params;
	}

	public function updateAgregate($target_table, $agg_table, $agg_target, $agg_function, $target_pk, $agg_fk)
	{
		if ( isset($target_table, $agg_table, $agg_target, $agg_function, $target_pk, $agg_fk) ) {
			$placeholders = array();
			$params = array();

			$sql = "update " . $target_table
				. " set " . $agg_target . " = (select " . $agg_function . " from "
					. $agg_table . " where " . $agg_table . "." . $agg_fk . " = " . $target_table . "." . $target_pk . ")";

			$statement = $this->db->prepare($sql);
			if ($statement && $statement->execute()) {
				return true;
			}

			$caller = callerClassAndMethod('updateAgregate');
			$errPoint = ($statement ? $statement : $this->db);
			$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
			$this->reportSQLError($caller['class'], $caller['function'], $errPoint->errorCode(), $pdoError, $sql, $qualifiers);
		}
		return false;
	}

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

			$statement = $this->db->prepare($sql);
			if ($statement && $statement->execute($params)) {
				$dict = $statement->fetch();
				return (($dict != false) ? $dict->COUNT : 0);
			}

			$caller = callerClassAndMethod('countForQualifier');
			$errPoint = ($statement ? $statement : $this->db);
			$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
			$this->reportSQLError($caller['class'], $caller['function'], $errPoint->errorCode(), $pdoError, $sql, $qualifiers);
		}
		return false;
	}

	public function fetch($table, $columns, $qualifiers = null)
	{
		if ( isset($table, $columns) ) {
			$placeholders = array();
			$params = array();

			if ( is_array($columns) ) {
				$columns = implode(", ", $columns);
			}

			$sql = "SELECT " . $columns . " FROM " . $table;
			if ( isset($qualifiers) ) {
				$params = $this->parameters($params, $qualifiers);
				$sql .= " WHERE " . $this->keyValueClause(" AND ", $qualifiers);
			}

			$statement = $this->db->prepare($sql);
			if ($statement && $statement->execute($params)) {
				$dboClassName = $this->dboClassName();
				try {
					if (class_exists($dboClassName)) {
						return $statement->fetchObject($dboClassName);
					}
				}
				catch ( \ClassNotFoundException $e ) {
					return $statement->fetch();
				}
			}

			$caller = callerClassAndMethod('fetch');
			$errPoint = ($statement ? $statement : $this->db);
			$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
			$this->reportSQLError($caller['class'], $caller['function'], $errPoint->errorCode(), $pdoError, $sql, $qualifiers);
		}
		return false;
	}

	public function fetchRandom($table, $columns, $limit = 1)
	{
		// SELECT name FROM students ORDER BY RANDOM() LIMIT 1
		if ( isset($table, $columns) ) {
			if ( is_array($columns) ) {
				$columns = implode(", ", $columns);
			}

			$sql = "SELECT " . $columns . " FROM " . $table . " ORDER BY RANDOM()";

			if ( isset($limit) ) {
				$sql .= " LIMIT " . $limit;
			}

//			$this->echoSQL( $sql, $params);

			$statement = $this->db->prepare($sql);
			if ($statement && $statement->execute()) {
				$dboClassName = $this->dboClassName();
				try {
					if (class_exists($dboClassName)) {
						return $statement->fetchAll(PDO::FETCH_CLASS, $dboClassName);
					}
				}
				catch ( \ClassNotFoundException $e ) {
					return $statement->fetchAll();
				}
			}

			$caller = callerClassAndMethod('fetchAll');
			$errPoint = ($statement ? $statement : $this->db);
			$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
			$this->reportSQLError($caller['class'], $caller['function'], $errPoint->errorCode(), $pdoError, $sql, $qualifiers);
		}
		return false;

	}

	public function fetchAll($table, $columns, $qualifiers = null, $order = null, $limit = null)
	{
		if ( isset($table, $columns) ) {
			$placeholders = array();
			$params = array();

			if ( is_array($columns) ) {
				$columns = implode(", ", $columns);
			}

			$sql = "SELECT " . $columns . " FROM " . $table;
			if ( isset($qualifiers) ) {
				$params = $this->parameters($params, $qualifiers);
				$sql .= " WHERE " . $this->keyValueClause(" AND ", $qualifiers);
			}

			$sql .= $this->orderbyClause($order);

			if ( isset($limit) ) {
				$sql .= " LIMIT " . $limit;
			}

//			$this->echoSQL( $sql, $params);

			$statement = $this->db->prepare($sql);
			if ($statement && $statement->execute($params)) {
				$dboClassName = $this->dboClassName();
				try {
					if (class_exists($dboClassName)) {
						return $statement->fetchAll(PDO::FETCH_CLASS, $dboClassName);
					}
				}
				catch ( \ClassNotFoundException $e ) {
					return $statement->fetchAll();
				}
			}

			$caller = callerClassAndMethod('fetchAll');
			$errPoint = ($statement ? $statement : $this->db);
			$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
			$this->reportSQLError($caller['class'], $caller['function'], $errPoint->errorCode(), $pdoError, $sql, $qualifiers);
		}
		return false;
	}

	public function fetchAllLike($table, $columns, $likes, $qualifiers = null, $order = null, $limit = null, $joinType = 'AND', $likePrefix = '', $likeSuffix = '%')
	{
		if ( isset($table, $columns, $likes) ) {
			$placeholders = array();
			$params = array();

			if ( is_array($columns) ) {
				$columns = implode(", ", $columns);
			}

			$sql = "SELECT " . $columns . " FROM " . $table;
			if ( is_array($likes) && count($likes) > 0) {
				$params = $this->parameters($params, $likes, 'like', $likePrefix, $likeSuffix);
				$sql .= " WHERE " . $this->keyValueClause( $joinType, $likes, 'like', 'LIKE');

				if ( isset($qualifiers) ) {
					$params = $this->parameters($params, $qualifiers);
					$sql .= " AND " . $this->keyValueClause(" AND ", $qualifiers);
				}
			}
			else if ( isset($qualifiers) ) {
				$params = $this->parameters($params, $qualifiers);
				$sql .= " WHERE " . $this->keyValueClause(" AND ", $qualifiers);
			}

			$sql .= $this->orderbyClause($order);

			if ( isset($limit) ) {
				$sql .= " LIMIT " . $limit;
			}

			$statement = $this->db->prepare($sql);
			if ($statement && $statement->execute($params)) {
				$dboClassName = $this->dboClassName();
				try {
					if (class_exists($dboClassName)) {
						return $statement->fetchAll(PDO::FETCH_CLASS, $dboClassName);
					}
				}
				catch ( \ClassNotFoundException $e ) {
					return $statement->fetchAll();
				}
			}

			$caller = callerClassAndMethod('fetchAllLike');
			$errPoint = ($statement ? $statement : $this->db);
			$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
			$this->reportSQLError($caller['class'], $caller['function'], $errPoint->errorCode(), $pdoError, $sql, $likes);
		}
		return false;
	}

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
			if ( isset($limit) ) {
				$sql .= " LIMIT " . $limit;
			}

//			$this->echoSQL( $sql, $params);

			$statement = $this->db->prepare($sql);
			if ($statement && $statement->execute($params)) {
				$dboClassName = $this->dboClassName();
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
			$errPoint = ($statement ? $statement : $this->db);
			$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
			$this->reportSQLError($caller['class'], $caller['function'], $errPoint->errorCode(), $pdoError, $sql, $qualifiers);
		}
		return false;
	}

	public function fetchJoin($table, $columns, $joinSource, $joinForeign, $foreignObject, $qualifiers, $order = null)
	{
		$results = fetchAllJoin($table, $columns, $joinSource, $joinForeign, array($foreignObject), $qualifiers, $order);
		if ( $results != false && count($results) == 1) {
			return $results[0];
		}
		return false;
	}

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

			$statement = $this->db->prepare($sql);
			if ($statement && $statement->execute($params)) {
				return true;
			}

			$caller = callerClassAndMethod('update');
			$errPoint = ($statement ? $statement : $this->db);
			$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
			$this->reportSQLError($caller['class'], $caller['function'], $errPoint->errorCode(), $pdoError, $sql, $params);
		}
		return false;
	}

	public function createObj( $table, $updates, $primaryKey = 'id' )
	{
		if ( isset($table, $updates) ) {
			$params = array();
			$params = $this->parameters($params, $updates);
			$sql = "INSERT INTO " . $table . " (" . implode(", ", array_keys($updates)) . ") "
				. "VALUES (" . implode(", ", array_keys($params)) . ")";

			$statement = $this->db->prepare($sql);
			if ($statement && $statement->execute($params)) {
				// convert the last inserted ROWID into the record primary key
				$rowId = $this->db->lastInsertId();

				$sql = "SELECT " . $primaryKey . " FROM " . $table . ' WHERE ROWID = :row' ;
				$statement = $this->db->prepare($sql);
				if ($statement && $statement->execute(array(':row' => $rowId))) {
					$record = $statement->fetch();
					return $record->{$primaryKey};
				}
			}

			$caller = callerClassAndMethod('createObj');
			$errPoint = ($statement ? $statement : $this->db);
			$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
			$this->reportSQLError($caller['class'], $caller['function'], $errPoint->errorCode(), $pdoError, $sql, $params);
		}
		return false;
	}

	public function deleteObj( $obj, $table, $pkName = "id" )
	{
		if ( $obj != false && isset($table, $obj->{$pkName}) )
		{
			$sql = "delete from " . $table . " where " . $pkName . " = :id";
			$statement = $this->db->prepare($sql);
			$params = array( ":id" => $obj->{$pkName} );
			if ($statement && $statement->execute( $params ) ) {
				return true;
			}

			$caller = callerClassAndMethod('deleteObj');
			$errPoint = ($statement ? $statement : $this->db);
			$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
			$this->reportSQLError($caller['class'], $caller['function'], $errPoint->errorCode(), $pdoError, $sql, $params);
		}
		else {
			Logger::logError("Unable to delete " . get_class($obj) . '(' . $table . ', ' . $obj->{$pkName} . ')', get_class($this) );
		}

	   return false;
	}

	public function deleteAllJoin( $table, $joinSource, $joinForeign, $foreignObject )
	{
		if ( $foreignObject != false && isset($table, $joinSource, $joinForeign) ) {
			$sql = "delete from " . $table . " where " . $joinSource . " = :id";
			$params = array( ":id" => $foreignObject->{$joinForeign} );

			echo $sql . PHP_EOL;
			echo var_dump($foreignObject->{$joinForeign}) . PHP_EOL;
			echo var_export($params, true) . PHP_EOL;

			$statement = $this->db->prepare($sql);
			$statement->execute( $params );

			echo 'error code "'. var_export($this->db->errorCode(), true) . '"'. PHP_EOL;
			echo 'PDO error info ' . var_export( PDO::ERR_NONE, true) . PHP_EOL;
			return is_null($this->db->errorCode()) || $this->db->errorCode() === PDO::ERR_NONE;
		}

	   return false;
	}

	public function fetchAllForDateOlderThan($table, $columns, $date_column, $date, $includeNull = true, $limit)
	{
		return $this->fetchAllForDateComparison($table, $columns, $date_column, $date, '<', $includeNull, $limit);
	}

	public function fetchAllForDateNewerThan($table, $columns, $date_column, $date, $includeNull = true, $limit)
	{
		return $this->fetchAllForDateComparison($table, $columns, $date_column, $date, '>', $includeNull, $limit);
	}

	public function fetchAllForDateComparison($table, $columns, $date_column, $date, $comparison = '<', $includeNull = true, $limit)
	{
		if ( isset($table, $columns) ) {
			if ( isset($date) == false ) {
				$date = time();
			}

			$placeholders = array();
			$params = array();
			$qualifiers = array($date_column => $date);

			if ( is_array($columns) ) {
				$columns = implode(", ", $columns);
			}

			$sql = "SELECT " . $columns . " FROM " . $table;
			$params = $this->parameters($params, $qualifiers);
			$sql .= " WHERE " . $this->keyValueClause(" AND ", $qualifiers, null, $comparison);
			if ( $includeNull == true ) {
				$sql .= " OR " . $date_column . " is null";
				$sql .= " OR " . $date_column . " = ''";
			}

			$sql .= $this->orderbyClause(array( 'desc' => array($date_column)));

			if ( isset($limit) ) {
				$sql .= " LIMIT " . $limit;
			}
			echo $sql . PHP_EOL;
			echo var_export($params, true) . PHP_EOL;

			$statement = $this->db->prepare($sql);
			if ($statement && $statement->execute($params)) {
				$dboClassName = $this->dboClassName();
				try {
					if (class_exists($dboClassName)) {
						return $statement->fetchAll(PDO::FETCH_CLASS, $dboClassName);
					}
				}
				catch ( \ClassNotFoundException $e ) {
					return $statement->fetchAll();
				}
			}

			$caller = callerClassAndMethod('fetchAllLike');
			$errPoint = ($statement ? $statement : $this->db);
			$pdoError = $errPoint->errorInfo()[1] . ':' . $errPoint->errorInfo()[2];
			$this->reportSQLError($caller['class'], $caller['function'], $errPoint->errorCode(), $pdoError, $sql, $params);
		}
		return false;
	}

	public function attributesFor($object = null, $type = null) 				{ return array(); }
	public function attributeName($object = null, $type = null, $attr)			{ return $this->attributeId($attr); }
	public function attributeIsEditable($object = null, $type = null, $attr)	{ return true; }
	public function attributeRestrictionMessage($object = null, $type = null, $attr)	{ return null; }
	public function attributeEditPattern($object = null, $type = null, $attr)	{ return null; }
	public function attributePlaceholder($object = null, $type = null, $attr)	{ return null; }
	public function attributeOptions($object = null, $type = null, $attr)		{ return null; }

	public function attributeLabel($object = null, $type = null, $attr)
	{
		// example key:  Model/endpoint/type_id = type_id
		$labelKey = "Model/" . $this->tableName() . "/" . $attr;
		return Localized::Get($labelKey, $attr);
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
