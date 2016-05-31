<?php

namespace controller;

use \Controller as Controller;
use \DataObject as DataObject;
use \Model as Model;
use \model\user\Users as Users;

/**
 * Class Error
 * The index controller
 */
class Error extends Controller
{
	/**
	 * Handles what happens when user moves to URL/index/index, which is the same like URL/index or in this
	 * case even URL (without any controller/action) as this is the default controller-action when user gives no input.
	 */
	function index()
	{
		$this->view->render( '/error/index' );
	}
}
