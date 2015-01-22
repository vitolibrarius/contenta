<?php

namespace controller;

use \Controller as Controller;
use \Session as Session;
use \Auth as Auth;


class Login extends Controller
{
	/**
	 * Index, default action (shows the login form), when you do login/index
	 */
	function index()
	{
		if (Session::get('user_logged_in') == true ) {
			if ( Session::get('user_account_type') == 'admin' ) {
				header('location: ' . Config::Web('/admin/index'));
			}
			else {
				header('location: ' . Config::Web('/index'));
			}
		}
		else {
			$this->view->render('/login/index');
		}
	}

	/**
	 * The login action, when you submit login/login
	 */
	function login()
	{
		if ( Auth::login() == true ) {
			if ( Session::get('user_account_type') == 'admin' ) {
				header('location: ' . Config::Web('/admin/index'));
			}
			else {
				header('location: ' . Config::Web('/index'));
			}
		}
		else {
			$this->view->render('/login/index');
		}
	}

	/**
	 * The logout action, login/logout
	 */
	function logout()
	{
		Logger::logInfo("User logged out", Session::get('user_name'), Session::get('user_id'));

		Auth::deleteCookie();

		// delete the session
		Session::destroy();

		// redirect user to base URL
		header('location: ' . WEB_DIR . '/index/index');
	}

	/**
	 * Login with cookie
	 */
	function loginWithCookie()
	{
		$user_model = loadModel('User');
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

					$_SESSION["feedback_positive"][] = FEEDBACK_COOKIE_LOGIN_SUCCESSFUL;
					$user_model->stampLoginTimestamp($user);
					$login_successful = true;
				}
			}
		}

		if ($login_successful)
		{
			Logger::logInfo("User logged In", Session::get('user_name'), Session::get('user_id'));
			header('location: ' . WEB_DIR . '/index/index');
		}
		else
		{
			Logger::logError("Cookie invalid", 'cookie', $cookie);

			$_SESSION["feedback_negative"][] = FEEDBACK_COOKIE_INVALID;

			// delete the invalid cookie to prevent infinite login loops
			$this->deleteCookie();

			// if NO, then move user to login/index (login form) (this is a browser-redirection, not a rendered view)
			header('location: ' . WEB_DIR . '/login/index');
		}
	}
}
