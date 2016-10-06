<?php

namespace model\jobs;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\jobs\JobDBO as JobDBO;

/* import related objects */
use \model\jobs\Job_Type as Job_Type;
use \model\jobs\Job_TypeDBO as Job_TypeDBO;
use \model\network\Endpoint as Endpoint;
use \model\network\EndpointDBO as EndpointDBO;

/** Sample Creation script */
		/** JOB */
/*
		$sql = "CREATE TABLE IF NOT EXISTS job ( "
			. Job::id . " INTEGER PRIMARY KEY, "
			. Job::type_code . " TEXT, "
			. Job::endpoint_id . " INTEGER, "
			. Job::enabled . " INTEGER, "
			. Job::one_shot . " INTEGER, "
			. Job::fail_count . " INTEGER, "
			. Job::elapsed . " INTEGER, "
			. Job::minute . " TEXT, "
			. Job::hour . " TEXT, "
			. Job::dayOfWeek . " TEXT, "
			. Job::parameter . " TEXT, "
			. Job::next . " INTEGER, "
			. Job::last_run . " INTEGER, "
			. Job::last_fail . " INTEGER, "
			. Job::created . " INTEGER, "
			. "FOREIGN KEY (". Job::type_code .") REFERENCES " . Job_Type::TABLE . "(" . Job_Type::code . "),"
			. "FOREIGN KEY (". Job::endpoint_id .") REFERENCES " . Endpoint::TABLE . "(" . Endpoint::id . ")"
		. ")";
		$this->sqlite_execute( "job", $sql, "Create table job" );

		$sql = 'CREATE INDEX IF NOT EXISTS jobJob_Type_fk on job (type_code)';
		$this->sqlite_execute( "job", $sql, "FK Index on job (type_code)" );
		$sql = 'CREATE INDEX IF NOT EXISTS jobEndpoint_fk on job (endpoint_id)';
		$this->sqlite_execute( "job", $sql, "FK Index on job (endpoint_id)" );

*/
abstract class _Job extends Model
{
	const TABLE = 'job';

	// attribute keys
	const id = 'id';
	const type_code = 'type_code';
	const endpoint_id = 'endpoint_id';
	const enabled = 'enabled';
	const one_shot = 'one_shot';
	const fail_count = 'fail_count';
	const elapsed = 'elapsed';
	const minute = 'minute';
	const hour = 'hour';
	const dayOfWeek = 'dayOfWeek';
	const parameter = 'parameter';
	const next = 'next';
	const last_run = 'last_run';
	const last_fail = 'last_fail';
	const created = 'created';

	// relationship keys
	const jobType = 'jobType';
	const endpoint = 'endpoint';

	public function tableName() { return Job::TABLE; }
	public function tablePK() { return Job::id; }

	public function sortOrder()
	{
		return array(
			array( 'desc' => Job::next)
		);
	}

	public function allColumnNames()
	{
		return array(
			Job::id,
			Job::type_code,
			Job::endpoint_id,
			Job::enabled,
			Job::one_shot,
			Job::fail_count,
			Job::elapsed,
			Job::minute,
			Job::hour,
			Job::dayOfWeek,
			Job::parameter,
			Job::next,
			Job::last_run,
			Job::last_fail,
			Job::created
		);
	}

	public function allAttributes()
	{
		return array(
			Job::enabled,
			Job::one_shot,
			Job::fail_count,
			Job::elapsed,
			Job::minute,
			Job::hour,
			Job::dayOfWeek,
			Job::parameter,
			Job::next,
			Job::last_run,
			Job::last_fail,
			Job::created
		);
	}

	public function allForeignKeys()
	{
		return array(Job::type_code,
			Job::endpoint_id);
	}

	public function allRelationshipNames()
	{
		return array(
			Job::jobType,
			Job::endpoint
		);
	}

	/**
	 *	Simple fetches
	 */

	public function allForType_code($value)
	{
		return $this->allObjectsForKeyValue(Job::type_code, $value);
	}





	public function allForFail_count($value)
	{
		return $this->allObjectsForKeyValue(Job::fail_count, $value);
	}

	public function allForElapsed($value)
	{
		return $this->allObjectsForKeyValue(Job::elapsed, $value);
	}

