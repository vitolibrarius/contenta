<?php

namespace controller;

use \Controller as Controller;
use \DataObject as DataObject;
use \Model as Model;
use \Auth as Auth;
use \Session as Session;
use \Logger as Logger;
use \Localized as Localized;
use \Config as Config;

use \SQL as SQL;
use db\Qualifier as Qualifier;

use connectors\ComicVineConnector as ComicVineConnector;
use processor\ComicVineImporter as ComicVineImporter;

use model\Users as Users;
use model\Endpoint as Endpoint;
use model\Endpoint_Type as Endpoint_Type;
use model\Publisher as Publisher;
use model\Character as Character;
use model\Character_Alias as Character_Alias;
use model\Series as Series;
use model\Series_Alias as Series_Alias;
use model\Series_Character as Series_Character;
use model\Story_Arc as Story_Arc;
use model\User_Series as User_Series;

/**
 * Class Admin
 * The index controller
 */
class DisplayStories extends Controller
{
	function index()
	{
		if (Auth::handleLogin()) {
			$this->view->addStylesheet("select2.min.css");
			$this->view->addScript("select2.min.js");

			$model = Model::Named('Story_Arc');
			$this->view->model = $model;
			$this->view->render( '/story_arcs/index');
		}
	}

	function searchStoryArcs()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Story_Arc');
			$qualifiers = array();
			if ( isset($_GET['name']) && strlen($_GET['name']) > 0) {
				$qualifiers[] = Qualifier::Like( Story_Arc::name, $_GET['name'] );
			}
			if ( isset($_GET['publisher_id']) && intval($_GET['publisher_id']) > 0 ) {
				$qualifiers[] = Qualifier::Equals( Story_Arc::publisher_id, $_GET['publisher_id'] );
			}
			if ( isset($_GET['series_name']) && strlen($_GET['series_name']) > 0) {
				$select = \SQL::Select( Model::Named('Series'), array(Series::id))
					->where( Qualifier::Like( Series::search_name, normalizeSearchString($_GET['series_name'])) );
				$series_idArray = array_map(function($stdClass) {return $stdClass->{Series::id}; },
					$select->fetchAll());

				$storyArcsIds = Model::Named("Story_Arc_Series")->storyArcIdForAnySeriesIdArray( $series_idArray );

				if ( is_array($storyArcsIds) && count($storyArcsIds) > 0 ) {
					$qualifiers[] = Qualifier::IN( Story_Arc::id, $storyArcsIds );
				}
				else {
					$qualifiers[] = Qualifier::Equals( Story_Arc::id, 0 );
				}
			}
			if ( isset($_GET['character_id']) && is_array($_GET['character_id']) && count($_GET['character_id']) > 0 ) {
				$idArray = Model::Named("Story_Arc_Character")->storyArcIdForCharacterIdArray($_GET['character_id']);
				if ( is_array($idArray) && count($idArray) > 0 ) {
					$qualifiers[] = Qualifier::IN( Story_Arc::id, $idArray );
				}
				else {
					$qualifiers[] = Qualifier::Equals( Story_Arc::id, 0 );
				}
			}
			if ( isset($_GET['wanted']) && $_GET['wanted'] === 'true') {
				$qualifiers[] = Qualifier::Equals( Story_Arc::pub_wanted, 1 );
			}

			$select = SQL::Select($model);
			if ( count($qualifiers) > 0 ) {
				$select->where( Qualifier::AndQualifier( $qualifiers ));
			}
			$select->orderBy( $model->sortOrder() );
//
// 						Session::addPositiveFeedback("select ". $select);

			$this->view->model = $model;
			$this->view->listArray = $select->fetchAll();
 			$this->view->detailAction = "/DisplayStories/details";
			$this->view->render( '/story_arcs/story_arcCards', true);
		}
	}

	function details($oid = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			if ( $oid > 0 ) {
				$model = Model::Named('Story_Arc');
				$object = $model->objectForId($oid);
				if ( $object != false ) {
					$this->view->model = $model;
					$this->view->setViewTitle($object->name);
					$this->view->detail = $object;
					$this->view->seriesAction = "/DisplaySeries/details";
					$this->view->render( '/story_arcs/story_arcDetails' );
				}
				else {
					Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) );
					$this->view->render('/error/index');
				}
			}
			else {
				Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) );
				$this->view->render('/error/index');
			}
		}
	}
}
