<?php

namespace model\jobs;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\jobs\Job_Type as Job_Type;

/* import related objects */
use \model\jobs\Job_Running as Job_Running;
use \model\jobs\Job_RunningDBO as Job_RunningDBO;
use \model\jobs\Job as Job;
use \model\jobs\JobDBO as JobDBO;

class Job_TypeDBO extends _Job_TypeDBO
{
	public function jsonParameters() {
		if ( isset($this->parameter) ) {
			$jsonData = json_decode($this->parameter, true);
			if ( json_last_error() != 0 ) {
				throw new \Exception( jsonErrorString(json_last_error()) . "'" . $this->parameter . "'" );
			}
		}
		return (isset($jsonData) ? $jsonData : array());
	}
}

?>
