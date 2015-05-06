<?php

namespace controller;

use \Controller as Controller;
use \DataObject as DataObject;
use \Model as Model;
use \Auth as Auth;
use \Session as Session;
use \Logger as Logger;
use \Localized as Localized;
use model\Users as Users;
use model\Publisher as Publisher;

/**
 * Class Admin
 * The index controller
 */
class Admin extends Controller
{
	/**
	 * Handles what happens when user moves to URL/index/index, which is the same like URL/index or in this
	 * case even URL (without any controller/action) as this is the default controller-action when user gives no input.
	 */
	function index($userHash = null)
	{
		if ( Auth::handleLoginWithAPI($userHash) && Auth::requireRole(Users::AdministratorRole)) {
			$this->view->render( '/admin/index' );
		}
	}
}
