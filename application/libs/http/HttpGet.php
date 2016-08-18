<?php

namespace http;

/**
 * HttpGet class
 *
 * handles the HttpGet stuff. creates HttpGet when no one exists, sets and
 * gets values, and closes the HttpGet properly (=logout). Those methods
 * are STATIC, which means you can call them with HttpGet::get(XXX);
 $search_html = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_SPECIAL_CHARS);
$search_url = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_ENCODED);

 */
class HttpGet
{
    private static $adapter = null;

	/**
	 * starts the HttpGet
	 */
    public static function init(\interfaces\GlobalAdapter $adapter = null)
	{
		if ( is_null($adapter)) {
			self::$adapter = new \http\GlobalGETAdapter();
		}
		else {
			self::$adapter = $adapter;
		}
	}

    protected static function adapter()
	{
		if ( is_null(self::$adapter)) {
			self::$adapter = new \http\GlobalGETAdapter();
		}
		return self::$adapter;
	}

	/**
	 * sets a specific value to a specific key of the HttpGet
	 * @param mixed $key
	 * @param mixed $value
	 */
	public static function set($key, $value)
	{
		self::adapter()->set($key, $value);
	}

	/**
	 * tests for a valid value
	 */
	public static function hasValue($key)
	{
		$value = self::get($key, null);
		return (is_null($value) ? false : true);
	}

	/**
	 * tests for a valid values
	 */
	public static function hasValues()
	{
		$keys = func_get_args();
		$teeth = array_filter($keys, 'is_string');
		foreach( $teeth as $key ) {
			if ( self::hasValue($key) == false ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * gets/returns the value of a specific key of the HttpGet
	 * @param mixed $key Usually a string, right ?
	 * @return mixed
	 */
	public static function get($key, $default = null)
	{
		return self::adapter()->get($key, $default);
	}

	/**
	 * gets/returns the value of a specific key as an integer
	 * @param mixed $key Usually a string, right ?
	 * @return mixed
	 */
	public static function getInt($key, $default = null)
	{
		$value = self::get($key, $default );
		return intval($value);
	}

	public static function debugInfo()
	{
		$adapter = HttpGet::adapter();
		return get_called_class() . " [" . get_short_class($adapter) . "] " . var_export($adapter->allValues(), true);
	}
}
