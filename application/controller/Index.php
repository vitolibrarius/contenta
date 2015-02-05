<?php

namespace controller;

use \Controller as Controller;
use \DataObject as DataObject;
use \Model as Model;
use \Session as Session;
use \Auth as Auth;
use model\Users as Users;

/**
 * Class Index
 * The index controller
 */
class Index extends Controller
{
	/**
	 * Handles what happens when user moves to URL/index/index, which is the same like URL/index or in this
	 * case even URL (without any controller/action) as this is the default controller-action when user gives no input.
	 */
	function index()
	{
		if (Auth::handleLogin()) {
			$this->view->render( '/index/index' );
		}
	}
}
