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

	/**
	 * gets/returns the value of a specific key of the config
	 * @param mixed $key Usually a string, path may be separated using '/', so 'Internet/appname'
	 * @return mixed
	 */
	public static function Get($key, $default = '')
	{
		return self::instance()->getValue($key, $default);
	}

	/**
	 * gets/returns the absolute path value of a specific key of the config.
	 * @param mixed $key Usually a string, key may be separated using '/', so 'Repository/path', the key must end with "path"
	 * @return string path or raise exception
	 */
	public static function GetPath($key, $default)
	{
		return self::instance()->absolutePathValue($key, $default);
	}

	public static function GetRepository()
	{
		return self::instance()->repositoryDirectory();
	}

	public static function GetMedia()
	{
		return self::instance()->mediaDirectory();
	}

	public static function GetCache()
	{
		return self::instance()->cacheDirectory();
	}

	public static function GetProcessing()
	{
		return self::instance()->processingDirectory();
	}


	/**
	 * gets/returns the value of a specific key of the config
	 * @param mixed $key Usually a string, path may be separated using '/', so 'Internet/appname'
	 * @return mixed
	 */
	public function GetInteger($key, $default = 0)
	{
		return self::instance()->getIntegerValue($key, $default);
	}

	final public static function dirMask()
	{
		return self::instance()->getIntegerValue("Repository/dir_permission", 0755);
	}

	final public static function fileMask()
	{
		return self::instance()->getIntegerValue("Repository/file_permission", 0644);
	}

	final public static function AppName()
	{
		return self::instance()->getValue("Internet/appname", "Contenta");
	}

	final public static function Web($path = null)
	{
		$web = self::instance()->getValue("Internet/web_dir", "contenta");
		if ( is_string($path) && strlen($path) > 0 ) {
			return appendPath($web, $path);
		}
		return $web;
	}

	final public static function Url($path = null)
	{
		$web = self::instance()->getValue("Internet/web_url", "http://localhost/contenta");
		if ( is_string($path) && strlen($path) > 0 ) {
			return appendPath($web, $path);
		}
		return $web;
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
	public function getValue($key, $default = '')
	{
		$value = valueForKeypath($key, $this->configuration);
		return (is_null($value) ? $default : $value);
	}

	public function setValue($key, $value)
	{
		$newConfiguration = setValueForKeypath($key, $value, $this->configuration);
		if ( is_array($newConfiguration) ) {
			$this->configuration = $newConfiguration;
			return true;
		}
		return false;
	}

	/**
	 * gets/returns the value of a specific key of the config
	 * @param mixed $key Usually a string, path may be separated using '/', so 'Internet/appname'
	 * @return mixed
	 */
	public function getIntegerValue($key, $default = 0)
	{
		$value = $this->getValue($key, $default);
		return (is_null($value) ? $default : intval($value));
	}

	/**
	 * gets/returns the absolute path value of a specific key of the config.
	 * @param mixed $key Usually a string, key may be separated using '/', so 'Repository/path', the key must end with "path"
	 * @return string path or raise exception
	 */
	public function absolutePathValue($key, $default)
	{
		if ( isset($key) && strlen($key) > 0) {
			if ( "Repository" === $key || "Repository/path" == $key ) {
				return $this->repositoryDirectory();
			}
			else {
				if ( endsWith($key, "path") == false ) {
					$key = appendPath( $key, "path" );
				}

				$path = $this->getValue( $key, $default );
				if ($path[0] != '/') {
					// cache is relative to the repo
					$repo_base_path = $this->repositoryDirectory();
					$path = appendPath( $repo_base_path, $path );
				}
				return $path;
			}
		}
		return null;
	}

	public function repositoryDirectory()
	{
		$repo_base_path = $this->getValue("Repository/path");
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
		$cache_path = $this->absolutePathValue("Repository/cache", "_cache_");
		makeRequiredDirectory($cache_path, 'cache');
		return $cache_path;
	}

	public function processingDirectory()
	{
		$processing_path = $this->absolutePathValue("Repository/processing", "_processing_");
		makeRequiredDirectory($processing_path, 'processing');
		return $processing_path;
	}
}
