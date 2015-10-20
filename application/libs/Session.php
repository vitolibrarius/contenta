<?php

/**
 * Session class
 *
 * handles the session stuff. creates session when no one exists, sets and
 * gets values, and closes the session properly (=logout). Those methods
 * are STATIC, which means you can call them with Session::get(XXX);
 */
class Session
{
	/**
	 * starts the session
	 */
	public static function init()
	{
		// if no session exist, start the session
		if (session_id() == '') {
			session_start();
		}
	}

	/**
	 * sets a specific value to a specific key of the session
	 * @param mixed $key
	 * @param mixed $value
	 */
	public static function set($key, $value)
	{
		$_SESSION[$key] = $value;
	}

	/**
	 * gets/returns the value of a specific key of the session
	 * @param mixed $key Usually a string, right ?
	 * @return mixed
	 */
	public static function get($key, $default = null)
	{
		if (isset($_SESSION[$key])) {
			return $_SESSION[$key];
		}
		return $default;
	}

	public static function clearAllFeedback()
	{
		Session::set('feedback_positive', null);
		Session::set('feedback_negative', null);
	}

	public static function negativeFeedback()
	{
		return Session::get('feedback_negative', null);
	}

	public static function addNegativeFeedback($message = null)
	{
		if ( is_string($message) ) {
			$_SESSION["feedback_negative"][] = $message;
			Logger::logError( $message, Session::get('user_name'), Session::get('user_id'));
		}
	}

	public static function addValidationFeedback($message = null)
	{
		if ( is_string($message) ) {
			$_SESSION["feedback_negative"][] = UTF8_STAR . ' ' . $message;
		}
	}

	public static function positiveFeedback()
	{
		return Session::get('feedback_positive', null);
	}

	public static function addPositiveFeedback($message = null)
	{
		if ( is_string($message) ) {
			Logger::logInfo( $message, Session::get('user_name'), Session::get('user_id'));
			$_SESSION["feedback_positive"][] = $message;
		}
		else {
			$_SESSION["feedback_negative"][] = var_export($message, true);
		}
	}

	/**
	 * deletes the session (= logs the user out)
	 */
	public static function destroy()
	{
		session_destroy();
	}

	public static function isUserLoggedIn()
	{
		return Session::get('user_logged_in');
	}

	public static function clearCurrentPageStack()
	{
		Session::set('pageStack', null);
	}

	public static function pushCurrentPage($pageURL)
	{
		$stack = Session::get('pageStack');
		if ( isset($stack) == false || is_array($stack) == false) {
			$stack = array();
		}
		$stack[] = $pageURL;
		Session::set('pageStack', $stack);
	}

	public static function popCurrentPage()
	{
		$stack = Session::get('pageStack');
		if ( isset($stack) != false && is_array($stack)) {
			$pageURL = array_pop( $stack );
			Session::set('pageStack', $stack);
			return $pageURL;
		}
		return null;
	}

	public static function peekCurrentPage()
	{
		$stack = Session::get('pageStack');
		if ( isset($stack) != false && is_array($stack)) {
			$pageURL = end( $stack );
			reset($stack);
			Session::set('pageStack', $stack);
			return $pageURL;
		}
		return null;
	}
}
