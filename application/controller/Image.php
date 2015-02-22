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
class Image extends Controller
{
	/**
	 * Handles what happens when user moves to URL/index/index, which is the same like URL/index or in this
	 * case even URL (without any controller/action) as this is the default controller-action when user gives no input.
	 */
	function index()
	{
		if (Auth::handleLogin()) {
			$this->view->render( '/error/index' );
		}
	}

	function icon($table = null, $id = null)
	{
		if (isset($table, $id)) {
			$model = Model::Named($table);
			$obj = $model->objectForId($id);
			$file = 'public/img/default_icon_' . $table . '.png';
			if ( $obj != false )
			{
				$file = $obj->smallIconPath( $file );
			}

			header('Content-Type: image/' . file_ext($file));
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			readfile($file);
		}
	}

	function thumbnail($table = null, $id = null)
	{
		if (isset($table, $id)) {
			$model = Model::Named($table);
			$obj = $model->objectForId($id);
			$file = 'public/img/default_thumbnail_' . $table . '.png';
			if ( $obj != false )
			{
				$file = $obj->largeIconPath( $file );
			}

			header('Content-Type: image/' . file_ext($file));
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			readfile($file);
		}
	}
}
