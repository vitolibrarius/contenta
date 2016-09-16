<?php

namespace controller;

use \Controller as Controller;
use \DataObject as DataObject;
use \Model as Model;
use \Auth as Auth;

use \http\Session as Session;
use \http\HttpGet as HttpGet;

use \model\user\Users as Users;
use \model\reading\Reading_Queue as Reading_Queue;
use \model\reading\Reading_QueueDBO as Reading_QueueDBO;
use \model\reading\Reading_Item as Reading_Item;
use \model\reading\Reading_ItemDBO as Reading_ItemDBO;
use \model\reading\Reading_Queue_Item as Reading_Queue_Item;
use \model\reading\Reading_Queue_ItemDBO as Reading_Queue_ItemDBO;

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
			$user = Session::sessionUser();
			$queue_model = Model::Named("Reading_Queue");

			$this->view->model = $queue_model;
			$this->view->queues = $queue_model->allForUserUnread($user);
			$this->view->render( '/index/index' );
		}
	}

	function queueOrder($action = 'up', $queueId = 0)
	{
		if (Auth::handleLogin()) {
			$user = Session::sessionUser();
			$queue_model = Model::Named("Reading_Queue");
			$queue = false;
			if ( $queueId > 0 ) {
				$queue = $queue_model->objectForId($queueId);
			}

			if ( $queue == false && $action != 'reset' ) {
				Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) );
				$this->view->render('/error/index');
			}
			else {
				switch ($action) {
					case 'top':
						$queue_model->moveToTopQueuePriority( $queue );
						break;
					case 'up':
						$queue_model->increaseQueuePriority( $queue );
						break;
					case 'down':
						$queue_model->decreaseQueuePriority( $queue );
						break;
					case 'reset':
						$queue_model->resetQueuePriority( $user );
						break;
					default:
						Session::addNegativeFeedback("unknown action " . $action);
						break;
				}

				$this->view->model = $queue_model;
				$this->view->queues = $queue_model->allForUserUnread($user);
				$this->view->render( '/index/index' );
			}
		}
	}

	function ajax_queueItems()
	{
		if (Auth::handleLogin()) {
			$queue_id = HttpGet::get( "queue_id", 0 );
			$showAll = HttpGet::get( "showAll", false );
			if ( $queue_id > 0 ) {
				$queue = Model::Named("Reading_Queue")->objectForId($queue_id);
				$item_model = Model::Named("Reading_Queue_Item");
				$this->view->readItemPath = "/Index/ajax_readItem";
				$this->view->listArray = $item_model->allForReadingQueue( $queue, ($showAll == false) );
			}

			$this->view->render( '/reading_queue/readingItemCards', true );
		}
	}

	function ajax_readItem()
	{
		if (Auth::handleLogin()) {
			$queue_item_id = HttpGet::get( "record_id", 0 );
			if ( $queue_item_id > 0 ) {
				$item_model = Model::Named("Reading_Queue_Item");
				$queue_item = $item_model->objectForId($queue_item_id);

				$item = $queue_item->reading_item();
				if ( isset($item->read_date) && is_null($item->read_date) == false ) {
					$item->setRead_date(null);
					$item->saveChanges();
					$this->view->renderJson(array("toggled_on" => false) );
				}
				else {
					$item->setRead_date(time());
					$item->saveChanges();
					$this->view->renderJson(array("toggled_on" => true) );
				}
			}
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
