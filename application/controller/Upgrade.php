<?php

namespace controller;

use \Controller as Controller;
use \DataObject as DataObject;
use \Model as Model;
use \http\Session as Session;;
use \Logger as Logger;
use \Auth as Auth;

use \utilities\ShellCommand as ShellCommand;
use \utilities\Git as Git;

use \model\user\Users as Users;
use processor\Migration as Migration;


class Upgrade extends Controller
{
	function login()
	{
		if ( Auth::login() == true ) {
			if ( Session::get('user_account_type') == 'admin' ) {
				$this->view->render('/upgrade/index');
			}
			else {
				Session::addNegativeFeedback("Not authorized to perform upgrade actions");
				$this->view->render('/error/index');
			}
		}
		else {
			$this->view->model = Model::Named("Users");
			$this->view->render('/login/index');
		}
	}

	function index()
	{
		$user_model = Model::Named("Users");
		$adminUsers = $user_model->allUsers(Users::AdministratorRole);
		$latest = Model::Named("Version")->latestVersion();

		if ( is_array($adminUsers) == false || count($adminUsers) == 0) {
			// no users, show the options
			$this->view->latestVersion = $latest;
			$this->view->render('/upgrade/index');
		}
		else if (Session::get('user_logged_in') == false) {
			// not logged in, so show login panel
			$this->view->loginActionPath = "/Upgrade/login";
			$this->view->model = Model::Named("Users");
			$this->view->render('/login/index');
		}
		else if ( Session::get('user_account_type') != Users::AdministratorRole ) {
			Session::addNegativeFeedback("Not authorized to perform upgrade actions");
			$this->view->render('/error/index');
		}
		else {
			$this->view->latestVersion = $latest;
			$this->view->render('/upgrade/index');
		}
	}

	function migrate()
	{
		$user_model = Model::Named("Users");
		$adminUsers = $user_model->allUsers(Users::AdministratorRole);

		if ( is_array($adminUsers) == false || count($adminUsers) == 0) {
			// no users, just do it
			$processor = new Migration(currentVersionNumber());
			$processor->processData();

			$this->view->logs = $processor->migrationLogs();
			$this->view->render('/upgrade/completed');
		}
		else if (Session::get('user_logged_in') == false) {
			// not logged in, so show login panel
			$this->view->loginActionPath = "/Upgrade/login";
			$this->view->model = Model::Named("Users");
			$this->view->render('/login/index');
		}
		else if ( Session::get('user_account_type') != Users::AdministratorRole ) {
			Session::addNegativeFeedback("Not authorized to perform upgrade actions");
			$this->view->render('/error/index');
		}
		else {
			$processor = new Migration(currentVersionNumber());
			$processor->processData();

			$this->view->logs = $processor->migrationLogs();
			$this->view->render('/upgrade/completed');
		}
	}

	function upgradeEligibility()
	{
		$user_model = Model::Named("Users");
		$adminUsers = $user_model->allUsers(Users::AdministratorRole);

		if ( is_array($adminUsers) == false || count($adminUsers) == 0) {
			$this->index();
		}
		else if (Session::get('user_logged_in') == false) {
			// not logged in, so show login panel
			$this->view->loginActionPath = "/Upgrade/login";
			$this->view->model = Model::Named("Users");
			$this->view->render('/login/index');
		}
		else if ( Session::get('user_account_type') != Users::AdministratorRole ) {
			Session::addNegativeFeedback("Not authorized to perform upgrade actions");
			$this->view->render('/error/index');
		}
		else {
			$this->view->model = Model::Named("Patch");
			$this->view->patches = Model::Named("Patch")->allObjects();
			$this->view->render('/upgrade/eligibility');
		}
	}

	function gitPull()
	{
		$user_model = Model::Named("Users");
		$adminUsers = $user_model->allUsers(Users::AdministratorRole);

		if ( is_array($adminUsers) == false || count($adminUsers) == 0) {
			$this->index();
		}
		else if (Session::get('user_logged_in') == false) {
			// not logged in, so show login panel
			$this->view->loginActionPath = "/Upgrade/login";
			$this->view->model = Model::Named("Users");
			$this->view->render('/login/index');
		}
		else if ( Session::get('user_account_type') != Users::AdministratorRole ) {
			Session::addNegativeFeedback("Not authorized to perform upgrade actions");
			$this->view->render('/error/index');
		}
		else {
			$git = new \utilities\Git(SYSTEM_PATH);
			$this->view->git_results = $git->pull();
			if ( isset($this->view->git_results, $this->view->git_results['status']) ) {
				if ( $this->view->git_results['status'] == 0 ) {
					Logger::logWarning("Software updated. " . $this->view->git_results['stdout']);
				}
				else {
					Logger::logError("Software update failed. " . $this->view->git_results['stdout'] . PHP_EOL . $this->view->git_results['stderr']);
				}
				$this->view->render('/upgrade/git_results');
			}
			else {
				Session::addNegativeFeedback("Unexpected error");
				Logger::logError("An error occured. ");
				$this->view->render('/error/index');
			}
		}
	}
}
