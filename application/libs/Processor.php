<?php

use \Config as Config;
use \Database as Database;
use \Localized as Localized;
use \Logger as Logger;
use \Exception as Exception;

use utilities\Metadata as Metadata;


/**
 * Class Processor
 */
abstract class Processor
{
	/**
	 * loads the Processor with the given name.
	 * @param $name string name of the Processor
	 */
	public static function Named($name, $guid)
	{
		isset($name) || die('Processor name missing.');
		isset($guid) || die('Processor unique id missing.');

		// converts table names like "log_level" to "Log_Level" to match the classname
		$parts = explode("_", $name);
		$parts = array_map('ucfirst', $parts);
		$className = "processor\\" . implode("_", $parts);
		return new $className($guid);
	}

	public function __construct($guid)
	{
		isset($guid) || die('Processor unique id missing.');


		$this->guid = $guid;
		$reflect = new ReflectionClass($this);
		$this->type = $reflect->getShortName();

		$root = Config::GetProcessing();
		$processingRoot = appendPath($root, $this->type );
		makeRequiredDirectory($processingRoot, 'processing subdirectory for ' . $this->type );

		$this->workingDir = appendPath($processingRoot, $guid);
	}

	public function deleteWorkingDirectory()
	{
		if (workingDirectoryExists() == true && destroy_dir(workingDirectory()) == false) {
			Logger::logInfo("Error destroying working directory " . $this->type . '/' . $this->guid);
			return false;
		}
		return true;
	}

	public function workingDirectory($filename = null)
	{
		if (is_null($filename) || strlen($filename) == 0) {
			return $this->workingDir;
		}

		return appendPath($this->workingDir, $filename);
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

	public function metadata()
	{
		if ( isset($this->metatdata) == false) {
			$this->createWorkingDirectory();
			$this->metafile = Metadata::forDirectory($this->workingDirectory());
		}
		return $this->metafile;
	}

	public function isMeta($key)
	{
		return $this->metadata()->isMeta($key);
	}

	public function setMeta($key, $value)
	{
		return $this->metadata()->setMeta($key, $value);
	}

	public function getMeta($key)
	{
		return $this->metadata()->getMeta($key);
	}


	public abstract function processData();
}
