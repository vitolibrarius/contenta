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

/** Generated class, do not edit.
 */
abstract class _Log extends Model
{
	const TABLE = 'log';

	// attribute keys
	const id = 'id';
	const trace = 'trace';
	const trace_id = 'trace_id';
	const context = 'context';
	const context_id = 'context_id';
	const message = 'message';
	const session = 'session';
	const level_code = 'level_code';
	const created = 'created';

	// relationship keys
	const logLevel = 'logLevel';

	public function modelName()
	{
		return "Log";
	}

	public function dboName()
	{
		return '\model\logs\LogDBO';
	}

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

	public function allAttributes()
	{
		return array(
			Log::trace,
			Log::trace_id,
			Log::context,
			Log::context_id,
			Log::message,
			Log::session,
			Log::created
		);
	}

	public function allForeignKeys()
	{
		return array(Log::level_code);
	}

	public function allRelationshipNames()
	{
		return array(
			Log::logLevel
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




	/**
	 * Simple relationship fetches
	 */
	public function allForLogLevel($obj)
	{
		return $this->allObjectsForFK(Log::level_code, $obj, $this->sortOrder(), 50);
	}

	public function countForLogLevel($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Log::level_code, $obj );
		}
		return false;
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
	public function createObject( array $values = array() )
	{
		if ( isset($values) ) {

			// default values for attributes
			if ( isset($values['trace']) == false ) {
				$default_trace = $this->attributeDefaultValue( null, null, Log::trace);
				if ( is_null( $default_trace ) == false ) {
					$values['trace'] = $default_trace;
				}
			}
			if ( isset($values['trace_id']) == false ) {
				$default_trace_id = $this->attributeDefaultValue( null, null, Log::trace_id);
				if ( is_null( $default_trace_id ) == false ) {
					$values['trace_id'] = $default_trace_id;
				}
			}
			if ( isset($values['context']) == false ) {
				$default_context = $this->attributeDefaultValue( null, null, Log::context);
				if ( is_null( $default_context ) == false ) {
					$values['context'] = $default_context;
				}
			}
			if ( isset($values['context_id']) == false ) {
				$default_context_id = $this->attributeDefaultValue( null, null, Log::context_id);
				if ( is_null( $default_context_id ) == false ) {
					$values['context_id'] = $default_context_id;
				}
			}
			if ( isset($values['message']) == false ) {
				$default_message = $this->attributeDefaultValue( null, null, Log::message);
				if ( is_null( $default_message ) == false ) {
					$values['message'] = $default_message;
				}
			}
			if ( isset($values['session']) == false ) {
				$default_session = $this->attributeDefaultValue( null, null, Log::session);
				if ( is_null( $default_session ) == false ) {
					$values['session'] = $default_session;
				}
			}
			if ( isset($values['created']) == false ) {
				$default_created = $this->attributeDefaultValue( null, null, Log::created);
				if ( is_null( $default_created ) == false ) {
					$values['created'] = $default_created;
				}
			}

			// default conversion for relationships
			if ( isset($values['logLevel']) ) {
				$local_logLevel = $values['logLevel'];
				if ( $local_logLevel instanceof Log_LevelDBO) {
					$values[Log::level_code] = $local_logLevel->code;
				}
				else if ( is_string( $local_logLevel) ) {
					$params[Log::level_code] = $local_logLevel;
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
					$values[Log::level_code] = $local_logLevel->code;
				}
				else if ( is_string( $local_logLevel) ) {
					$params[Log::level_code] = $values['logLevel'];
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
			// does not own logLevel Log_Level
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
	 * Named fetches
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


	/**
	 * Attribute editing
	 */
	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Log::message
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesMap() {
		return array(
			Log::trace => Model::TEXT_TYPE,
			Log::trace_id => Model::TEXT_TYPE,
			Log::context => Model::TEXT_TYPE,
			Log::context_id => Model::TEXT_TYPE,
			Log::message => Model::TEXT_TYPE,
			Log::session => Model::TEXT_TYPE,
			Log::level_code => Model::TO_ONE_TYPE,
			Log::created => Model::DATE_TYPE
		);
	}

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) === false || is_null($object) == true) {
			switch ($attr) {
				case Log::session:
					return session_id();
				case Log::level_code:
					return 'warning';
			}
		}
		return parent::attributeDefaultValue($object, $type, $attr);
	}

	/*
	 * return the foreign key object
	 */
	public function attributeObject($object = null, $type = null, $attr, $value)
	{
		$fkObject = false;
		if ( isset( $attr ) ) {
			switch ( $attr ) {
				case Log::level_code:
					$log_level_model = Model::Named('Log_Level');
					$fkObject = $log_level_model->objectForId( $value );
					break;
				default:
					break;
			}
		}
		return $fkObject;
	}

	/**
	 * Validation
	 */
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
	function validate_level_code($object = null, $value)
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
