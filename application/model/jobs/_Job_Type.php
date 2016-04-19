<?php

namespace model\jobs;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
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
				default:
					break;
			}
		}
		return parent::joinAttributes( $joinModel );
	}

	/**
	 *	Create/Update functions
	 */
	public function base_create( $code, $name, $desc, $processor, $parameter, $scheduled, $requires_endpoint)
	{
		$obj = false;
		if ( isset($code) ) {
			$params = array(
				Job_Type::code => (isset($code) ? $code : null),
				Job_Type::name => (isset($name) ? $name : null),
				Job_Type::desc => (isset($desc) ? $desc : null),
				Job_Type::processor => (isset($processor) ? $processor : null),
				Job_Type::parameter => (isset($parameter) ? $parameter : null),
				Job_Type::scheduled => (isset($scheduled) ? $scheduled : true),
				Job_Type::requires_endpoint => (isset($requires_endpoint) ? $requires_endpoint : true),
			);


			list( $obj, $errorList ) = $this->createObject($params);
			if ( is_array($errorList) ) {
				return $errorList;
			}
		}
		return $obj;
	}

	public function base_update( Job_TypeDBO $obj,
		$code, $name, $desc, $processor, $parameter, $scheduled, $requires_endpoint)
	{
		if ( isset( $obj ) && is_null($obj) == false ) {
			$updates = array();

			if (isset($code) && (isset($obj->code) == false || $code != $obj->code)) {
				$updates[Job_Type::code] = $code;
			}
			if (isset($name) && (isset($obj->name) == false || $name != $obj->name)) {
				$updates[Job_Type::name] = $name;
			}
			if (isset($desc) && (isset($obj->desc) == false || $desc != $obj->desc)) {
				$updates[Job_Type::desc] = $desc;
			}
			if (isset($processor) && (isset($obj->processor) == false || $processor != $obj->processor)) {
				$updates[Job_Type::processor] = $processor;
			}
			if (isset($parameter) && (isset($obj->parameter) == false || $parameter != $obj->parameter)) {
				$updates[Job_Type::parameter] = $parameter;
			}
			if (isset($scheduled) && (isset($obj->scheduled) == false || $scheduled != $obj->scheduled)) {
				$updates[Job_Type::scheduled] = $scheduled;
			}
			if (isset($requires_endpoint) && (isset($obj->requires_endpoint) == false || $requires_endpoint != $obj->requires_endpoint)) {
				$updates[Job_Type::requires_endpoint] = $requires_endpoint;
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
}

?>
