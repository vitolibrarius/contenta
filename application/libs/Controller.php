<?php

use \Session as Session;
use \View as View;
use \Config as Config;
use \ReflectionClass as ReflectionClass;

/**
 * This is the "base controller class". All other "real" controllers extend this class.
 * Whenever a controller is created, we also
 * 1. initialize a session
 * 2. check if the user is not logged in anymore (session timeout) but has a cookie
 * 3. create a database connection (that will be passed to all models that need a database connection)
 * 4. create a view object
 */
class Controller
{
	function __construct()
	{
		Session::init();

		// user has remember-me-cookie ? then try to login with cookie ("remember me" feature)
		if (!isset($_SESSION['user_logged_in']) && isset($_COOKIE['rememberme'])) {
			header('location: ' . Config::Web('/login/loginWithCookie') );
		}

		// create a view object (that does nothing, but provides the view render() method)
		$reflect = new ReflectionClass($this);
		$this->view = new View($reflect->getShortName());
	}

	public static function Named($name)
	{
		$className = "controller\\" . $name;
		return new $className();
	}

}
