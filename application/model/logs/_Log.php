<?php

namespace model\logs;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \model\logs\LogDBO as LogDBO;

/* import related objects */
use \model\logs\Log_Level as Log_Level;
use \model\logs\Log_LevelDBO as Log_LevelDBO;

/** Sample Creation script */
		/** LOG */
/*
		$sql = "CREATE TABLE IF NOT EXISTS log ( "
			. Log::id . " INTEGER PRIMARY KEY, "
			. Log::trace . " TEXT, "
			. Log::trace_id . " TEXT, "
			. Log::context . " TEXT, "
			. Log::context_id . " TEXT, "
			. Log::message . " TEXT, "
			. Log::session . " TEXT, "
			. Log::level_code . " TEXT, "
			. Log::created . " INTEGER, "
			. "FOREIGN KEY (". Log::level_code .") REFERENCES " . Log_Level::TABLE . "(" . Log_Level::code . ")"
		. ")";
		$this->sqlite_execute( "log", $sql, "Create table log" );

		$sql = 'CREATE  INDEX IF NOT EXISTS log_level_code on log (level_code)';
		$this->sqlite_execute( "log", $sql, "Index on log (level_code)" );
		$sql = 'CREATE  INDEX IF NOT EXISTS log_tracetrace_id on log (trace,trace_id)';
		$this->sqlite_execute( "log", $sql, "Index on log (trace,trace_id)" );
		$sql = 'CREATE  INDEX IF NOT EXISTS log_contextcontext_id on log (context,context_id)';
		$this->sqlite_execute( "log", $sql, "Index on log (context,context_id)" );
*/
abstract class _Log extends Model
{
	const TABLE = 'log';
	const id = 'id';
	const trace = 'trace';
	const trace_id = 'trace_id';
	const context = 'context';
	const context_id = 'context_id';
	const message = 'message';
	const session = 'session';
	const level_code = 'level_code';
	const created = 'created';

	public function tableName() { return Log::TABLE; }
	public function tablePK() { return Log::id; }

	public function sortOrder()
	{
		return array(
			array( 'desc' => Log::created)
		);
	}

	public function allColumnNames()
	{
		return array(
			Log::id,
			Log::trace,
			Log::trace_id,
			Log::context,
			Log::context_id,
			Log::message,
			Log::session,
			Log::level_code,
			Log::created
		);
	}

	/**
	 *	Simple fetches
	 */
	public function allForTrace($value)
	{
		return $this->allObjectsForKeyValue(Log::trace, $value);
	}

	public function allLikeTrace($value)
	{
		return SQL::Select( $this )
			->where( Qualifier::Like( Log::trace, $value, SQL::SQL_LIKE_AFTER ))
			->orderBy( $this->sortOrder() )
			->limit( 50 )
			->fetchAll();
	}
	public function allForTrace_id($value)
	{
		return $this->allObjectsForKeyValue(Log::trace_id, $value);
	}

	public function allLikeTrace_id($value)
	{
		return SQL::Select( $this )
			->where( Qualifier::Like( Log::trace_id, $value, SQL::SQL_LIKE_AFTER ))
			->orderBy( $this->sortOrder() )
			->limit( 50 )
			->fetchAll();
	}
	public function allForContext($value)
	{
		return $this->allObjectsForKeyValue(Log::context, $value);
	}

	public function allLikeContext($value)
	{
		return SQL::Select( $this )
			->where( Qualifier::Like( Log::context, $value, SQL::SQL_LIKE_AFTER ))
			->orderBy( $this->sortOrder() )
			->limit( 50 )
			->fetchAll();
	}
	public function allForContext_id($value)
	{
		return $this->allObjectsForKeyValue(Log::context_id, $value);
	}

	public function allLikeContext_id($value)
	{
		return SQL::Select( $this )
			->where( Qualifier::Like( Log::context_id, $value, SQL::SQL_LIKE_AFTER ))
			->orderBy( $this->sortOrder() )
			->limit( 50 )
			->fetchAll();
	}
	public function allForMessage($value)
	{
		return $this->allObjectsForKeyValue(Log::message, $value);
	}

	public function allLikeMessage($value)
	{
		return SQL::Select( $this )
			->where( Qualifier::Like( Log::message, $value, SQL::SQL_LIKE_AFTER ))
			->orderBy( $this->sortOrder() )
			->limit( 50 )
			->fetchAll();
	}
	public function allForSession($value)
	{
		return $this->allObjectsForKeyValue(Log::session, $value);
	}

	public function allLikeSession($value)
	{
		return SQL::Select( $this )
			->where( Qualifier::Like( Log::session, $value, SQL::SQL_LIKE_AFTER ))
			->orderBy( $this->sortOrder() )
			->limit( 50 )
			->fetchAll();
	}
	public function allForLevel_code($value)
	{
		return $this->allObjectsForKeyValue(Log::level_code, $value);
	}


