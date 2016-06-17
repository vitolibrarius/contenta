<?php

namespace model\jobs;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \model\jobs\Job_RunningDBO as Job_RunningDBO;

/* import related objects */
use \model\jobs\Job as Job;
use \model\jobs\JobDBO as JobDBO;
use \model\jobs\Job_Type as Job_Type;
use \model\jobs\Job_TypeDBO as Job_TypeDBO;

class Job_Running extends _Job_Running
{
	/**
	 *	Create/Update functions
	 */
	public function createObject( array $values = array())
	{
		if ( isset($values) ) {
			// massage values as necessary
		}

		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Job_RunningDBO ) {
			// massage values as necessary
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		return array(
			Job_Running::job_id => Model::INT_TYPE,
			Job_Running::type_id => Model::INT_TYPE,
			Job_Running::processor => Model::TEXT_TYPE,
			Job_Running::guid => Model::TEXT_TYPE,
			Job_Running::pid => Model::INT_TYPE,
			Job_Running::desc => Model::TEXT_TYPE,
			Job_Running::created => Model::DATE_TYPE
		);
	}

	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Job_Running::processor,
				Job_Running::pid
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributeIsEditable($object = null, $type = null, $attr)
	{
		// add customization here
		return parent::attributeIsEditable($object, $type, $attr);
	}

	/*
	public function attributeRestrictionMessage($object = null, $type = null, $attr)	{ return null; }
	public function attributePlaceholder($object = null, $type = null, $attr)	{ return null; }
	*/

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) === false || is_null($object) == true) {
			switch ($attr) {
			}
		}
		return parent::attributeDefaultValue($object, $type, $attr);
	}

	public function attributeEditPattern($object = null, $type = null, $attr)
	{
		return null;
	}

	public function attributeOptions($object = null, $type = null, $attr)
	{
		if ( $attr == Job_Running::job_id ) {
			$model = Model::Named('Job');
			return $model->allObjects();
		}
		if ( $attr == Job_Running::type_id ) {
			$model = Model::Named('Job_Type');
			return $model->allObjects();
		}
		return null;
	}

	/** Validation */
	function validate_job_id($object = null, $value)
	{
		return parent::validate_job_id($object, $value);
	}

	function validate_type_id($object = null, $value)
	{
		return parent::validate_type_id($object, $value);
	}

	function validate_processor($object = null, $value)
	{
		return parent::validate_processor($object, $value);
	}

	function validate_guid($object = null, $value)
	{
		return parent::validate_guid($object, $value);
	}

	function validate_pid($object = null, $value)
	{
		return parent::validate_pid($object, $value);
	}

	function validate_desc($object = null, $value)
	{
		return parent::validate_desc($object, $value);
	}

	function validate_created($object = null, $value)
	{
		return parent::validate_created($object, $value);
	}

	public function clearFinishedProcesses()
	{
		$allRunning = $this->allObjects();
		if ( is_array($allRunning) ) {
			$shell = "ps " . ((PHP_OS === 'Darwin') ? ' ax ' : '') . "| awk '{print $1}'";
			$output = shell_exec(  $shell );
			$pids = explode(PHP_EOL, $output);

			foreach ( $allRunning as $jobrunning ) {
				if ( in_array($jobrunning->pid, $pids) == false ) {
					// process is done
					$this->deleteObject($jobrunning);
				}
			}
		}
		return true;
	}

	public function updateDesc( $jobrunning = null, $desc = '' )
	{
		if ( $jobrunning instanceof Job_RunningDBO && strlen($desc) > 0) {
			if ( $this->updateObject( $jobrunning, array( Job_Running::desc => $desc )) ) {
				return $this->refreshObject($jobrunning);
			}
		}
		return false;
	}
}

?>
