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
			$media_model = Model::Named("Media");

			$this->view->model = Model::Named("Media");
			$this->view->recentMedia = $media_model->mostRecent();
			$this->view->render( '/index/index' );
		}
	}

	function ajax_recentMedia()
	{
		if (Auth::handleLogin()) {
			$media_model = Model::Named("Media");

			$this->view->recentMedia = $media_model->mostRecent();
			$this->view->render( '/index/rec' );
		}
	}
}
