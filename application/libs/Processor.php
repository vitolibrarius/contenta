<?php


/**
 * Class Processor
 */
abstract class Processor
{
	public function __construct($guid)
	{
		isset($guid) || die('Processor unique id missing.');

		$root = Config::GetProcessing();
		$type = str_replace("Processor", "", get_class($this));
		$working = appendPath($root, $type);
		makeRequiredDirectory($working, 'processing subdirectory for ' . $type);

		$this->workingDir = appendPath($working, $guid);
		$this->guid = $guid;
		$this->type = $type;
	}

	public function workingDirectory()
	{
		return $this->workingDir;
	}

	public function workingDirectoryExists()
	{
		return is_dir($this->workingDir);
	}

	public function createWorkingDirectory()
	{
		if ( $this->workingDirectoryExists() == false ) {
			makeRequiredDirectory($this->workingDir, 'processing subdirectory for ' . $this->type . '/' . $this->guid);
		}
		return true;
	}

	public abstract function processData();
}
