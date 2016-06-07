<?php

namespace model\jobs;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \model\jobs\Job_TypeDBO as Job_TypeDBO;

/* import related objects */
use \model\jobs\Job_Running as Job_Running;
use \model\jobs\Job_RunningDBO as Job_RunningDBO;
use \model\jobs\Job as Job;
use \model\jobs\JobDBO as JobDBO;

/** Sample Creation script */
		/** JOB_TYPE */
/*
		$sql = "CREATE TABLE IF NOT EXISTS job_type ( "
			. Job_Type::id . " INTEGER PRIMARY KEY, "
			. Job_Type::code . " TEXT, "
			. Job_Type::name . " TEXT, "
			. Job_Type::desc . " TEXT, "
			. Job_Type::processor . " TEXT, "
			. Job_Type::parameter . " TEXT, "
			. Job_Type::scheduled . " INTEGER, "
			. Job_Type::requires_endpoint . " INTEGER "
		. ")";
		$this->sqlite_execute( "job_type", $sql, "Create table job_type" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS job_type_code on job_type (code)';
		$this->sqlite_execute( "job_type", $sql, "Index on job_type (code)" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS job_type_name on job_type (name)';
		$this->sqlite_execute( "job_type", $sql, "Index on job_type (name)" );
*/
abstract class _Job_Type extends Model
{
	const TABLE = 'job_type';
	const id = 'id';
	const code = 'code';
	const name = 'name';
	const desc = 'desc';
	const processor = 'processor';
	const parameter = 'parameter';
	const scheduled = 'scheduled';
	const requires_endpoint = 'requires_endpoint';

	public function tableName() { return Job_Type::TABLE; }
	public function tablePK() { return Job_Type::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Job_Type::name)
		);
	}

	public function allColumnNames()
	{
		return array(
			Job_Type::id,
			Job_Type::code,
			Job_Type::name,
			Job_Type::desc,
			Job_Type::processor,
			Job_Type::parameter,
			Job_Type::scheduled,
			Job_Type::requires_endpoint
		);
	}

	/**
	 *	Simple fetches
	 */
	public function objectForCode($value)
	{
		return $this->singleObjectForKeyValue(Job_Type::code, $value);
	}

	public function objectForName($value)
	{
		return $this->singleObjectForKeyValue(Job_Type::name, $value);
	}

	public function allForDesc($value)
	{
		return $this->allObjectsForKeyValue(Job_Type::desc, $value);
	}

	public function allForProcessor($value)
	{
		return $this->allObjectsForKeyValue(Job_Type::processor, $value);
	}

	public function allForParameter($value)
	{
		return $this->allObjectsForKeyValue(Job_Type::parameter, $value);
	}



	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "job_running":
					return array( Job_Type::id, "job_type_id"  );
					break;
				case "job":
					return array( Job_Type::id, "job_type_id"  );
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
			if ( isset($values['jobsRunning']) ) {
				$local_jobsRunning = $values['jobsRunning'];
				if ( $local_jobsRunning instanceof Job_RunningDBO) {
					$values[Job_Type::id] = $local_jobsRunning->job_type_id;
				}
				else if ( is_integer( $local_jobsRunning) ) {
					$params[Job_Type::id] = $local_jobsRunning;
				}
			}
			if ( isset($values['jobs']) ) {
				$local_jobs = $values['jobs'];
				if ( $local_jobs instanceof JobDBO) {
					$values[Job_Type::id] = $local_jobs->job_type_id;
				}
				else if ( is_integer( $local_jobs) ) {
					$params[Job_Type::id] = $local_jobs;
				}
			}
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Job_Type ) {
			if ( isset($values['jobsRunning']) ) {
				$local_jobsRunning = $values['jobsRunning'];
				if ( $local_jobsRunning instanceof Job_RunningDBO) {
					$values[Job_Type::id] = $local_jobsRunning->job_type_id;
				}
				else if ( is_integer( $local_jobsRunning) ) {
					$params[Job_Type::id] = $values['jobsRunning'];
				}
			}
			if ( isset($values['jobs']) ) {
				$local_jobs = $values['jobs'];
				if ( $local_jobs instanceof JobDBO) {
					$values[Job_Type::id] = $local_jobs->job_type_id;
				}
				else if ( is_integer( $local_jobs) ) {
					$params[Job_Type::id] = $values['jobs'];
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
		if ( $object instanceof Job_Type )
		{
			$job_running_model = Model::Named('Job_Running');
			if ( $job_running_model->deleteAllForKeyValue(Job_Running::job_type_id, $this->id) == false ) {
				return false;
			}
			$job_model = Model::Named('Job');
			if ( $job_model->deleteAllForKeyValue(Job::job_type_id, $this->id) == false ) {
				return false;
			}
			return parent::deleteObject($object);
		}

		return false;
	}


	/**
	 *	Named fetches
	 */

	/** Set attributes */
	public function setCode( Job_TypeDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Job_Type::code => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setName( Job_TypeDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Job_Type::name => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setDesc( Job_TypeDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Job_Type::desc => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setProcessor( Job_TypeDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Job_Type::processor => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setParameter( Job_TypeDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Job_Type::parameter => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setScheduled( Job_TypeDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Job_Type::scheduled => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setRequires_endpoint( Job_TypeDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Job_Type::requires_endpoint => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}


	/** Validation */
	function validate_code($object = null, $value)
	{
		$value = trim($value);
		if (empty($value)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Job_Type::code,
				"FIELD_EMPTY"
			);
		}
		// make sure Code is unique
		$existing = $this->objectForCode($value);
		if ( $existing != false && ( is_null($object) || $existing->id != $object->id)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Job_Type::code,
				"UNIQUE_FIELD_VALUE"
			);
		}
		return null;
	}
	function validate_name($object = null, $value)
	{
		$value = trim($value);
		// make sure Name is unique
		$existing = $this->objectForName($value);
		if ( $existing != false && ( is_null($object) || $existing->id != $object->id)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Job_Type::name,
				"UNIQUE_FIELD_VALUE"
			);
		}
		return null;
	}
	function validate_desc($object = null, $value)
	{
		$value = trim($value);
		return null;
	}
	function validate_processor($object = null, $value)
	{
		$value = trim($value);
		return null;
	}
	function validate_parameter($object = null, $value)
	{
		$value = trim($value);
		return null;
	}
	function validate_scheduled($object = null, $value)
	{
		if ( is_null($value) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Job_Type::scheduled,
				"FIELD_EMPTY"
			);
		}

		// Returns TRUE for "1", "true", "on" and "yes"
		// Returns FALSE for "0", "false", "off" and "no"
		// Returns NULL otherwise.
		$v = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
		if (is_null($v)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Job_Type::scheduled,
				"FILTER_VALIDATE_BOOLEAN"
			);
		}
		return null;
	}
	function validate_requires_endpoint($object = null, $value)
	{
		if ( is_null($value) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Job_Type::requires_endpoint,
				"FIELD_EMPTY"
			);
		}

		// Returns TRUE for "1", "true", "on" and "yes"
		// Returns FALSE for "0", "false", "off" and "no"
		// Returns NULL otherwise.
		$v = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
		if (is_null($v)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Job_Type::requires_endpoint,
				"FILTER_VALIDATE_BOOLEAN"
			);
		}
		return null;
	}
}

?>
