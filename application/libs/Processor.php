<?php


/**
 * Class Processor
 */
abstract class Processor
{
	public function __construct($guid)
	{
		isset($guid) || die('Processor unique id missing.');

		$reflect = new ReflectionClass($this);

		$this->guid = $guid;
		$this->type = $reflect->getShortName();

		$root = Config::GetProcessing();
		$processingRoot = appendPath($root, $this->type );
		makeRequiredDirectory($processingRoot, 'processing subdirectory for ' . get_class($this));

		$this->workingDir = appendPath($processingRoot, $guid);
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
