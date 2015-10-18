<?php

use \Config as Config;
use \Logger as Logger;

class Cache
{
	/*
		hour =	  3600
		day =	86400
		2days = 172800
		14 days = 1209600
	*/
	const TTL_MINUTE = 60;
	const TTL_HOUR = 3600;
	const TTL_DAY = 86400;
	const TTL_WEEK = 604800;

	const TimeToLive = 1209600;
	const FILELIST = 'filelist';
	const CACHE_TTL_MARKER = 'CacheTTLMarker';

	private static $instance;

	final public static function instance()
	{
		if ( null == self::$instance ) {
			self::$instance = new Cache();
			self::$instance->purgeExpired();
		}
		return self::$instance;
	}

	final public static function MakeKey()
	{
		$teeth = func_get_args();
		return join(DIRECTORY_SEPARATOR, $teeth);
	}

	final public static function Clear($key)
	{
		return self::instance()->clearCachedValue($key);
	}

	final public static function Fetch($key, $default = null, $customTTL = -1)
	{
		return self::instance()->fetchCachedValue($key, $default, $customTTL);
	}

	final public static function Store($key, $data = null)
	{
		return self::instance()->storeCachedValue($key, $data);
	}

	private function fullpath($key)
	{
		$rootDir = Config::GetCache();
		$shaKey = sha1($key);
		$subDir = substr($shaKey, 0, 2);
		$partialPath = appendPath($rootDir, $subDir);
		safe_mkdir($partialPath) || die('Failed to create cache sub-directory ' . $partialPath);
		return appendPath($partialPath, $shaKey);
	}

	public function fetchCachedValue($key, $default = null, $customTTL = -1)
	{
		$cache_path = $this->fullpath($key);

		if (!@file_exists($cache_path))
		{
			return false;
		}

		if ( $customTTL < Cache::TTL_MINUTE ) {
			$customTTL = Cache::TimeToLive;
		}

		if (filemtime($cache_path) < (time() - $customTTL))
		{
			$this->clear($key);
			return false;
		}

		if (!$fp = @fopen($cache_path, 'rb'))
		{
			return false;
		}

		flock($fp, LOCK_SH);

		$cache = false;

		if (filesize($cache_path) > 0)
		{
			$cache = unserialize(fread($fp, filesize($cache_path)));

			// update the access/mod time
			touch($cache_path);
		}

		flock($fp, LOCK_UN);
		fclose($fp);

		return $cache;
	}

	public function storeCachedValue($key, $data)
	{
		$cache_path = $this->fullpath($key);

		if ( ! $fp = fopen($cache_path, 'wb'))
		{
			return false;
		}

		if (flock($fp, LOCK_EX))
		{
			fwrite($fp, serialize($data));
			flock($fp, LOCK_UN);
		}
		else
		{
			return false;
		}
		fclose($fp);
		@chmod($cache_path, 0777);
		return true;
	}


	public function clearCachedValue($key)
	{
		$cache_path = $this->fullpath($key);

		if (file_exists($cache_path))
		{
			safe_unlink($cache_path);
			return true;
		}

		return false;
	}

	public function purgeExpired()
	{
		$rootDir = Config::GetCache();
		$ttl_marker_path = appendPath($rootDir, Cache::CACHE_TTL_MARKER);
		$purge = false;

		if (!@file_exists($ttl_marker_path)) {
			$purge = true;
		}
		else if (filemtime($ttl_marker_path) < (time() - (Cache::TimeToLive/3))) {
			$purge = true;
		}

		if ( $purge == true ) {
			Logger::logInfo('Purging Cache', get_class($this));
			$count = 0;
			$start = microtime(true);;
			$objects = new RecursiveIteratorIterator( new RecursiveDirectoryIterator($rootDir), RecursiveIteratorIterator::SELF_FIRST);
			foreach($objects as $name => $object) {
				if ( $object->isFile() ) {
					if ($object->getMTime() < (time() - Cache::TimeToLive)) {
						$count++;
						safe_unlink($object->getPathname());
					}
				}
			}
			touch($ttl_marker_path);
			Logger::logInfo('Purge Cache complete, ' . $count . ' items removed, elapsed ' . (microtime(true) - $start), get_class($this));
		}
		return $purge;
	}
}
?>