	public function allForMinute($value)
	{
		return $this->allObjectsForKeyValue(Job::minute, $value);
	}


	public function allForHour($value)
	{
		return $this->allObjectsForKeyValue(Job::hour, $value);
	}


	public function allForDayOfWeek($value)
	{
		return $this->allObjectsForKeyValue(Job::dayOfWeek, $value);
	}


	public function allForParameter($value)
	{
		return $this->allObjectsForKeyValue(Job::parameter, $value);
	}







	/**
	 * Simple relationship fetches
	 */
	public function allForJobType($obj)
	{
		return $this->allObjectsForFK(Job::type_code, $obj, $this->sortOrder(), 50);
	}

	public function countForJobType($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Job::type_code, $obj );
		}
		return false;
	}
	public function allForEndpoint($obj)
	{
		return $this->allObjectsForFK(Job::endpoint_id, $obj, $this->sortOrder(), 50);
	}

	public function countForEndpoint($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Job::endpoint_id, $obj );
		}
		return false;
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "job_type":
					return array( Job::type_code, "code"  );
					break;
				case "endpoint":
					return array( Job::endpoint_id, "id"  );
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
			if ( isset($values['enabled']) == false ) {
				$default_enabled = $this->attributeDefaultValue( null, null, Job::enabled);
				if ( is_null( $default_enabled ) == false ) {
					$values['enabled'] = $default_enabled;
				}
			}
			if ( isset($values['one_shot']) == false ) {
				$default_one_shot = $this->attributeDefaultValue( null, null, Job::one_shot);
				if ( is_null( $default_one_shot ) == false ) {
					$values['one_shot'] = $default_one_shot;
				}
			}
			if ( isset($values['fail_count']) == false ) {
				$default_fail_count = $this->attributeDefaultValue( null, null, Job::fail_count);
				if ( is_null( $default_fail_count ) == false ) {
					$values['fail_count'] = $default_fail_count;
				}
			}
			if ( isset($values['elapsed']) == false ) {
				$default_elapsed = $this->attributeDefaultValue( null, null, Job::elapsed);
				if ( is_null( $default_elapsed ) == false ) {
					$values['elapsed'] = $default_elapsed;
				}
			}
			if ( isset($values['minute']) == false ) {
				$default_minute = $this->attributeDefaultValue( null, null, Job::minute);
				if ( is_null( $default_minute ) == false ) {
					$values['minute'] = $default_minute;
				}
			}
			if ( isset($values['hour']) == false ) {
				$default_hour = $this->attributeDefaultValue( null, null, Job::hour);
				if ( is_null( $default_hour ) == false ) {
					$values['hour'] = $default_hour;
				}
			}
			if ( isset($values['dayOfWeek']) == false ) {
				$default_dayOfWeek = $this->attributeDefaultValue( null, null, Job::dayOfWeek);
				if ( is_null( $default_dayOfWeek ) == false ) {
					$values['dayOfWeek'] = $default_dayOfWeek;
				}
			}
			if ( isset($values['parameter']) == false ) {
				$default_parameter = $this->attributeDefaultValue( null, null, Job::parameter);
				if ( is_null( $default_parameter ) == false ) {
					$values['parameter'] = $default_parameter;
				}
			}
			if ( isset($values['next']) == false ) {
				$default_next = $this->attributeDefaultValue( null, null, Job::next);
				if ( is_null( $default_next ) == false ) {
					$values['next'] = $default_next;
				}
			}
			if ( isset($values['last_run']) == false ) {
				$default_last_run = $this->attributeDefaultValue( null, null, Job::last_run);
				if ( is_null( $default_last_run ) == false ) {
					$values['last_run'] = $default_last_run;
				}
			}
			if ( isset($values['last_fail']) == false ) {
				$default_last_fail = $this->attributeDefaultValue( null, null, Job::last_fail);
				if ( is_null( $default_last_fail ) == false ) {
					$values['last_fail'] = $default_last_fail;
				}
			}
			if ( isset($values['created']) == false ) {
				$default_created = $this->attributeDefaultValue( null, null, Job::created);
				if ( is_null( $default_created ) == false ) {
					$values['created'] = $default_created;
				}
			}

			// default conversion for relationships
			if ( isset($values['jobType']) ) {
				$local_jobType = $values['jobType'];
				if ( $local_jobType instanceof Job_TypeDBO) {
					$values[Job::type_code] = $local_jobType->code;
				}
				else if ( is_string( $local_jobType) ) {
					$params[Job::type_code] = $local_jobType;
				}
			}
			if ( isset($values['endpoint']) ) {
				$local_endpoint = $values['endpoint'];
				if ( $local_endpoint instanceof EndpointDBO) {
					$values[Job::endpoint_id] = $local_endpoint->id;
				}
				else if ( is_integer( $local_endpoint) ) {
					$params[Job::endpoint_id] = $local_endpoint;
				}
			}
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Job ) {
			if ( isset($values['jobType']) ) {
				$local_jobType = $values['jobType'];
				if ( $local_jobType instanceof Job_TypeDBO) {
					$values[Job::type_code] = $local_jobType->code;
				}
				else if ( is_string( $local_jobType) ) {
					$params[Job::type_code] = $values['jobType'];
				}
			}
			if ( isset($values['endpoint']) ) {
				$local_endpoint = $values['endpoint'];
				if ( $local_endpoint instanceof EndpointDBO) {
					$values[Job::endpoint_id] = $local_endpoint->id;
				}
				else if ( is_integer( $local_endpoint) ) {
					$params[Job::endpoint_id] = $values['endpoint'];
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
		if ( $object instanceof JobDBO )
		{
			// does not own jobType Job_Type
			// does not own endpoint Endpoint
			return parent::deleteObject($object);
		}

		return false;
	}

	public function deleteAllForJobType(Job_TypeDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForJobType($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForJobType($obj);
			}
		}
		return $success;
	}
	public function deleteAllForEndpoint(EndpointDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForEndpoint($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForEndpoint($obj);
			}
		}
		return $success;
	}

	/**
	 * Named fetches
	 */

	/**
	 * Attribute editing
	 */
	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Job::type_code,
				Job::minute,
				Job::hour,
				Job::dayOfWeek,
				Job::next
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesMap() {
		return array(
			Job::type_code => Model::TO_ONE_TYPE,
			Job::endpoint_id => Model::TO_ONE_TYPE,
			Job::enabled => Model::FLAG_TYPE,
			Job::one_shot => Model::FLAG_TYPE,
			Job::fail_count => Model::INT_TYPE,
			Job::elapsed => Model::INT_TYPE,
			Job::minute => Model::TEXT_TYPE,
			Job::hour => Model::TEXT_TYPE,
			Job::dayOfWeek => Model::TEXT_TYPE,
			Job::parameter => Model::TEXTAREA_TYPE,
			Job::next => Model::DATE_TYPE,
			Job::last_run => Model::DATE_TYPE,
			Job::last_fail => Model::DATE_TYPE,
			Job::created => Model::DATE_TYPE
		);
	}

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) === false || is_null($object) == true) {
			switch ($attr) {
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
				case Job::type_code:
					$job_type_model = Model::Named('Job_Type');
					$fkObject = $job_type_model->objectForId( $value );
					break;
				case Job::endpoint_id:
					$endpoint_model = Model::Named('Endpoint');
					$fkObject = $endpoint_model->objectForId( $value );
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
	function validate_type_code($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Job::type_code,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_endpoint_id($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_enabled($object = null, $value)
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
				Job::enabled,
				"FILTER_VALIDATE_BOOLEAN"
			);
		}
		return null;
	}
	function validate_one_shot($object = null, $value)
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
				Job::one_shot,
				"FILTER_VALIDATE_BOOLEAN"
			);
		}
		return null;
	}
	function validate_fail_count($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Job::fail_count,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
	function validate_elapsed($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Job::elapsed,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
	function validate_minute($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Job::minute,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_hour($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Job::hour,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_dayOfWeek($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Job::dayOfWeek,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_parameter($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_next($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Job::next,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_last_run($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_last_fail($object = null, $value)
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
				Job::created,
				"IMMUTABLE"
			);
		}
		return null;
	}
}

?>
