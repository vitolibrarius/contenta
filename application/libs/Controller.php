<?php

use \http\Session as Session;;
use \View as View;
use \Config as Config;

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
		// create a view object (that does nothing, but provides the view render() method)
		$this->view = new View(get_short_class($this));
	}

	public static function Named($name)
	{
		$className = "controller\\" . $name;
		return new $className();
	}
}
