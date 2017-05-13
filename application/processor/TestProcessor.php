<?php

namespace processor;

use \Processor as Processor;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;

use \model\jobs\Job_Type as Job_Type;
use \model\jobs\Job_Running as Job_Running;
use \model\jobs\Job as Job;

use \interfaces\ProcessStatusReporter as ProcessStatusReporter;

class TestProcessor extends Processor
{
	public $items;
	public $list;

	function __construct($guid)
	{
		parent::__construct($guid);
	}

	function RandomString()
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randstring = '';
		for ($i = 0; $i < 10; $i++) {
			$randstring .= $characters[rand(0, strlen($characters) -1)];
		}
		return $randstring;
	}

	function batch( $size = 0 )
	{
		if ( is_numeric($size) ) {
			$size = abs($size);
			$this->list = array();
			for ( $idx = 0; $idx < $size; $idx++ ) {
				$this->list[] = $this->RandomString();
			}
		}
		else {
			throw new \Exception( "Batch parameter is not a number '" . $size . "'");
		}
	}

	public function processData(ProcessStatusReporter $reporter = null)
	{
		echo ". items = $this->items" . PHP_EOL;
		foreach( $this->list as $idx=>$item ) {
			echo ". . $idx = $item" . PHP_EOL;
		}

		for ( $i =0;  $i < 10; $i++ ) {
			sleep(1);
		}
		echo PHP_EOL;
	}
}
