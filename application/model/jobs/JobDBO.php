<?php

namespace model\jobs;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\jobs\Job as Job;
use \utilities\CronEvaluator as CronEvaluator;

/* import related objects */
use \model\jobs\Job_Type as Job_Type;
use \model\jobs\Job_TypeDBO as Job_TypeDBO;
use \model\network\Endpoint as Endpoint;
use \model\network\EndpointDBO as EndpointDBO;

class JobDBO extends _JobDBO
{
	public $uuid;

	public function elapsedFormatted()
	{
		return formattedTimeElapsed($this->elapsed);
	}

	public function nextDate()
	{
		if ( isset($this->next) ) {
			if ( $this->next < time() ) {
				try {
					$m = (isset($this->minute) ? $this->minute : null);
					$h = (isset($this->hour) ? $this->hour : null);
					$d = (isset($this->dayOfWeek) ? $this->dayOfWeek : null);

					$cronEval = new CronEvaluator( $m, $h, $d );
					$nextRunDate = $cronEval->nextDate();
					$this->{Job::next}($nextRunDate->getTimestamp());
					return date("M d, Y H:i", $nextRunDate->getTimestamp());
				}
				catch ( \Exception $ve ) {
					return $ve;
				}
			}
		}
		return $this->formattedDate( Job::next, "M d, Y H:i" );
	}

	public function nextDates($num = 3)
	{
		$fmted = array();
		try {
			$m = (isset($this->minute) ? $this->minute : null);
			$h = (isset($this->hour) ? $this->hour : null);
			$d = (isset($this->dayOfWeek) ? $this->dayOfWeek : null);

			$cronEval = new CronEvaluator( $m, $h, $d );
			$nextRunDates = $cronEval->nextSeriesDates('now', $num);
			foreach( $nextRunDates as $nrd ) {
				$fmted[] = date("M d, Y H:i", $nrd->getTimestamp());
			}
		}
		catch ( \Exception $ve ) {
			$fmted[] = "Schedule Error: " . $ve->getMessage();
		}
		return $fmted;
	}

	public function displayName() {
		$type = $this->jobType();
		return (empty($type) ? 'Unknown' : $type->name);
	}

	public function displayDescription()
	{
		return $this->{"jobType/name"}() . " " . $this->{"endpoint/name"}();
	}

	public function jsonParameters() {
		$jsonData = array();
		$jobType = $this->jobType();
		if ( $jobType instanceof Job_TypeDBO ) {
			$jsonData = $jobType->jsonParameters();
		}

		if ( isset($this->parameter) ) {
			$override = json_decode($this->parameter, true);
			if ( json_last_error() != 0 ) {
				return jsonErrorString(json_last_error()) . "'" . $this->parameter . "'";
			}

			if (is_array($override) ) {
				foreach( $override as $key=>$value ) {
					$jsonData[$key] = $value;
				}
			}
		}
		return (isset($jsonData) ? $jsonData : array());
	}

	public function uuidParameter() {
		if ( isset($this->uuid) ) {
			return $this->uuid;
		}

		$jsonData = $this->jsonParameters();
		$this->uuid = (isset($jsonData["uuid"]) ? $jsonData["uuid"] : uuid());
		return $this->uuid;
	}
}

?>
