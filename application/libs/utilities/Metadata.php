<?php

namespace utilities;

use \Logger as Logger;

class Metadata
{
	const DefaultFilename = 'metadata.json';
	public $path;
	public $filename;
	public $jsonData;

	function __construct($fullpath)
	{
		$this->path = dirname($fullpath);
		$this->filename = basename($fullpath);
		is_dir($this->path) || die("Unable to find directory for " . $this->path);

		$this->readMetadata();
	}

	public static function forDirectory($fullpath) {
		return new Metadata(appendPath($fullpath, Metadata::DefaultFilename));
	}

	public function fullpath() {
		return appendPath($this->path, $this->filename);
	}

	public function isMeta($key)
	{
		$test = $this->getMeta($key);
		return (is_null($test) == false);
	}

	/**
	 * sets a specific value to a specific key of the session
	 * @param mixed $key
	 * @param mixed $value
	 */
	public function setMeta($key, $value)
	{
		if ( isset($key) && strlen($key) > 0) {
			$newConfiguration = setValueForKeypath($key, $value, $this->jsonData);
			if ( is_array($newConfiguration) ) {
				$this->jsonData = $newConfiguration;
				$this->writeMetadata();
				return true;
			}
		}
		return false;
	}

	/**
	 * gets/returns the value of a specific key of the metadata
	 * @param mixed $key Usually a string, path may be separated using '/', so 'source/subkey/itemkey'
	 * @return mixed
	 */
	public function getMeta($key, $default = null)
	{
		if ( isset($key) && strlen($key) > 0) {
			$data = $this->readMetadata();
			$value = valueForKeypath($key, $data);
			return (is_null($value) ? $default : $value);
		}
		return $default;
	}

	public function readMetadata()
	{
		if ( is_null($this->jsonData) ) {
			if (is_file($this->fullpath())) {
				$this->jsonData = json_decode(file_get_contents($this->fullpath()), true);
				if ( json_last_error() != 0 ) {
					Logger::logError( 'Last error: ' . jsonErrorString(json_last_error()), get_class($this), 'readMetadata()');
					throw new \Exception( jsonErrorString(json_last_error()) );
				}
			}
			else {
				$this->jsonData = array();
			}
		}

		return $this->jsonData;
	}

	public function writeMetadata()
	{
		if (is_dir($this->path)) {
			$metadatafile = $this->fullpath();
			$returnValue = file_put_contents( $metadatafile, json_encode($this->jsonData, JSON_PRETTY_PRINT));
			if ( json_last_error() != 0 ) {
				Logger::logError( 'Last error: ' . jsonErrorString(json_last_error()), get_class($this), 'readMetadata()');
				throw new \Exception( jsonErrorString(json_last_error()) );
			}
			return $returnValue;
		}
		return false;
	}
}

?>
