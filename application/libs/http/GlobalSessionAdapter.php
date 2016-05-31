<?php

namespace http;

use \http\Session as Session;;

class GlobalSessionAdapter implements \interfaces\GlobalAdapter
{
	public function __construct()
	{
		throw new \Exception( "GlobalSessionAdapter constructed" );
		// if no session exist, start the session
		if (session_id() == '') {
			session_start();
		}
	}

	/**
	 * deletes the session (= logs the user out)
	 */
	public static function destroy()
	{
		session_destroy();
	}

	public function allValues()
	{
		return $_SESSION;
	}

    public function get($key, $default = null)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function delete($key)
    {
    	unset( $_SESSION[$key] );
    }

    public function getArray($key, array $default = null)
    {
    	return $this->get($key, $default);
    }

    public function addToArray($key, $value)
    {
		$_SESSION[$key][] = $value;
    }

    public function clearArray($key)
    {
    	unset( $_SESSION[$key] );
    }
}
