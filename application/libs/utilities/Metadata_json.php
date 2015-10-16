<?php

namespace utilities;

use \Logger as Logger;
use \Metadata as Metadata;

class Metadata_json extends Metadata implements \MetadataInterface
{
	const DefaultFilename = 'metadata.json';
	public $jsonData;

	function __construct($fullpath)
	{
		parent::__construct($fullpath);
		$this->readMetadata();
	}

	public static function hasMetadataFile($path, $filename)
	{
		if ( is_dir($path) ) {
			$file = ((isset($filename) && strlen($filename) > 0) ? $filename : Metadata_json::DefaultFilename);
			return file_exists(appendPath($path, $file));
		}
		return false;
	}

	public function metaCount($key = null)
	{
		if (is_null($key) || strlen($key) == 0) {
			$data = $this->readMetadata();
			return count($data);
		}
		else {
			$data = $this->getMeta($key);
			if (is_null($data) == false) {
				if ( is_array($data) ) {
					return count($data);
				}
				else {
					return 1;
				}
			}
		}
		return 0;
	}

	/**
	 * sets a specific value to a specific key of the session
	 * @param mixed $key
	 * @param mixed $value
	 */
	public function setMeta($key, $value)
	{
		if ( isset($key) && strlen($key) > 0) {
			$newConfiguration = array_setValueForKeypath($key, $value, $this->jsonData);
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
			$value = array_valueForKeypath($key, $data);
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
