<?php

namespace controller;

use \Controller as Controller;
use \DataObject as DataObject;
use \Model as Model;
use \Auth as Auth;
use \Session as Session;
use \Logger as Logger;
use \Localized as Localized;
use controller\Admin as Admin;
use model\Users as Users;
use model\Publisher as Publisher;

/**
 * Class Admin
 * The index controller
 */
class AdminPublishers extends Admin
{
	function publisherlist()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Publisher');
			$this->view->model = $model;
			$this->view->list = $model->allObjects();
			$this->view->render( '/publishers/index');
		}
	}
}
