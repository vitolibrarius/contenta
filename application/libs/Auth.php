<?php

use \Model as Model;
use \Localized as Localized;
use \Config as Config;
use \Logger as Logger;

use \http\Session as Session;
use \http\Cookies as Cookies;
use \http\HttpGet as HttpGet;
use \http\HttpPost as HttpPost;

use \model\user\Users as Users;
use \model\user\UsersDBO as UsersDBO;

/**
 * Class Auth
 * Simply checks if user is logged in. In the app, several controllers use Auth::handleLogin() to
 * check if user if user is logged in, useful to show controllers/methods only to logged-in users.
 */
class Auth
{
	public static function handleLogin()
	{
		// if user is still not logged in, or the user has remember-me-cookie ? then try to login with cookie ("remember me" feature),
		// if not then destroy session, handle user as "not logged in" and redirect user to login page
		if ( Session::get('user_logged_in', false) == false ) {
			if ( Cookies::get('rememberme', false) == false || (Auth::loginWithCookie() == false)) {
				Session::destroy();
				header('location: ' . Config::Web('/login') );
				return false;
			}
		}

		return true;
	}

	public static function handleLoginWithAPI($userHash = '')
	{
		$login_successful = false;
		if ( empty($userHash) == false ) {
			$user = Model::Named('Users')->objectForApi_hash($userHash);
			if ( $user instanceof UsersDBO ) {
				Session::set('user_logged_in', true);
				Session::set('user_id', $user->id);
				Session::set('user_name', $user->name);
				Session::set('user_email', $user->email);
				Session::set('user_account_type', $user->account_type);

				$user->stampLogin();
				$login_successful = true;
			}
		}

		if ( $login_successful === false ) {
			Logger::logError("Error attempting API access '" . $userHash . "'");
			Session::destroy();
			http_response_code(401); // Unauthorized
		}

		return $login_successful;
	}

	public static function requireRole($role = null) {
		if (isset($role) == false  || $role == null) {
			return true;
		}

		if (Session::get('user_logged_in') == true
			&& (Session::get('user_account_type') === Users::AdministratorRole || Session::get('user_account_type') === $role )) {
			return true;
		}

		Logger::logError("User is not authorized", Session::get('user_name'), Session::get('user_id'));
		http_response_code(401); // Not Authorized
		throw new \Exception("Not authorized", 1);
	}

	public static function login()
	{
		$userLogin = HttpPost::getModelValue( 'users', null );
		if ( is_array($userLogin) == false ) {
			Session::addNegativeFeedback(Localized::Get("Auth", "USERNAME_FIELD_EMPTY"));
			return false;
		}

		// we do negative-first checks here
		if (!isset($userLogin['user_name']) OR empty($userLogin['user_name'])) {
			Session::addNegativeFeedback(Localized::Get("Auth", "USERNAME_FIELD_EMPTY"));
			return false;
		}
		if (!isset($userLogin['user_password']) OR empty($userLogin['user_password'])) {
			Session::addNegativeFeedback(Localized::Get("Auth", "PASSWORD_FIELD_EMPTY"));
			return false;
		}

		$user_model = Model::Named("Users");
		$user = $user_model->objectForName($userLogin['user_name']);
		if ( $user == false )
		{
			Session::addNegativeFeedback(Localized::Get("Auth", "LOGIN_FAILED"));
			return false;
		}

		// block login attempt if somebody has already failed 3 times and the last login attempt is less than 30sec ago
//  		echo PHP_EOL . "*** " . var_export(($user->last_failed_login > (time()-30)), true) . PHP_EOL;
		if (($user->failed_logins >= 3) AND ($user->last_failed_login >= (time()-30))) {
			Session::addNegativeFeedback(Localized::Get("Auth", "PASSWORD_WRONG_3_TIMES"));
			return false;
		}

		$VERIFIED_PASSWORD = false;
		if ( PHP_VERSION_ID > 50500 )
		{
			$VERIFIED_PASSWORD = password_verify($userLogin['user_password'], $user->password_hash);
		}
		else if (hash(HASH_DEFAULT_ALGO, $userLogin['user_password']) === $user->password_hash)
		{
			$VERIFIED_PASSWORD = true;
		}

		if ($VERIFIED_PASSWORD == true)
		{
			if ($user->active != 1) {
				Session::addNegativeFeedback(Localized::Get("Auth", "ACCOUNT_NOT_ACTIVE" ));
				return false;
			}

			// login process, write the user data into session
			Session::set('user_logged_in', true);
			Session::set('user_id', $user->id);
			Session::set('user_name', $user->name);
			Session::set('user_email', $user->email);
			Session::set('user_account_type', $user->account_type);

			// generate integer-timestamp for saving of last-login date, also clears the failed login stats
			$user->stampLogin();

			// if user has checked the "remember me" checkbox, then write cookie
			if (isset($userLogin['user_rememberme'])) {

				// generate 64 char random string and update database
				$random_token_string = $user->generateRememberme_token();

				// generate cookie string that consists of user id, random string and combined hash of both
				$cookie_string_first_part = $user->id . ':' . $random_token_string;
				$cookie_string_hash = hash('sha256', $cookie_string_first_part);
				$cookie_string = $cookie_string_first_part . ':' . $cookie_string_hash;

				// set cookie
				Cookies::set( 'rememberme', $cookie_string );
			}

			// return true to make clear the login was successful
			return true;

		} else {
			// increment the failed login counter for that user
			$user->increaseFailedLogin();

			// feedback message
			Session::addNegativeFeedback(Localized::Get("Auth", "PASSWORD_WRONG" ));
			return false;
		}

		// default return
		return false;
	}

