<?php

namespace controller;

use \Controller as Controller;
use \Session as Session;
use \Auth as Auth;
use \Config as Config;
use \Model as Model;
use \Localized as Localized;


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
			$this->view->model = Model::Named("Users");
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
			$this->view->model = Model::Named("Users");
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
		header('location: ' . Config::Web('/index/index'));
	}

	/**
	 * Login with cookie
	 */
	function loginWithCookie()
	{
		if ( Auth::loginWithCookie() == true ) {
			if ( Session::get('user_account_type') == 'admin' ) {
				header('location: ' . Config::Web('/admin/index'));
			}
			else {
				header('location: ' . Config::Web('/index'));
			}
		}
		else {
			Session::addNegativeFeedback(Localized::Get("Auth/COOKIE_INVALID", "Your remember-me-cookie is invalid." ));

			// if NO, then move user to login/index (login form) (this is a browser-redirection, not a rendered view)
			header('location: ' . Config::Web('/login'));
		}
	}
}
