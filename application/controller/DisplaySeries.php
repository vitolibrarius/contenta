<?php

namespace controller;

use \Controller as Controller;
use \DataObject as DataObject;
use \Model as Model;
use \Auth as Auth;
use \Logger as Logger;
use \Localized as Localized;
use \Config as Config;

use \http\Session as Session;;

use \SQL as SQL;
use db\Qualifier as Qualifier;

use connectors\ComicVineConnector as ComicVineConnector;
use processor\ComicVineImporter as ComicVineImporter;

use \model\user\Users as Users;
use \model\network\Endpoint as Endpoint;
use \model\network\Endpoint_Type as Endpoint_Type;
use \model\media\Publisher as Publisher;
use \model\media\Character as Character;
use \model\media\Character_Alias as Character_Alias;
use \model\media\Series as Series;
use \model\media\Series_Alias as Series_Alias;
use \model\media\Series_Character as Series_Character;
use \model\reading\Reading_Queue as Reading_Queue;
use \model\reading\Reading_QueueDBO as Reading_QueueDBO;
use \model\reading\Reading_Item as Reading_Item;
use \model\reading\Reading_ItemDBO as Reading_ItemDBO;

/**
 * Class Admin
 * The index controller
 */
class DisplaySeries extends Controller
{
	function index()
	{
		if (Auth::handleLogin()) {
			$this->view->addStylesheet("select2.min.css");
			$this->view->addScript("select2.min.js");

			$model = Model::Named('Series');
			$this->view->model = $model;
			$this->view->render( '/series/index');
		}
	}

	function searchSeries($pageNum = 0)
	{
		if (Auth::handleLogin()) {
			$model = Model::Named('Series');
			$qualifiers = array();
			if ( isset($_GET['name']) && strlen($_GET['name']) > 0) {
				$qualifiers[] = Qualifier::Like( Series::search_name, normalizeSearchString($_GET['name']));
			}
			if ( isset($_GET['year']) && strlen($_GET['year']) == 4 ) {
				$qualifiers[] = Qualifier::Equals( Series::start_year, $_GET['year'] );
			}
			if ( isset($_GET['publisher_id']) && intval($_GET['publisher_id']) > 0 ) {
				$qualifiers[] = Qualifier::Equals( Series::publisher_id, $_GET['publisher_id'] );
			}

			$select = SQL::Select($model);
			if ( count($qualifiers) > 0 ) {
				$select->where( Qualifier::AndQualifier( $qualifiers ));
			}
			$select->orderBy( $model->sortOrder() );

			$this->view->model = $model;
			$this->view->listArray = $select->fetchAll();
 			$this->view->detailAction = "/DisplaySeries/details";
			$this->view->queuedPath = "/DisplaySeries/toggleReadingQueue";
			$this->view->render( '/series/seriesCards', true);
		}
	}

	function details($oid = 0)
	{
		if (Auth::handleLogin()) {
			if ( $oid > 0 ) {
				$model = Model::Named('Series');
				$object = $model->objectForId($oid);
				if ( $object != false ) {
					$this->view->model = $model;
					$this->view->setViewTitle($object->name);
					$this->view->detail = $object;
					$this->view->storyArcAction = "/DisplayStories/details";
					$this->view->render( '/series/seriesDetails' );
				}
				else {
					Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) . " " . $model->tableName() . " [$oid]" );
					$this->view->render('/error/index');
				}
			}
			else {
				Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) );
				$this->view->render('/error/index');
			}
		}
	}

	function toggleReadingQueue($oid = 0)
	{
		if (Auth::handleLogin() ) {
			if ( $oid > 0 ) {
				$user = Session::sessionUser();

				$model = Model::Named('Series');
				$rq_model = Model::Named('Reading_Queue');
				$series = $model->objectForId($oid);
				if ( $series != false ) {
					$readingQueue = $rq_model->objectForUserAndSeries($user, $series);
					if ( $readingQueue != false ) {
						$rq_model->deleteObject($readingQueue);
						$this->view->renderJson(array("toggled_on" => false) );
					}
					else {
						$rq_model->createReadingQueueSeries($user, $series);
						$this->view->renderJson(array("toggled_on" => true) );
					}
				}
			}
		}
	}
}
