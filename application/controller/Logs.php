<?php

namespace controller;

use \Controller as Controller;
use \DataObject as DataObject;
use \Model as Model;
use \Session as Session;
use \Auth as Auth;
use model\Users as Users;
use model\Log as Log;
use model\LogLevel as LogLevel;

/**
 * Class Error
 * The index controller
 */
class Logs extends Controller
{
	/**
	 * Handles what happens when user moves to URL/index/index, which is the same like URL/index or in this
	 * case even URL (without any controller/action) as this is the default controller-action when user gives no input.
	 */
	function index()
	{
		if (Auth::handleLogin()) {
			if (Session::get('user_logged_in') != true || Session::get('user_account_type') != 'admin' ) {
				Logger::logError("User is not authorized", Session::get('user_name'), Session::get('user_id'));
				http_response_code(401); // Not Authorized
				throw new Exception("Not authorized", 1);
			}

			$this->view->render( '/logs/index');
		}
	}

	function log_table()
	{
		if (Auth::handleLogin()) {
			if (Session::get('user_logged_in') != true || Session::get('user_account_type') != 'admin' ) {
				Logger::logError("User is not authorized", Session::get('user_name'), Session::get('user_id'));
				http_response_code(401); // Not Authorized
				throw new Exception("Not authorized", 1);
			}
			$log_model = Model::Named("Log");
			$this->view->logArray = $log_model->mostRecentLike(
				isset($_GET['trace']) ? $_GET['trace'] : null,
				isset($_GET['trace_id']) ? $_GET['trace_id'] : null,
				isset($_GET['context']) ? $_GET['context'] : null,
				isset($_GET['context_id']) ? $_GET['context_id'] : null,
				isset($_GET['level']) ? $_GET['level'] : null,
				isset($_GET['message']) ? $_GET['message'] : null
			);
			$this->view->render( '/logs/log_table', true);
		}
	}

	function log_inline()
	{
		if (Auth::handleLogin()) {
			$log_model = Model::Named("Log");
			$this->view->logArray = $log_model->mostRecentLike(
				isset($_GET['trace']) ? $_GET['trace'] : null,
				isset($_GET['trace_id']) ? $_GET['trace_id'] : null,
				isset($_GET['context']) ? $_GET['context'] : null,
				isset($_GET['context_id']) ? $_GET['context_id'] : null,
				isset($_GET['level']) ? $_GET['level'] : null,
				isset($_GET['message']) ? $_GET['message'] : null,
				"desc",
				isset($_GET['limit']) ? $_GET['limit'] : null
			);
			$this->view->render( '/logs/log_inline', true);
		}
	}
}
