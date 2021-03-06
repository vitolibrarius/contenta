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

/** Generated class, do not edit.
 */
abstract class _Job_Type extends Model
{
	const TABLE = 'job_type';

	// attribute keys
	const code = 'code';
	const name = 'name';
	const desc = 'desc';
	const processor = 'processor';
	const parameter = 'parameter';
	const scheduled = 'scheduled';
	const requires_endpoint = 'requires_endpoint';

	// relationship keys
	const jobsRunning = 'jobsRunning';
	const jobs = 'jobs';

	public function modelName()
	{
		return "Job_Type";
	}

	public function dboName()
	{
		return '\model\jobs\Job_TypeDBO';
	}

	public function tableName() { return Job_Type::TABLE; }
	public function tablePK() { return Job_Type::code; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Job_Type::name)
		);
	}

	public function allColumnNames()
	{
		return array(
			Job_Type::code,
			Job_Type::name,
			Job_Type::desc,
			Job_Type::processor,
			Job_Type::parameter,
			Job_Type::scheduled,
			Job_Type::requires_endpoint
		);
	}

	public function allAttributes()
	{
		return array(
			Job_Type::name,
			Job_Type::desc,
			Job_Type::processor,
			Job_Type::parameter,
			Job_Type::scheduled,
			Job_Type::requires_endpoint
		);
	}

	public function allForeignKeys()
	{
		return array();
	}

	public function allRelationshipNames()
	{
		return array(
			Job_Type::jobsRunning,
			Job_Type::jobs
		);
	}

	public function attributes()
	{
		return array(
			Job_Type::name => array('length' => 256,'type' => 'TEXT'),
			Job_Type::desc => array('length' => 4096,'type' => 'TEXT'),
			Job_Type::processor => array('length' => 256,'type' => 'TEXT'),
			Job_Type::parameter => array('length' => 4096,'type' => 'TEXT'),
			Job_Type::scheduled => array('type' => 'BOOLEAN'),
			Job_Type::requires_endpoint => array('type' => 'BOOLEAN')
		);
	}

	public function relationships()
	{
		return array(
			Job_Type::jobsRunning => array(
				'destination' => 'Job_Running',
				'ownsDestination' => true,
				'isMandatory' => false,
				'isToMany' => true,
				'joins' => array( 'code' => 'type_code')
			),
			Job_Type::jobs => array(
				'destination' => 'Job',
				'ownsDestination' => true,
				'isMandatory' => false,
				'isToMany' => true,
				'joins' => array( 'code' => 'type_code')
			)
		);
	}

	public function searchQualifiers( array $query )
	{
		$qualifiers = array();
		if ( is_array($query) ) {
			foreach( $query as $attr => $value ) {
				switch ($attr) {
			// Job_Type::code == TEXT
				case Job_Type::code:
					if (strlen($value) > 0) {
						$qualifiers[Job_Type::code] = Qualifier::Equals( Job_Type::code, $value );
					}
					break;

			// Job_Type::name == TEXT
				case Job_Type::name:
					if (strlen($value) > 0) {
						$qualifiers[Job_Type::name] = Qualifier::Equals( Job_Type::name, $value );
					}
					break;

			// Job_Type::desc == TEXT
				case Job_Type::desc:
					if (strlen($value) > 0) {
						$qualifiers[Job_Type::desc] = Qualifier::Equals( Job_Type::desc, $value );
					}
					break;

			// Job_Type::processor == TEXT
				case Job_Type::processor:
					if (strlen($value) > 0) {
						$qualifiers[Job_Type::processor] = Qualifier::Equals( Job_Type::processor, $value );
					}
					break;

			// Job_Type::parameter == TEXT
				case Job_Type::parameter:
					if (strlen($value) > 0) {
						$qualifiers[Job_Type::parameter] = Qualifier::Equals( Job_Type::parameter, $value );
					}
					break;

			// Job_Type::scheduled == BOOLEAN
				case Job_Type::scheduled:
					$v = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
					if (is_null($v) == false) {
						$qualifiers[Job_Type::scheduled] = Qualifier::Equals( Job_Type::scheduled, $v );
					}
					break;

			// Job_Type::requires_endpoint == BOOLEAN
				case Job_Type::requires_endpoint:
					$v = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
					if (is_null($v) == false) {
						$qualifiers[Job_Type::requires_endpoint] = Qualifier::Equals( Job_Type::requires_endpoint, $v );
					}
					break;

				default:
					/* no type specified for Job_Type::requires_endpoint */
					break;
				}
			}
		}
		return $qualifiers;
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


	public function allForDesc($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Job_Type::desc, $value, null, $limit);
	}


	public function allForProcessor($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Job_Type::processor, $value, null, $limit);
	}


	public function allForParameter($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Job_Type::parameter, $value, null, $limit);
	}





	/**
	 * Simple relationship fetches
	 */

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "job_running":
					return array( Job_Type::code, "type_code"  );
					break;
				case "job":
					return array( Job_Type::code, "type_code"  );
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
			if ( $job_running_model->deleteAllForKeyValue(Job_Running::type_code, $object->code) == false ) {
				return false;
			}
			$job_model = Model::Named('Job');
			if ( $job_model->deleteAllForKeyValue(Job::type_code, $object->code) == false ) {
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

	public function attributesMap() {
		return array(
			Job_Type::name => Model::TEXT_TYPE,
			Job_Type::desc => Model::TEXTAREA_TYPE,
			Job_Type::processor => Model::TEXT_TYPE,
			Job_Type::parameter => Model::TEXTAREA_TYPE,
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

	/*
	 * return the foreign key object
	 */
	public function attributeObject($object = null, $type = null, $attr, $value)
	{
		$fkObject = false;
		if ( isset( $attr ) ) {
			switch ( $attr ) {
				default:
					break;
			}
		}
		return $fkObject;
	}

	/**
	 * Validation
	 */
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