	public static function loginWithCookie()
	{
		$user_model = Model::Named("Users");
		$login_successful = false;
		$cookie = Cookies::get('rememberme', false);
		if (empty($cookie) == false)
		{
			// check cookie's contents, check if cookie contents belong together
			list ($user_id, $token, $hash) = explode(':', $cookie);
			if ($hash === hash('sha256', $user_id . ':' . $token) AND (empty($token) == false))
			{
				$user = $user_model->userWithRemembermeToken($user_id, $token);
				if ( $user != false )
				{
					Session::set('user_logged_in', true);
					Session::set('user_id', $user->id);
					Session::set('user_name', $user->name);
					Session::set('user_email', $user->email);
					Session::set('user_account_type', $user->account_type);

					$user->stampLogin();

					$login_successful = true;
				}
			}
		}

		if ($login_successful == false)
		{
			Logger::logError("Cookie invalid", 'cookie', $cookie);

			// delete the invalid cookie to prevent infinite login loops
			Cookies::deleteCookie('rememberme');
		}
		return $login_successful;
	}

	public static function httpAuthenticate($auth_type = 'Basic', $auth_user, $auth_pw)
	{
		// we do negative-first checks here
		if (!isset($auth_user) OR empty($auth_user)) {
			return false;
		}
		if (!isset($auth_pw) OR empty($auth_pw)) {
			return false;
		}

		$user_model = Model::Named("Users");
		$user = $user_model->objectForName($auth_user);
		if ( $user == false ) {
			Logger::logError( "Authentication failed for $auth_type, $auth_user, $auth_pw" );
			return false;
		}

		// block login attempt if somebody has already failed 3 times and the last login attempt is less than 30sec ago
		if (($user->failed_logins() >= 3) AND ($user->last_failed_login() > (time()-30))) {
			return false;
		}

		if ($user->active() != 1) {
			return false;
		}

		$VERIFIED_PASSWORD = false;
		if ( PHP_VERSION_ID > 50500 )
		{
			$VERIFIED_PASSWORD = password_verify($auth_pw, $user->password_hash);
		}
		else if (hash(HASH_DEFAULT_ALGO, $auth_pw) === $user->password_hash)
		{
			$VERIFIED_PASSWORD = true;
		}

		if ($VERIFIED_PASSWORD == true)
		{
			// login process, write the user data into session
			Session::set('user_logged_in', true);
			Session::set('user_id', $user->id);
			Session::set('user_name', $user->name);
			Session::set('user_email', $user->email);
			Session::set('user_account_type', $user->account_type);

			// return true to make clear the login was successful
			return true;

		} else {
			// increment the failed login counter for that user
			$user->increaseFailedLogin();
			Logger::logError( "Authentication failed for $auth_type, $auth_user, $auth_pw" );
			return false;
		}

		// default return
		return false;
	}
}
