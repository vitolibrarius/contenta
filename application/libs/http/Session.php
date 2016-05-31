<?php

namespace http;

/**
 * Session class
 *
 * handles the session stuff. creates session when no one exists, sets and
 * gets values, and closes the session properly (=logout). Those methods
 * are STATIC, which means you can call them with Session::get(XXX);
 */
class Session
{
    private static $adapter = null;

	/**
	 * starts the session
	 */
    public static function init(\interfaces\GlobalAdapter $adapter = null)
	{
		if ( is_null($adapter)) {
			self::$adapter = new \http\GlobalSessionAdapter();
		}
		else {
			self::$adapter = $adapter;
		}
	}

    protected static function adapter()
	{
		if ( is_null(self::$adapter)) {
			self::$adapter = new \http\GlobalSessionAdapter();
		}
		return self::$adapter;
	}

	public static function destroy()
	{
		self::$adapter = null;
	}

	/**
	 * sets a specific value to a specific key of the session
	 * @param mixed $key
	 * @param mixed $value
	 */
	public static function set($key, $value)
	{
		self::adapter()->set($key, $value);
	}

	/**
	 * gets/returns the value of a specific key of the session
	 * @param mixed $key Usually a string, right ?
	 * @return mixed
	 */
	public static function get($key, $default = null)
	{
		return self::adapter()->get($key, $default);
	}

	public static function clearAllFeedback()
	{
		self::adapter()->clearArray('feedback_positive');
		self::adapter()->clearArray('feedback_negative');
	}

	public static function negativeFeedback()
	{
		return self::adapter()->getArray('feedback_negative', null);
	}

	public static function addNegativeFeedback($message = null)
	{
		if ( is_string($message) ) {
			self::adapter()->addToArray("feedback_negative", $message);
			\Logger::logWarning( $message, Session::get('user_name'), Session::get('user_id'));
		}
	}

	public static function addValidationFeedback($message = null)
	{
		if ( is_string($message) ) {
			self::adapter()->addToArray("feedback_negative", UTF8_STAR . ' ' . $message);
		}
	}

	public static function positiveFeedback()
	{
		return self::adapter()->getArray('feedback_positive', null);
	}

	public static function addPositiveFeedback($message = null)
	{
		if ( is_string($message) ) {
			self::adapter()->addToArray("feedback_positive", $message);
		}
	}

	public static function isUserLoggedIn()
	{
		return Session::get('user_logged_in', false);
	}

	public static function debugInfo()
	{
		$adapter = Session::adapter();
		return get_called_class() . " [" . get_short_class($adapter) . "] " . var_export($adapter->allValues(), true);
	}
}
