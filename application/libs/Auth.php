<?php

use \Session as Session;

/**
 * Class Auth
 * Simply checks if user is logged in. In the app, several controllers use Auth::handleLogin() to
 * check if user if user is logged in, useful to show controllers/methods only to logged-in users.
 */
class Auth
{
	public static function handleLogin()
	{
		// initialize the session
		Session::init();

		// if user is still not logged in, then destroy session, handle user as "not logged in" and
		// redirect user to login page
		if (!isset($_SESSION['user_logged_in'])) {
			Session::destroy();
			header('location: ' . WEB_DIR . '/login');
			return false;
		}

		return true;
	}

	public static function requireRole($role = null) {
		if (isset($role) == false) {
			return true;
		}

		if (Session::get('user_logged_in') == true && Session::get('user_account_type') === $role ) {
			return true;
		}

		Logger::logError("User is not authorized", Session::get('user_name'), Session::get('user_id'));
		http_response_code(401); // Not Authorized
		throw new Exception("Not authorized", 1);
	}
}
