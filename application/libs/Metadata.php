<?php

use utilities\Metadata_json as Metadata_json;
use utilities\Metadata_sqlite as Metadata_sqlite;

interface MetadataInterface {
	public static function hasMetadataFile($path, $filename);
}

abstract class Metadata
{
	const TYPE_SQLITE = 'sqlite';
	const TYPE_JSON = 'json';

	public $path;
	public $filename;

	function __construct($fullpath)
	{
		$this->path = dirname($fullpath);
		$this->filename = basename($fullpath);
		is_dir($this->path) || die("Unable to find directory for " . $this->path);
	}

	public static function forDirectory($fullpath, $type = Metadata::TYPE_JSON)
	{
		return Metadata::forDirectoryAndFile( $fullpath, null, $type );
	}

	public static function forDirectoryAndFile($path, $filename = null, $type = Metadata::TYPE_JSON)
	{
		$j_file = (is_null($filename) ? Metadata_json::DefaultFilename : $filename);
		$s_file = (is_null($filename) ? Metadata_sqlite::DefaultFilename : $filename);

		// always use existing metadata
		if ( Metadata_json::hasMetadataFile( $path, $j_file) == true ) {
			return new Metadata_json( appendPath($path, $j_file));
		}
		else if (Metadata_sqlite::hasMetadataFile( $path, $s_file ) == true ) {
			return new Metadata_sqlite( appendPath($path, $s_file));
		}

		if ( $type === Metadata::TYPE_JSON ) {
			return new Metadata_json( appendPath($path, $j_file));
		}

		return new Metadata_sqlite( appendPath($path, $s_file));
	}

	public function fullpath() {
		return appendPath($this->path, $this->filename);
	}

	public function deleteMetadataFile() {
		return unlink(appendPath($this->path, $this->filename));
	}


	public function isMeta($key)
	{
		$test = $this->getMeta($key);
		return (is_null($test) == false);
	}


	/**
	 * Counts the number of entries for 'key' or if null the total top level keys
	 * @param mixed $key
	 */
	public abstract function metaCount($key = null);

	/**
	 * sets a specific value to a specific key of the session
	 * @param mixed $key
	 * @param mixed $value
	 */
	public abstract function setMeta($key, $value);

	/**
	 * gets/returns the value of a specific key of the metadata
	 * @param mixed $key Usually a string, path may be separated using '/', so 'source/subkey/itemkey'
	 * @return mixed
	 */
	public abstract function getMeta($key, $default = null);
}

?>
