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
use \model\Endpoint as Endpoint;
use \model\EndpointDBO as EndpointDBO;

/** Sample Creation script */
		/** JOB */
/*
		$sql = "CREATE TABLE IF NOT EXISTS job ( "
			. Job::id . " INTEGER PRIMARY KEY, "
			. Job::type_id . " INTEGER, "
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
			. "FOREIGN KEY (". Job::endpoint_id .") REFERENCES " . Endpoint::TABLE . "(" . Endpoint::id . ")"
		. ")";
		$this->sqlite_execute( "job", $sql, "Create table job" );

*/
abstract class _Job extends Model
{
	const TABLE = 'job';
	const id = 'id';
	const type_id = 'type_id';
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
			Job::type_id,
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

	/**
	 *	Simple fetches
	 */





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







	public function allForJobType($obj)
	{
		return $this->allObjectsForFK(Job::type_id, $obj, $this->sortOrder(), 50);
	}
	public function allForEndpoint($obj)
	{
		return $this->allObjectsForFK(Job::endpoint_id, $obj, $this->sortOrder(), 50);
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "job_type":
					return array( Job::type_id, "id"  );
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
			if ( isset($values['jobType']) ) {
				$local_jobType = $values['jobType'];
				if ( $local_jobType instanceof Job_TypeDBO) {
					$values[Job::type_id] = $local_jobType->id;
				}
				else if ( is_integer( $local_jobType) ) {
					$params[Job::type_id] = $local_jobType;
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
					$values[Job::type_id] = $local_jobType->id;
				}
				else if ( is_integer( $local_jobType) ) {
					$params[Job::type_id] = $values['jobType'];
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
			// does not own Job_Type
			// does not own Endpoint
			return parent::deleteObject($object);
		}

		return false;
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
	 *	Named fetches
	 */

	/** Set attributes */
	public function setType_id( JobDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Job::type_id => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setEndpoint_id( JobDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Job::endpoint_id => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setEnabled( JobDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Job::enabled => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setOne_shot( JobDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Job::one_shot => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setFail_count( JobDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Job::fail_count => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setElapsed( JobDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Job::elapsed => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setMinute( JobDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Job::minute => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setHour( JobDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Job::hour => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setDayOfWeek( JobDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Job::dayOfWeek => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setParameter( JobDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Job::parameter => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setNext( JobDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Job::next => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setLast_run( JobDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Job::last_run => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setLast_fail( JobDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Job::last_fail => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setCreated( JobDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Job::created => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}


	/** Validation */
	function validate_type_id($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Job::type_id,
				"FILTER_VALIDATE_INT"
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

		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Job::endpoint_id,
				"FILTER_VALIDATE_INT"
			);
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
