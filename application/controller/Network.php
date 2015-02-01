<?php

namespace controller;

use \Controller as Controller;
use \DataObject as DataObject;
use \Model as Model;
use \Session as Session;
use \Auth as Auth;
use model\Users as Users;
use model\Endpoint as Endpoint;

/**
 * Class Endpoint
 */
class Network extends Controller
{
	function index()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Endpoint');
			$this->view->viewTitle = "Network Configuration";
			$this->view->endpoints = $model->allObjects();
			$this->view->render( '/network/index' );
		}
	}
}