	public function allForLogLevel($obj)
	{
		return $this->allObjectsForFK(Log::level_code, $obj, $this->sortOrder(), 50);
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "log_level":
					return array( Log::level_code, "code"  );
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
	public function base_create( $logLevel, $trace, $trace_id, $context, $context_id, $message, $session)
	{
		$obj = false;
		if ( isset($logLevel, $message) ) {
			$params = array(
				Log::trace => (isset($trace) ? $trace : null),
				Log::trace_id => (isset($trace_id) ? $trace_id : null),
				Log::context => (isset($context) ? $context : null),
				Log::context_id => (isset($context_id) ? $context_id : null),
				Log::message => (isset($message) ? $message : null),
				Log::session => (isset($session) ? $session : session_id()),
				Log::created => time(),
			);

			if ( isset($logLevel) ) {
				if ( $logLevel instanceof Log_LevelDBO) {
					$params[Log::level_code] = $logLevel->code;
				}
				else if ( is_string($logLevel) ) {
					$params[Log::level_code] = $logLevel;
				}
			}

			list( $obj, $errorList ) = $this->createObject($params);
			if ( is_array($errorList) ) {
				return $errorList;
			}
		}
		return $obj;
	}

	public function base_update( LogDBO $obj,
		$logLevel, $trace, $trace_id, $context, $context_id, $message, $session)
	{
		if ( isset( $obj ) && is_null($obj) == false ) {
			$updates = array();

			if (isset($trace) && (isset($obj->trace) == false || $trace != $obj->trace)) {
				$updates[Log::trace] = $trace;
			}
			if (isset($trace_id) && (isset($obj->trace_id) == false || $trace_id != $obj->trace_id)) {
				$updates[Log::trace_id] = $trace_id;
			}
			if (isset($context) && (isset($obj->context) == false || $context != $obj->context)) {
				$updates[Log::context] = $context;
			}
			if (isset($context_id) && (isset($obj->context_id) == false || $context_id != $obj->context_id)) {
				$updates[Log::context_id] = $context_id;
			}
			if (isset($message) && (isset($obj->message) == false || $message != $obj->message)) {
				$updates[Log::message] = $message;
			}
			if (isset($session) && (isset($obj->session) == false || $session != $obj->session)) {
				$updates[Log::session] = $session;
			}

			if ( isset($logLevel) ) {
				if ( $logLevel instanceof Log_LevelDBO) {
					$updates[Log::level_code] = $logLevel->code;
				}
				else if ( is_string($logLevel) ) {
					$updates[Log::level_code] = $logLevel;
				}
			}

			if ( count($updates) > 0 ) {
				list($obj, $errorList) = $this->updateObject( $obj, $updates );
				if ( is_array($errorList) ) {
					return $errorList;
				}
			}
		}
		return $obj;
	}

	/**
	 *	Delete functions
	 */
	public function deleteObject( DataObject $object = null)
	{
		if ( $object instanceof Log )
		{
			// does not own Log_Level
			return parent::deleteObject($object);
		}

		return false;
	}

	public function deleteAllForLogLevel(Log_LevelDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForLogLevel($obj);
			foreach ($array as $key => $value) {
				if ($this->deleteObject($value) == false) {
					$success = false;
					break;
				}
			}
		}
		return $success;
	}

	/**
	 *	Named fetches
	 */
	public function messagesSince( $sessionId, $lastCheck )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		if ( isset($sessionId)) {
			$qualifiers[] = Qualifier::Equals( 'session', $sessionId);
		}
		if ( isset($lastCheck)) {
			$qualifiers[] = Qualifier::GreaterThan( 'created', $lastCheck);
		}

		if ( count($qualifiers) > 0 ) {
			$select->where( Qualifier::Combine( 'AND', $qualifiers ));
		}

		$result = $select->fetchAll();
		return $result;
	}

	public function mostRecentLike( $trace, $trace_id, $context, $context_id, $levelCode, $message )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		if ( isset($trace)) {
			$qualifiers[] = Qualifier::Like( 'trace', $trace, SQL::SQL_LIKE_AFTER);
		}
		if ( isset($trace_id)) {
			$qualifiers[] = Qualifier::Like( 'trace_id', $trace_id, SQL::SQL_LIKE_AFTER);
		}
		if ( isset($context)) {
			$qualifiers[] = Qualifier::Like( 'context', $context, SQL::SQL_LIKE_AFTER);
		}
		if ( isset($context_id)) {
			$qualifiers[] = Qualifier::Like( 'context_id', $context_id, SQL::SQL_LIKE_AFTER);
		}
		if ( isset($message)) {
			$qualifiers[] = Qualifier::Like( 'message', $message, SQL::SQL_LIKE_AFTER);
		}
		if ( isset($levelCode)) {
			$qualifiers[] = Qualifier::Equals( 'level_code', $levelCode);
		}

		if ( count($qualifiers) > 0 ) {
			$select->where( Qualifier::Combine( 'AND', $qualifiers ));
		}

		$result = $select->fetchAll();
		return $result;
	}


	/** Validation */
	function validate_trace($object = null, $value)
	{
		$value = trim($value);
		return null;
	}
	function validate_trace_id($object = null, $value)
	{
		$value = trim($value);
		return null;
	}
	function validate_context($object = null, $value)
	{
		$value = trim($value);
		return null;
	}
	function validate_context_id($object = null, $value)
	{
		$value = trim($value);
		return null;
	}
	function validate_message($object = null, $value)
	{
		$value = trim($value);
		if (empty($value)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Log::message,
				"FIELD_EMPTY"
			);
		}
		return null;
	}
	function validate_session($object = null, $value)
	{
		$value = trim($value);
		return null;
	}
	function validate_level_code($object = null, $value)
	{
		$value = trim($value);
		if (isset($object->level_code) === false && empty($value) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Log::level_code,
				"FIELD_EMPTY"
			);
		}
		return null;
	}
	function validate_created($object = null, $value)
	{
		if ( isset($object, $object->created) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Log::created,
				"IMMUTABLE"
			);
		}
		return null;
	}
}

?>
