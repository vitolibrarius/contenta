<?php

namespace model\jobs;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

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
					return array( Job_Type::id, "type_id"  );
					break;
				case "job":
					return array( Job_Type::id, "type_id"  );
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
			if ( isset($values['code']) == false ) {
				$default_code = $this->attributeDefaultValue( null, null, Job_Type::code);
				if ( is_null( $default_code ) == false ) {
					$values['code'] = $default_code;
				}
			}
			if ( isset($values['name']) == false ) {
				$default_name = $this->attributeDefaultValue( null, null, Job_Type::name);
				if ( is_null( $default_name ) == false ) {
					$values['name'] = $default_name;
				}
			}
			if ( isset($values['desc']) == false ) {
				$default_desc = $this->attributeDefaultValue( null, null, Job_Type::desc);
				if ( is_null( $default_desc ) == false ) {
					$values['desc'] = $default_desc;
				}
			}
			if ( isset($values['processor']) == false ) {
				$default_processor = $this->attributeDefaultValue( null, null, Job_Type::processor);
				if ( is_null( $default_processor ) == false ) {
					$values['processor'] = $default_processor;
				}
			}
			if ( isset($values['parameter']) == false ) {
				$default_parameter = $this->attributeDefaultValue( null, null, Job_Type::parameter);
				if ( is_null( $default_parameter ) == false ) {
					$values['parameter'] = $default_parameter;
				}
			}
			if ( isset($values['scheduled']) == false ) {
				$default_scheduled = $this->attributeDefaultValue( null, null, Job_Type::scheduled);
				if ( is_null( $default_scheduled ) == false ) {
					$values['scheduled'] = $default_scheduled;
				}
			}
			if ( isset($values['requires_endpoint']) == false ) {
				$default_requires_endpoint = $this->attributeDefaultValue( null, null, Job_Type::requires_endpoint);
				if ( is_null( $default_requires_endpoint ) == false ) {
					$values['requires_endpoint'] = $default_requires_endpoint;
				}
			}

			// default conversion for relationships
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Job_Type ) {
		}

		return parent::updateObject($object, $values);
	}

	/**
	 *	Delete functions
	 */
	public function deleteObject( DataObject $object = null)
	{
		if ( $object instanceof Job_TypeDBO )
		{
			$job_running_model = Model::Named('Job_Running');
			if ( $job_running_model->deleteAllForKeyValue(Job_Running::type_id, $this->id) == false ) {
				return false;
			}
			$job_model = Model::Named('Job');
			if ( $job_model->deleteAllForKeyValue(Job::type_id, $this->id) == false ) {
				return false;
			}
			return parent::deleteObject($object);
		}

		return false;
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
				Job_Type::code
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesMap() {
		return array(
			Job_Type::code => Model::TEXT_TYPE,
			Job_Type::name => Model::TEXT_TYPE,
			Job_Type::desc => Model::TEXT_TYPE,
			Job_Type::processor => Model::TEXT_TYPE,
			Job_Type::parameter => Model::TEXT_TYPE,
			Job_Type::scheduled => Model::FLAG_TYPE,
			Job_Type::requires_endpoint => Model::FLAG_TYPE
		);
	}

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) === false || is_null($object) == true) {
			switch ($attr) {
				case Job_Type::scheduled:
					return true;
				case Job_Type::requires_endpoint:
					return true;
			}
		}
		return parent::attributeDefaultValue($object, $type, $attr);
	}

	/**
	 * Validation
	 */
	function validate_code($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
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
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

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
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_processor($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
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
	function validate_scheduled($object = null, $value)
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
				Job_Type::scheduled,
				"FILTER_VALIDATE_BOOLEAN"
			);
		}
		return null;
	}
	function validate_requires_endpoint($object = null, $value)
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
				Job_Type::requires_endpoint,
				"FILTER_VALIDATE_BOOLEAN"
			);
		}
		return null;
	}
}

?>
