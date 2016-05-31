<?php

namespace http;

/**
 * Cookies class
 *
 * handles the Cookies stuff. creates Cookies when no one exists, sets and
 * gets values, and closes the Cookies properly (=logout). Those methods
 * are STATIC, which means you can call them with Cookies::get(XXX);
 */
class Cookies
{
    private static $adapter = null;

	/**
	 * starts the Cookies
	 */
    public static function init(\interfaces\GlobalAdapter $adapter = null)
	{
		if ( is_null($adapter)) {
			self::$adapter = new \http\GlobalCookieAdapter();
		}
		else {
			self::$adapter = $adapter;
		}
	}

    protected static function adapter()
	{
		if ( is_null(self::$adapter)) {
			self::$adapter = new \http\GlobalCookieAdapter();
		}
		return self::$adapter;
	}

	/**
	 * sets a specific value to a specific key of the Cookies
	 * @param mixed $key
	 * @param mixed $value
	 */
	public static function set($key, $value)
	{
		self::adapter()->set($key, $value);
	}

	/**
	 * gets/returns the value of a specific key of the Cookies
	 * @param mixed $key Usually a string, right ?
	 * @return mixed
	 */
	public static function get($key, $default = null)
	{
		return self::adapter()->get($key, $default);
	}

	public static function deleteCookie($key)
	{
		return self::adapter()->delete($key);
	}

	public static function debugInfo()
	{
		$adapter = Session::adapter();
		return get_called_class() . " [" . get_short_class($adapter) . "] " . var_export($adapter->allValues(), true);
	}
}
