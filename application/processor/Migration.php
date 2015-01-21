<?php

namespace processor;

use \Processor as Processor;
use \Migrator as Migrator;
use \Config as Config;
use \Logger as Logger;

class Migration extends Processor
{
	function __construct($guid)
	{
		parent::__construct($guid);
	}

	public function processData()
	{
		$this->createWorkingDirectory();

		$config = Config::instance();
		$config->setValue("Logging/type", "File") || die("Failed to change the configured logger");
		$config->setValue("Logging/path", $this->workingDirectory() ) || die("Failed to change the configured logging path");
		Logger::resetInstance();

 		Migrator::Upgrade($this->workingDirectory());
	}

	public function migrationLogs()
	{
		$logs = array();
		foreach (scandir($this->workingDirectory()) as $file)
		{
			if (startsWith($file, "log_"))
			{
				$path = appendPath( $this->workingDirectory(), $file);
				$data = trim(file_get_contents($path));
				$logs[$file] = $data;
			}
		}
		return $logs;
	}
}
