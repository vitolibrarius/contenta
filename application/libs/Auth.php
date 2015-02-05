<?php

use \Session as Session;
use \Model as Model;
use \Localized as Localized;
use \Config as Config;
use model\Users as Users;

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

		// if user is still not logged in, or the user has remember-me-cookie ? then try to login with cookie ("remember me" feature),
		// if not then destroy session, handle user as "not logged in" and redirect user to login page
		if ( isset($_SESSION['user_logged_in']) == false) {
			if (isset($_COOKIE['rememberme']) == false || (Auth::loginWithCookie() == false)) {
				Session::destroy();
				header('location: ' . Config::Web('/login') );
				return false;
			}
		}

		return true;
	}

	public static function requireRole($role = null) {
		if (isset($role) == false  || $role == null) {
			return true;
		}

		if (Session::get('user_logged_in') == true && Session::get('user_account_type') === $role ) {
			return true;
		}

		Logger::logError("User is not authorized", Session::get('user_name'), Session::get('user_id'));
		http_response_code(401); // Not Authorized
		throw new Exception("Not authorized", 1);
	}

	public static function login()
	{
		$values = splitPOSTValues($_POST);
		if ( isset($values, $values['users']) == false ) {
			Session::addNegativeFeedback(Localized::Get("Auth/USERNAME_FIELD_EMPTY", "Username field was empty."));
			return false;
		}

		$userLogin = $values['users'];

		// we do negative-first checks here
		if (!isset($userLogin['user_name']) OR empty($userLogin['user_name'])) {
			Session::addNegativeFeedback(Localized::Get("Auth/USERNAME_FIELD_EMPTY", "Username field was empty."));
			return false;
		}
		if (!isset($userLogin['user_password']) OR empty($userLogin['user_password'])) {
			Session::addNegativeFeedback(Localized::Get("Auth/PASSWORD_FIELD_EMPTY", "Password field was empty."));
			return false;
		}

		$user_model = Model::Named("Users");
		$user = $user_model->userByName($userLogin['user_name']);
		if ( $user == false )
		{
			Session::addNegativeFeedback(Localized::Get("Auth/LOGIN_FAILED", "Login failed."));
			return false;
		}

		// block login attempt if somebody has already failed 3 times and the last login attempt is less than 30sec ago
		if (($user->failed_logins >= 3) AND ($user->last_failed_login > (time()-30))) {
			Session::addNegativeFeedback(Localized::Get("Auth/PASSWORD_WRONG_3_TIMES",
				"You have typed in a wrong password 3 or more times already. Please wait 30 seconds to try again."));
			return false;
		}

		Session::addPositiveFeedback( var_export($user, true));

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
				Session::addNegativeFeedback(Localized::Get("Auth/ACCOUNT_NOT_ACTIVE", "Your account is not activated." ));
				return false;
			}

			// login process, write the user data into session
			Session::init();
			Session::set('user_logged_in', true);
			Session::set('user_id', $user->id);
			Session::set('user_name', $user->name);
			Session::set('user_email', $user->email);
			Session::set('user_account_type', $user->account_type);

			// reset the failed login counter for that user (if necessary)
			$user_model->clearFailedLogin($user);

			// generate integer-timestamp for saving of last-login date
			$user_model->stampLoginTimestamp($user);

			// if user has checked the "remember me" checkbox, then write cookie
			if (isset($userLogin['user_rememberme'])) {

				// generate 64 char random string and update database
				$random_token_string = $user_model->generateRememberMeToken($user);

				// generate cookie string that consists of user id, random string and combined hash of both
				$cookie_string_first_part = $user->id . ':' . $random_token_string;
				$cookie_string_hash = hash('sha256', $cookie_string_first_part);
				$cookie_string = $cookie_string_first_part . ':' . $cookie_string_hash;

				// set cookie
				$domain = "." . parse_url(Config::Url(), PHP_URL_HOST);
				setcookie('rememberme', $cookie_string, time() + COOKIE_RUNTIME, Config::Web('/'), $domain);
			}

			// return true to make clear the login was successful
			return true;

		} else {
			// increment the failed login counter for that user
			$user_model->increaseFailedLogin($user);

			// feedback message
			Session::addNegativeFeedback(Localized::Get("Auth/PASSWORD_WRONG", "Unable to authenticate." ));
			return false;
		}

		// default return
		return false;
	}

	public static function loginWithCookie()
	{
		$user_model = Model::Named("Users");
		$login_successful = false;
		$cookie = isset($_COOKIE['rememberme']) ? $_COOKIE['rememberme'] : '';
		if ($cookie)
		{
			// check cookie's contents, check if cookie contents belong together
			list ($user_id, $token, $hash) = explode(':', $cookie);
			if ($hash === hash('sha256', $user_id . ':' . $token) AND (empty($token) == false))
			{
				$user = $user_model->userByToken($user_id, $token);
				if ( $user != false )
				{
					Session::init();
					Session::set('user_logged_in', true);
					Session::set('user_id', $user->id);
					Session::set('user_name', $user->name);
					Session::set('user_email', $user->email);
					Session::set('user_account_type', $user->account_type);

					$user_model->stampLoginTimestamp($user);
					Logger::logInfo("User logged In (cookie)", Session::get('user_name'), Session::get('user_id'));

					$login_successful = true;
				}
			}
		}

		if ($login_successful == false)
		{
			Logger::logError("Cookie invalid", 'cookie', $cookie);

			// delete the invalid cookie to prevent infinite login loops
			Auth::deleteCookie();
		}
		return $login_successful;
	}

	public static function deleteCookie()
	{
		$domain = "." . parse_url(Config::Url(), PHP_URL_HOST);
		// set the rememberme-cookie to ten years ago (3600sec * 365 days * 10).
		// that's obviously the best practice to kill a cookie via php
		// @see http://stackoverflow.com/a/686166/1114320
		setcookie('rememberme', false, time() - (3600 * 3650), Config::Web('/'), $domain);
	}
}
