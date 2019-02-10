<?php

namespace controller;

use \Controller as Controller;
use \DataObject as DataObject;
use \Model as Model;
use \Auth as Auth;
use \Logger as Logger;
use \Localized as Localized;
use \Config as Config;

use \http\Session as Session;
use \http\HttpGet as HttpGet;
use \http\HttpPost as HttpPost;
use \http\PageParams as PageParams;

use \SQL as SQL;
use db\Qualifier as Qualifier;

use \model\user\Users as Users;
use \model\media\Book as Book;
use \model\media\Publisher as Publisher;
use \model\media\Character as Character;
use \model\media\Story_Arc as Story_Arc;
use \model\pull_list\Pull_List as Pull_List;

/**
 * Class API
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

	public function __call($modelName, $args)
	{
		Logger::logInfo(  "__call: " . $modelName . var_export($args, true) );
// 		if (Auth::handleLogin()) {
			$model = Model::Named($modelName);
			switch ( $_SERVER['REQUEST_METHOD'] ) {
				case 'DELETE': // delete
					break;
				case 'POST': // insert
					break;
				case 'PUT': // update
					break;

				case 'GET': // select
				default:
					$oid = (isset($args[0]) ? $args[0] : 0);
					$related = (isset($args[1]) ? $args[1] : null);
					$parameters = Session::pageParameters( $this, $modelName );
					list( $hasNewValues, $query) = $parameters->updateParametersFromGET();
					$pageNum = $parameters->valueForKey( "pageNum", 0 );

					if ( $oid > 0 ) {
						$object = $model->objectForId($oid);
						if ( $object != false && is_null($related) == false) {
							$data = array(
								"querySize" => $parameters->querySize(),
								"pageShown" => $parameters->pageShown(),
								"pageCount" => $parameters->pageCount(),
								"pageSize" => $parameters->pageSize()
							);
						 	$related = $object->$related();
							if ( is_array($related) ) {
								$data["items"] = $related;
							}
							else {
								$data["item"] = $related;
							}

							$this->view->renderJson( $data );
						}
						else {
							$data = array(
								"item" => $object
							);

							$this->view->renderJson( $data );
						}
					}
					else {
						$results = $model->searchQuery( $hasNewValues, $query, $pageNum, $parameters );
						$data = array(
							"querySize" => $parameters->querySize(),
							"pageShown" => $parameters->pageShown(),
							"pageCount" => $parameters->pageCount(),
							"pageSize" => $parameters->pageSize(),
							"items" => $results
						);

						$this->view->renderJson( $data );
					}
					break;
			}
// 		}
    }

	function publishers( $userHash = null)
	{
		if (Auth::handleLogin() || Auth::handleLoginWithAPI($userHash)) {
			$qualifiers = array();
			$pub_id = HttpGet::getInt("id", 0);
			if ( $pub_id > 0 ) {
				$qualifiers[] = Qualifier::Equals( Publisher::id, $pub_id );
			}
			else {
				$pub_name = HttpGet::get("q", false);
				if ( $pub_name != false) {
					$qualifiers[] = Qualifier::Like( Publisher::name, $pub_name );
				}
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
				$pub = $mediaObj->publication();
				$item = Model::Named("Reading_Item")->createReadingItemPublication($user, $pub);
				$path = $mediaObj->contentaPath();
				$etag = $mediaObj->checksum;

				if ( file_exists($path) == true )
				{
					$item->setRead_date(time());
					$item->saveChanges();

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

					if ( ob_get_contents() != false ) {
						ob_clean();
						flush();
					}
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

	function bookPayload( $id, $userHash = null )
	{
		Logger::logError(  "bookPayload: " . $id );
		if (Auth::handleLogin() || Auth::handleLoginWithAPI($userHash)) {
			$book_model = Model::Named('Book');
			$bookObj = $book_model->objectForId($id);
			if ( $bookObj != false )
			{
				$path = $bookObj->contentaPath();
				$etag = $bookObj->checksum;

				if ( file_exists($path) == true )
				{
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
					header('Content-Type: application/epub+zip');
					header('Content-Disposition: attachment; filename=' . basename($path));
					header('Pragma: public');
					header('Content-Length: ' . filesize($path));

					if ( ob_get_contents() != false ) {
						ob_clean();
						flush();
					}
					readfile($path);
				}
				else
				{
					Logger::logError(  "No file: " . $id . " - " . $path );
					header('location: error/index');
				}
			}
			else
			{
				Logger::logError(  "No book: " . $id );
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

	function pull_lists( $userHash = null)
	{
		if (Auth::handleLogin() || Auth::handleLoginWithAPI($userHash)) {
			$qualifiers = array();
			if ( isset($_GET['q']) && strlen($_GET['q']) > 0) {
				$qualifiers[] = Qualifier::Like( Pull_List::name, $_GET['q'] );
			}

			$select = SQL::Select( Model::Named("Pull_List"), array( Pull_List::id, Pull_List::name ));
			if ( count($qualifiers) > 0 ) {
				$select->where( Qualifier::AndQualifier( $qualifiers ));
			}
			$objects = $select->limit(-1)->fetchAll();

			$this->view->renderJson( $objects );
		}
	}
}
