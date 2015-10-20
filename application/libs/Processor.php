<?php

use \Config as Config;
use \Database as Database;
use \Localized as Localized;
use \Logger as Logger;
use \Exception as Exception;
use \Session as Session;
use \Metadata as Metadata;

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

		$this->purgeOnExit = false;
		$this->guid = $guid;
		$this->type = get_short_class($this);

		$root = Config::GetProcessing();
		$processingRoot = appendPath($root, $this->type );
		makeRequiredDirectory($processingRoot, 'processing subdirectory for ' . $this->type );

		$this->workingDir = appendPath($processingRoot, $guid);
	}

	function __destruct()
	{
		if ( isset($this->purgeOnExit) && $this->purgeOnExit == true ) {
			$this->deleteWorkingDirectory();
		}
	}

	public function setPurgeOnExit($trueFalse = false)
	{
		$this->purgeOnExit = $trueFalse;
	}

	public function deleteWorkingDirectory()
	{
		if ( is_sub_dir($this->workingDirectory(), Config::GetProcessing()) ) {
			if ($this->workingDirectoryExists() == true ) {
				$purged = destroy_dir($this->workingDirectory());
				if ( $purged != true ) {
					Logger::LogError("Error destroying working directory " . $this->workingDirectory(), $this->type, $this->guid);
					return false;
				}
			}
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
			$this->metafile = Metadata::forDirectory($this->workingDirectory(), Metadata::TYPE_SQLITE);
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

	public function getMeta($key, $default = null)
	{
		return $this->metadata()->getMeta($key, $default);
	}

	public function setMetaBoolean( $path, $newValue = false )
	{
		$oldValue = $this->getMeta( $path );
		if ( $oldValue == null || $oldValue == false ) {
			$this->setMeta( $path, $newValue );
		}
	}

	public function getMetaBoolean( $path, $default = false)
	{
		$oldValue = $this->getMeta( $path );
		return ( is_null($oldValue) ? boolval($default) : $oldValue );
	}

	public function daemonizeProcess()
	{
		if ( Session::isUserLoggedIn() ) {
			$user = Model::Named('Users')->objectForId(Session::Get('user_id'));
			return Daemonize( get_short_class($this), $user->api_hash, $this->guid);
		}

		return false;
	}

	public function initializationParams(array $specialParams = array())
	{
		foreach( $specialParams as $specialKey => $specialValue ) {
			if ( property_exists($this, $specialKey)) {
				$this->{$specialKey} = $specialValue;
			}
			else if (method_exists($this, $specialKey)) {
				if ( is_array($specialValue) ) {
					call_user_func_array(array($this, $specialKey), $specialValue );
				}
				else {
					$this->{$specialKey}( $specialValue );
				}
			}
			else {
				throw new \Exception( "bad parameter $specialKey = " . var_export($specialValue, true));
			}
		}
	}

	public abstract function processData();
}
