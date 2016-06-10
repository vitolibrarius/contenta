<?php

namespace model\logs;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

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
			. Log::level . " TEXT, "
			. Log::created . " INTEGER, "
			. "FOREIGN KEY (". Log::level .") REFERENCES " . Log_Level::TABLE . "(" . Log_Level::code . ")"
		. ")";
		$this->sqlite_execute( "log", $sql, "Create table log" );

		$sql = 'CREATE  INDEX IF NOT EXISTS log_level on log (level)';
		$this->sqlite_execute( "log", $sql, "Index on log (level)" );
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
	const level = 'level';
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
			Log::level,
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
	public function allForLevel($value)
	{
		return $this->allObjectsForKeyValue(Log::level, $value);
	}


	public function allForLogLevel($obj)
	{
		return $this->allObjectsForFK(Log::level, $obj, $this->sortOrder(), 50);
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "log_level":
					return array( Log::level, "code"  );
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
			if ( isset($values['logLevel']) ) {
				$local_logLevel = $values['logLevel'];
				if ( $local_logLevel instanceof Log_LevelDBO) {
					$values[Log::level] = $local_logLevel->code;
				}
				else if ( is_string( $local_logLevel) ) {
					$params[Log::level] = $local_logLevel;
				}
			}
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Log ) {
			if ( isset($values['logLevel']) ) {
				$local_logLevel = $values['logLevel'];
				if ( $local_logLevel instanceof Log_LevelDBO) {
					$values[Log::level] = $local_logLevel->code;
				}
				else if ( is_string( $local_logLevel) ) {
					$params[Log::level] = $values['logLevel'];
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
		if ( $object instanceof LogDBO )
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
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForLogLevel($obj);
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
			$qualifiers[] = Qualifier::Equals( 'level', $levelCode);
		}

		if ( count($qualifiers) > 0 ) {
			$select->where( Qualifier::Combine( 'AND', $qualifiers ));
		}

		$result = $select->fetchAll();
		return $result;
	}


	/** Set attributes */
	public function setTrace( LogDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Log::trace => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setTrace_id( LogDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Log::trace_id => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setContext( LogDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Log::context => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setContext_id( LogDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Log::context_id => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setMessage( LogDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Log::message => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setSession( LogDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Log::session => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setLevel( LogDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Log::level => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setCreated( LogDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Log::created => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}


	/** Validation */
	function validate_trace($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_trace_id($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_context($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_context_id($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_message($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
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
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_level($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_created($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// created date is not changeable
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
