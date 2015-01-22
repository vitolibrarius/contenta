<?php

namespace controller;

use \Controller as Controller;
use \DataObject as DataObject;
use \Model as Model;
use \Session as Session;
use model\Users as Users;
use processor\Migration as Migration;


class Upgrade extends Controller
{
	function showUpgradeOptions()
	{
		$user_model = Model::Named("Users");
		$adminUsers = $user_model->allUsers(Users::AdministratorRole);
		$latest = Model::Named("Version")->latestVersion();

		if ( is_array($adminUsers) == false || count($adminUsers) == 0) {
			// no users, show the options
			$this->view->latest = $latest;
			$this->view->render('/upgrade/index');
		}
		else if (Session::get('user_logged_in') == false) {
			// not logged in, so show login panel
			$this->view->loginActionPath = "/Upgrade/login";
			$this->view->render('/login/index');
		}
		else if ( Session::get('user_account_type') == Users::AdministratorRole ) {
			Session::addNegativeFeedback("Not authorized to perform upgrade actions");
			$this->view->render('/error/index');
		}
		else {
			$this->view->render('/upgrade/index');
		}
	}

	function migrate()
	{
		$user_model = Model::Named("Users");
		$adminUsers = $user_model->allUsers(Users::AdministratorRole);
		$latest = Model::Named("Version")->latestVersion();

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
			$this->view->render('/login/index');
		}
		else if ( Session::get('user_account_type') == Users::AdministratorRole ) {
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
}
