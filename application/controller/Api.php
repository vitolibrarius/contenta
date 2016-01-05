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
use model\Character as Character;
use model\Story_Arc as Story_Arc;

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
				$qualifiers[] = Qualifier::Like( Publisher::name, $_GET['q'] );
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

	function characters( $userHash = null)
	{
		if (Auth::handleLogin() || Auth::handleLoginWithAPI($userHash)) {
			$qualifiers = array();
			if ( isset($_GET['q']) && strlen($_GET['q']) > 0) {
				$qualifiers[] = Qualifier::Like( Character::name, $_GET['q']);
			}

			$select = SQL::Select( Model::Named("Character"), array( Character::id, Character::name ));
			if ( count($qualifiers) > 0 ) {
				$select->where( Qualifier::AndQualifier( $qualifiers ));
			}
			$select->orderBy( array(Character::name) );
			$characters = $select->limit(-1)->fetchAll();

			$this->view->renderJson( $characters );
		}
	}

	function story_arcs( $userHash = null)
	{
		if (Auth::handleLogin() || Auth::handleLoginWithAPI($userHash)) {
			$qualifiers = array();
			if ( isset($_GET['q']) && strlen($_GET['q']) > 0) {
				$qualifiers[] = Qualifier::Like( Story_Arc::name, $_GET['q']);
			}
			if ( isset($_GET['r']) && ($_GET['r'] == 'wanted')) {
				$qualifiers[] = Qualifier::Equals( Story_Arc::pub_wanted, Model::TERTIARY_TRUE);
			}

			$select = SQL::Select( Model::Named("Story_Arc"), array( Story_Arc::id, Story_Arc::name ));
			if ( count($qualifiers) > 0 ) {
				$select->where( Qualifier::AndQualifier( $qualifiers ));
			}
			$select->orderBy( array(Story_Arc::name) );
			$characters = $select->limit(-1)->fetchAll();

			$this->view->renderJson( $characters );
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

	function mediaPayload( $id, $userHash = null )
	{
		if (Auth::handleLogin() || Auth::handleLoginWithAPI($userHash)) {
			$media_model = Model::Named('Media');
			$user_model = Model::Named('Users');

			$user = $user_model->objectForId(Session::get('user_id'));
			$mediaObj = $media_model->objectForId($id);
			if ( $mediaObj != false )
			{
				$path = $mediaObj->contentaPath();
				$etag = $mediaObj->checksum;

				if ( file_exists($path) == true )
				{
// 					$user->flagMediaAsRead($mediaObj);
					if ( !empty($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag) {
						header('HTTP/1.1 304 Not Modified');
						header('Content-Length: 0');
						exit;
					}

					$expiry = 604800; // (60*60*24*7)
					header('ETag: ' . $etag);
					header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()) .' GMT');
					header('Expires:'. gmdate('D, d M Y H:i:s', time() + $expiry) .' GMT');
					header('Content-Description: File Transfer');
					header('Content-Type: application/x-cbz');
					header('Content-Disposition: attachment; filename=' . basename($path));
					header('Pragma: public');
					header('Content-Length: ' . filesize($path));
					ob_clean();
					flush();
					readfile($path);
				}
				else
				{
					header('location: error/index');
				}
			}
			else
			{
				header('location: error/index');
			}
		}
	}

	function notifications( $userHash = null )
	{
		if (Auth::handleLogin() || Auth::handleLoginWithAPI($userHash)) {
			$positive = Session::positiveFeedback();
			$negative = Session::negativeFeedback();
			Session::clearAllFeedback();

			$lastCheck = Session::get('user_notification');
			if ( is_null($lastCheck) || time() - $lastCheck > 100) {
				Logger::logError( "Last check is $lastCheck" );
			}

			$logs = Model::Named("Log")->messagesSince( session_id(), $lastCheck );
			Session::set('user_notification', time());

			$this->view->renderJson( array(
				"session_id" => session_id(),
				"positive" => (is_null($positive) ? array() : $positive),
				"negative" => (is_null($negative) ? array() : $negative),
				"logs" => (is_array($logs) ? $logs : array())
				)
			);
		}
	}

}
