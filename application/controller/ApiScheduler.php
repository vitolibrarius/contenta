<?php

namespace controller;

use \Controller as Controller;
use \DataObject as DataObject;
use \Model as Model;
use \Auth as Auth;
use \http\Session as Session;;
use \Logger as Logger;
use \Localized as Localized;
use \model\user\Users as Users;

/**
 * Class Admin
 * The index controller
 */
class ApiScheduler extends Api
{
	function runschedule($userHash = null)
	{
		if ( Auth::handleLoginWithAPI($userHash) && Auth::requireRole(Users::AdministratorRole)) {
		}
	}
}

