<?php

// guard to ensure basic configuration is loaded
defined('SYSTEM_PATH') || exit("SYSTEM_PATH not found.");

class ConfigFileDoesNotExistException extends Exception {}
class ConfigFileNotValidException extends Exception {}
class ConfigFileEmptyException extends Exception {}

/**
 * Configuration class.
 * Reads the 'contenta.ini' file and gives callers values as requested
 */
class Config
{
	private static $instance;

	var $isInitialized = false;
	var $filename = null;
	var $configuration = array();

	final public static function instance()
	{
		if ( null == self::$instance ) {
			self::$instance = new Config();
			self::$instance->initialize();
		}
		return self::$instance;
	}

	final public static function dirMask()
	{
		return self::$instance->getInteger("Repository/dir_permission", 0755);
	}

	final public static function fileMask()
	{
		return self::$instance->getInteger("Repository/file_permission", 0644);
	}

	private function initialize()
	{
		if ( $this->isInitialized == false ) {
			$this->isInitialized = true;

			$this->filename = appendPath( SYSTEM_PATH, 'contenta.ini' );
			if (file_exists($this->filename) == false ) {
				throw new ConfigFileDoesNotExistException( "Unable to find configuration '" . $this->filename . "'");
			}

			$this->configuration = parse_ini_file ( $this->filename, true );
			if ( $this->validateConfiguration() == false ) {
				throw new ConfigFileNotValidException( "Errors validating configuration '" . $this->filename . "'");
			}
		}
	}

	private function validateConfiguration()
	{
		if ( is_array($this->configuration) == false || count($this->configuration) == 0 ) {
			throw new ConfigFileEmptyException("Configuration appears to be empty");
		}

		$repo_base_path = $this->repositoryDirectory();
		$media_path = $this->mediaDirectory();
		$cache_path = $this->cacheDirectory();
		$processing_path = $this->processingDirectory();


		return true;
	}

	/**
	 * gets/returns the value of a specific key of the config
	 * @param mixed $key Usually a string, path may be separated using '/', so 'Internet/appname'
	 * @return mixed
	 */
	public function get($key, $default = '')
	{
		$value = valueForKeypath($key, $this->configuration);
		return (is_null($value) ? $default : $value);
	}

	/**
	 * gets/returns the value of a specific key of the config
	 * @param mixed $key Usually a string, path may be separated using '/', so 'Internet/appname'
	 * @return mixed
	 */
	public function getInteger($key, $default = 0)
	{
		$value = $this->get($key, $default);
		return (is_null($value) ? $default : intval($value));
	}

	public function repositoryDirectory()
	{
		$repo_base_path = $this->get("Repository/path");
		makeRequiredDirectory($repo_base_path, 'Repository base');
		return $repo_base_path;
	}

	public function mediaDirectory()
	{
		$repo_base_path = $this->repositoryDirectory();
		$media_path = appendPath( $repo_base_path, "media" );
		makeRequiredDirectory($media_path, 'Media base');
		return $media_path;
	}

	public function cacheDirectory()
	{
		$cache_path = $this->get("Repository/cache", "_cache_");
		if ($cache_path[0] != '/') {
			// cache is relative to the repo
			$repo_base_path = $this->repositoryDirectory();
			$cache_path = appendPath( $repo_base_path, $cache_path );
		}

		makeRequiredDirectory($cache_path, 'cache');
		return $cache_path;
	}

	public function processingDirectory()
	{
		$processing_path = $this->get("Repository/processing", "_processing_");
		if ($processing_path[0] != '/') {
			// processing is relative to the repo
			$repo_base_path = $this->repositoryDirectory();
			$processing_path = appendPath( $repo_base_path, $processing_path );
		}

		makeRequiredDirectory($processing_path, 'processing');
		return $processing_path;
	}
}
