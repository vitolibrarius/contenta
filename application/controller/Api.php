<?php

namespace controller;

use \Controller as Controller;
use \DataObject as DataObject;
use \Model as Model;
use \Auth as Auth;
use \Session as Session;
use \Logger as Logger;
use \Localized as Localized;

use \SQL as SQL;
use db\Qualifier as Qualifier;

use model\Users as Users;
use model\Publisher as Publisher;

/**
 * Class Admin
 * The index controller
 */
class Api extends Controller
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

	function publishers( $userHash = null)
	{
		if (Auth::handleLogin() || Auth::handleLoginWithAPI($userHash)) {
			$qualifiers = array();
			if ( isset($_GET['q']) && strlen($_GET['q']) > 0) {
				$qualifiers[] = Qualifier::LikeQualifier( Publisher::name, '%' . $_GET['q'] . '%' );
			}

			$select = SQL::Select( Model::Named("Publisher"), array( Publisher::id, Publisher::name ));
			if ( count($qualifiers) > 0 ) {
				$select->where( Qualifier::AndQualifier( $qualifiers ));
			}
			$select->orderBy( array(Publisher::name) );
			$publishers = $select->limit(-1)->fetchAll();

			$this->view->renderJson( $publishers );
		}
	}

	function cron_process( $userHash = null )
	{
		if ( Auth::handleLoginWithAPI($userHash) && Auth::requireRole(Users::AdministratorRole)) {
			$job_model = Model::Named("Job");
			$jobs_to_run = $job_model->jobsToRun();

			$json = array();
			foreach( $jobs_to_run as $aJob ) {
				$pid = DaemonizeJob( $aJob );
				$json[] = array(
					"pid" => $pid,
					"job" => $aJob->displayDescription()
				);
			}

			$this->view->renderJson( $json );
		}
	}
}
