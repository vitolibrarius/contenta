<?php

namespace model\jobs;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \model\jobs\Job_RunningDBO as Job_RunningDBO;

/* import related objects */
use \model\jobs\Job as Job;
use \model\jobs\JobDBO as JobDBO;
use \model\jobs\Job_Type as Job_Type;
use \model\jobs\Job_TypeDBO as Job_TypeDBO;

/** Sample Creation script */
		/** JOB_RUNNING */
/*
		$sql = "CREATE TABLE IF NOT EXISTS job_running ( "
			. Job_Running::id . " INTEGER PRIMARY KEY, "
			. Job_Running::job_id . " INTEGER, "
			. Job_Running::job_type_id . " INTEGER, "
			. Job_Running::processor . " TEXT, "
			. Job_Running::guid . " TEXT, "
			. Job_Running::pid . " INTEGER, "
			. Job_Running::desc . " TEXT, "
			. Job_Running::created . " INTEGER "
		. ")";
		$this->sqlite_execute( "job_running", $sql, "Create table job_running" );

*/
abstract class _Job_Running extends Model
{
	const TABLE = 'job_running';
	const id = 'id';
	const job_id = 'job_id';
	const job_type_id = 'job_type_id';
	const processor = 'processor';
	const guid = 'guid';
	const pid = 'pid';
	const desc = 'desc';
	const created = 'created';

	public function tableName() { return Job_Running::TABLE; }
	public function tablePK() { return Job_Running::id; }

	public function sortOrder()
	{
		return array(
			array( 'desc' => Job_Running::created)
		);
	}

	public function allColumnNames()
	{
		return array(
			Job_Running::id,
			Job_Running::job_id,
			Job_Running::job_type_id,
			Job_Running::processor,
			Job_Running::guid,
			Job_Running::pid,
			Job_Running::desc,
			Job_Running::created
		);
	}

	/**
	 *	Simple fetches
	 */
	public function allForProcessor($value)
	{
		return $this->allObjectsForKeyValue(Job_Running::processor, $value);
	}

	public function allForGuid($value)
	{
		return $this->allObjectsForKeyValue(Job_Running::guid, $value);
	}

	public function allForDesc($value)
	{
		return $this->allObjectsForKeyValue(Job_Running::desc, $value);
	}



	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "job":
					return array( Job_Running::job_id, "id"  );
					break;
				case "job_type":
					return array( Job_Running::job_type_id, "id"  );
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
	public function base_create( $job_id, $job_type_id, $processor, $guid, $pid, $desc)
	{
		$obj = false;
		if ( isset($processor, $pid) ) {
			$params = array(
				Job_Running::job_id => (isset($job_id) ? $job_id : null),
				Job_Running::job_type_id => (isset($job_type_id) ? $job_type_id : null),
				Job_Running::processor => (isset($processor) ? $processor : null),
				Job_Running::guid => (isset($guid) ? $guid : null),
				Job_Running::pid => (isset($pid) ? $pid : null),
				Job_Running::desc => (isset($desc) ? $desc : null),
				Job_Running::created => time(),
			);


			list( $obj, $errorList ) = $this->createObject($params);
			if ( is_array($errorList) ) {
				return $errorList;
			}
		}
		return $obj;
	}

	public function base_update( Job_RunningDBO $obj,
		$job_id, $job_type_id, $processor, $guid, $pid, $desc)
	{
		if ( isset( $obj ) && is_null($obj) == false ) {
			$updates = array();

			if (isset($job_id) && (isset($obj->job_id) == false || $job_id != $obj->job_id)) {
				$updates[Job_Running::job_id] = $job_id;
			}
			if (isset($job_type_id) && (isset($obj->job_type_id) == false || $job_type_id != $obj->job_type_id)) {
				$updates[Job_Running::job_type_id] = $job_type_id;
			}
			if (isset($processor) && (isset($obj->processor) == false || $processor != $obj->processor)) {
				$updates[Job_Running::processor] = $processor;
			}
			if (isset($guid) && (isset($obj->guid) == false || $guid != $obj->guid)) {
				$updates[Job_Running::guid] = $guid;
			}
			if (isset($pid) && (isset($obj->pid) == false || $pid != $obj->pid)) {
				$updates[Job_Running::pid] = $pid;
			}
			if (isset($desc) && (isset($obj->desc) == false || $desc != $obj->desc)) {
				$updates[Job_Running::desc] = $desc;
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
		if ( $object instanceof Job_Running )
		{
			// does not own Job
			// does not own Job_Type
			return parent::deleteObject($object);
		}

		return false;
	}


	/**
	 *	Named fetches
	 */
	public function allForProcessorGUID( $processorName, $guid )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		$qualifiers[] = Qualifier::Equals( 'processor', $processorName);
		$qualifiers[] = Qualifier::Equals( 'guid', $guid);

		if ( count($qualifiers) > 0 ) {
			$select->where( Qualifier::Combine( 'AND', $qualifiers ));
		}

		$result = $select->fetchAll();
		return $result;
	}


	/** Set attributes */
	public function setJob_id( Job_RunningDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Job_Running::job_id => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setJob_type_id( Job_RunningDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Job_Running::job_type_id => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setProcessor( Job_RunningDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Job_Running::processor => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setGuid( Job_RunningDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Job_Running::guid => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setPid( Job_RunningDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Job_Running::pid => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setDesc( Job_RunningDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Job_Running::desc => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setCreated( Job_RunningDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Job_Running::created => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}


	/** Validation */
	function validate_job_id($object = null, $value)
	{
		if (isset($object->job_id) === false && empty($value) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Job_Running::job_id,
				"FIELD_EMPTY"
			);
		}
		return null;
	}
	function validate_job_type_id($object = null, $value)
	{
		if (isset($object->job_type_id) === false && empty($value) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Job_Running::job_type_id,
				"FIELD_EMPTY"
			);
		}
		return null;
	}
	function validate_processor($object = null, $value)
	{
		$value = trim($value);
		if (empty($value)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Job_Running::processor,
				"FIELD_EMPTY"
			);
		}
		return null;
	}
	function validate_guid($object = null, $value)
	{
		$value = trim($value);
		return null;
	}
	function validate_pid($object = null, $value)
	{
		if (empty($value)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Job_Running::pid,
				"FIELD_EMPTY"
			);
		}
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Job_Running::pid,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
	function validate_desc($object = null, $value)
	{
		$value = trim($value);
		return null;
	}
	function validate_created($object = null, $value)
	{
		if ( isset($object, $object->created) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Job_Running::created,
				"IMMUTABLE"
			);
		}
		return null;
	}
}

?>
