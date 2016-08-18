<?php

namespace http;

/**
 * HttpPost class
 *
 * handles the HttpPost stuff. creates HttpPost when no one exists, sets and
 * gets values, and closes the HttpPost properly (=logout). Those methods
 * are STATIC, which means you can call them with HttpPost::get(XXX);
 */
class HttpPost
{
    private static $adapter = null;

	/**
	 * starts the HttpPost
	 */
    public static function init(\interfaces\GlobalAdapter $adapter = null)
	{
		if ( is_null($adapter)) {
			self::$adapter = new \http\GlobalPOSTAdapter();
		}
		else {
			self::$adapter = $adapter;
		}
	}

    protected static function adapter()
	{
		if ( is_null(self::$adapter)) {
			self::$adapter = new \http\GlobalPOSTAdapter();
		}
		return self::$adapter;
	}

	/**
	 * sets a specific value to a specific key of the HttpPost
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
	 * gets/returns the value of a specific key of the HttpPost
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

	public static function getModelValue($modelName = null, $key = null)
	{
		$split = splitPOSTValues(self::adapter()->allValues());
		if ( is_array($split) && empty($modelName) == false ) {
			$split = (isset($split[$modelName]) ? $split[$modelName] : null);
			if ( is_array($split) && empty($key) == false ) {
				$split = (isset($split[$key]) ? $split[$key] : null);
			}
		}
		return $split;
	}

	public static function debugInfo()
	{
		$adapter = HttpPost::adapter();
		return get_called_class() . " [" . get_short_class($adapter) . "] " . var_export($adapter->allValues(), true);
	}
}
