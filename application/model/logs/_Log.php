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

	public function attributes()
	{
		return array(
			Log::trace => array('length' => 256,'type' => 'TEXT'),
			Log::trace_id => array('length' => 256,'type' => 'TEXT'),
			Log::context => array('length' => 256,'type' => 'TEXT'),
			Log::context_id => array('length' => 256,'type' => 'TEXT'),
			Log::message => array('type' => 'TEXT'),
			Log::session => array('length' => 256,'type' => 'TEXT'),
			Log::created => array('type' => 'DATE')
		);
	}

	public function relationships()
	{
		return array(
			Log::logLevel => array(
				'destination' => 'Log_Level',
				'ownsDestination' => false,
				'isMandatory' => true,
				'isToMany' => false,
				'joins' => array( 'level_code' => 'code')
			)
		);
	}

	public function searchQualifiers( array $query )
	{
		$qualifiers = array();
		if ( is_array($query) ) {
			foreach( $query as $attr => $value ) {
				switch ($attr) {
			// Log::id == INTEGER

			// Log::trace == TEXT
				case Log::trace:
					if (strlen($value) > 0) {
						$qualifiers[Log::trace] = Qualifier::Like(Log::trace, $value);
					}
					break;

			// Log::trace_id == TEXT
				case Log::trace_id:
					if (strlen($value) > 0) {
						$qualifiers[Log::trace_id] = Qualifier::Like(Log::trace_id, $value);
					}
					break;

			// Log::context == TEXT
				case Log::context:
					if (strlen($value) > 0) {
						$qualifiers[Log::context] = Qualifier::Like(Log::context, $value);
					}
					break;

			// Log::context_id == TEXT
				case Log::context_id:
					if (strlen($value) > 0) {
						$qualifiers[Log::context_id] = Qualifier::Like(Log::context_id, $value);
					}
					break;

			// Log::message == TEXT
				case Log::message:
					if (strlen($value) > 0) {
						$qualifiers[Log::message] = Qualifier::Like(Log::message, $value);
					}
					break;

			// Log::session == TEXT
				case Log::session:
					if (strlen($value) > 0) {
						$qualifiers[Log::session] = Qualifier::Equals( Log::session, $value );
					}
					break;

			// Log::level_code == TEXT
				case Log::level_code:
					if (strlen($value) > 0) {
						$qualifiers[Log::level_code] = Qualifier::Equals( Log::level_code, $value );
					}
					break;

			// Log::created == DATE

				default:
					/* no type specified for Log::created */
					break;
				}
			}
		}
		return $qualifiers;
	}

	/**
	 *	Simple fetches
	 */

	public function allForTrace($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Log::trace, $value, null, $limit);
	}

	public function allLikeTrace($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return SQL::Select( $this )
			->where( Qualifier::Like( Log::trace, $value, SQL::SQL_LIKE_AFTER ))
			->orderBy( $this->sortOrder() )
			->limit( $limit )
			->fetchAll();
	}

	public function allForTrace_id($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Log::trace_id, $value, null, $limit);
	}

	public function allLikeTrace_id($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return SQL::Select( $this )
			->where( Qualifier::Like( Log::trace_id, $value, SQL::SQL_LIKE_AFTER ))
			->orderBy( $this->sortOrder() )
			->limit( $limit )
			->fetchAll();
	}

	public function allForContext($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Log::context, $value, null, $limit);
	}

	public function allLikeContext($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return SQL::Select( $this )
			->where( Qualifier::Like( Log::context, $value, SQL::SQL_LIKE_AFTER ))
			->orderBy( $this->sortOrder() )
			->limit( $limit )
			->fetchAll();
	}

	public function allForContext_id($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Log::context_id, $value, null, $limit);
	}

	public function allLikeContext_id($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return SQL::Select( $this )
			->where( Qualifier::Like( Log::context_id, $value, SQL::SQL_LIKE_AFTER ))
			->orderBy( $this->sortOrder() )
			->limit( $limit )
			->fetchAll();
	}

	public function allForMessage($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Log::message, $value, null, $limit);
	}

	public function allLikeMessage($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return SQL::Select( $this )
			->where( Qualifier::Like( Log::message, $value, SQL::SQL_LIKE_AFTER ))
			->orderBy( $this->sortOrder() )
			->limit( $limit )
			->fetchAll();
	}

	public function allForSession($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Log::session, $value, null, $limit);
	}


	public function allForLevel_code($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Log::level_code, $value, null, $limit);
	}




	/**
	 * Simple relationship fetches
	 */
	public function allForLogLevel($obj, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForFK(Log::level_code, $obj, $this->sortOrder(), $limit);
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
		if ( isset($sessionId) && is_null($sessionId) == false) {
			$qualifiers[] = Qualifier::Equals( 'session', $sessionId);
		}
		if ( isset($lastCheck) && is_null($lastCheck) == false) {
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
		if ( isset($trace) && is_null($trace) == false) {
			$qualifiers[] = Qualifier::Like( 'trace', $trace, SQL::SQL_LIKE_AFTER);
		}
		if ( isset($trace_id) && is_null($trace_id) == false) {
			$qualifiers[] = Qualifier::Like( 'trace_id', $trace_id, SQL::SQL_LIKE_AFTER);
		}
		if ( isset($context) && is_null($context) == false) {
			$qualifiers[] = Qualifier::Like( 'context', $context, SQL::SQL_LIKE_AFTER);
		}
		if ( isset($context_id) && is_null($context_id) == false) {
			$qualifiers[] = Qualifier::Like( 'context_id', $context_id, SQL::SQL_LIKE_AFTER);
		}
		if ( isset($message) && is_null($message) == false) {
			$qualifiers[] = Qualifier::Like( 'message', $message, SQL::SQL_LIKE_AFTER);
		}
		if ( isset($levelCode) && is_null($levelCode) == false) {
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
