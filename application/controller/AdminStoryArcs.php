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

use connectors\ComicVineConnector as ComicVineConnector;
use processor\ComicVineImporter as ComicVineImporter;

use controller\Admin as Admin;

use model\Users as Users;
use model\Endpoint as Endpoint;
use model\Endpoint_Type as Endpoint_Type;
use model\Publisher as Publisher;
use model\Character as Character;
use model\Character_Alias as Character_Alias;
use model\Story_Arc as Story_Arc;
use model\Story_Arc_Character as Story_Arc_Character;
use model\Series as Series;

use \SQL as SQL;
use db\Qualifier as Qualifier;

/**
 * Class Admin
 * The index controller
 */
class AdminStoryArcs extends Admin
{
	function index()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
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

			$select = SQL::Select($model);
			if ( count($qualifiers) > 0 ) {
				$select->where( Qualifier::AndQualifier( $qualifiers ));
			}
			$select->orderBy( $model->sortOrder() );
//
// 						Session::addPositiveFeedback("select ". $select);

			$this->view->model = $model;
			$this->view->listArray = $select->fetchAll();
			$this->view->editAction = "/AdminStoryArcs/editStoryArc";
			$this->view->deleteAction = "/AdminStoryArcs/deleteStoryArc";
			$this->view->render( '/story_arcs/story_arcCards', true);
		}
	}

	function deleteStoryArc($oid = null)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Story_Arc');
			if ( $oid > 0 ) {
				$object = $model->objectForId($oid);
				if ( $object != false ) {
					$errors = $model->deleteObject($object);
					if ( $errors == false ) {
						Session::addNegativeFeedback( Localized::GlobalLabel("Delete Failure") );
					}
					else {
						Session::addPositiveFeedback(Localized::GlobalLabel( "Delete Completed" ));
					}
				}
				else {
					Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) );
				}
			}
			else {
				Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) );
			}
			$this->index();
		}
	}

	function editStoryArc($oid = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$this->view->addStylesheet("select2.min.css");
			$this->view->addScript("select2.min.js");

			$model = Model::Named('Story_Arc');
			$this->view->model = $model;
			if ( $oid > 0 ) {
				$this->view->object = $model->objectForId($oid);
			}
			$this->view->saveAction = "/AdminStoryArcs/saveStoryArc";
			$this->view->additionalAction = "/AdminStoryArcs/updatedAdditional";

			$this->view->render( '/edit/story_arc');

			\Logger::loginfo("Editing StoryArc " . $oid, Session::get('user_name'), Session::get('user_id'));
		}
	}

	function saveStoryArc($oid = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Story_Arc');
			$values = splitPOSTValues($_POST);
			if ( isset($values[$model->tableName()], $values[$model->tableName()][Story_Arc::pub_wanted]) == false ) {
				$values[$model->tableName()][Story_Arc::pub_wanted] = Model::TERTIARY_FALSE;
			}

			if ( $oid > 0 ) {
				$object = $model->objectForId($oid);
				if ( $object != false ) {
					$oldIsWanted = $object->isWanted();
					$errors = $model->updateObject($object, $values[$model->tableName()]);
					if ( is_array($errors) ) {
						Session::addNegativeFeedback( Localized::GlobalLabel("Validation Errors") );
						foreach ($errors as $attr => $errMsg ) {
							Session::addValidationFeedback($errMsg);
						}
						$this->editStoryArc($oid);
					}
					else {
						$object = $model->refreshObject($object);
						if ( $object->isWanted() && $oldIsWanted == false ) {
							// now a wanted story_arc, ensure the publications are updated
							$endpoint = $object->externalEndpoint();
							if ( $endpoint != false ) {
								$importer = new ComicVineImporter( $model->tableName() . "_" .$object->xid );
								$importer->setEndpoint($endpoint);
								$importer->enqueue_story_arc( array( "xid" => $object->xid), true, true );
								foreach( $object->publications() as $publication ) {
									if ( $publication->series() == null || isset($publication->xupdated) == false) {
										$importer->enqueue_publication( array( "xid" => $publication->xid), true, true );
									}
								}
								$importer->daemonizeProcess();
							}
						}
						Session::addPositiveFeedback(Localized::GlobalLabel( "Save Completed" ));
						header('location: ' . Config::Web('/AdminStoryArcs/editStoryArc/' . $oid));
					}
				}
				else {
					Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) );
					$this->view->render('/error/index');
				}
			}
			else {
				list($object, $error) = $model->createObject($values[$model->tableName()]);
				if ( is_array($errors) ) {
					Session::addNegativeFeedback( Localized::GlobalLabel("Validation Errors") );
					foreach ($errors as $attr => $errMsg ) {
						Session::addValidationFeedback( $errMsg );
					}
					header('location: ' . Config::Web('/AdminStoryArcs/editStoryArc/'));
				}
				else {
					Session::addPositiveFeedback(Localized::GlobalLabel( "Save Completed" ));
					header('location: ' . Config::Web('/AdminStoryArcs/editStoryArc/' . $object->id));
				}
			}
		}
	}

	function updatedAdditional($oid = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Story_Arc');
			$values = splitPOSTValues($_POST);

			if ( $oid > 0 ) {
				$object = $model->objectForId($oid);
				$endpoint = $object->externalEndpoint();
				if ( $endpoint != false ) {
					$importer = new ComicVineImporter( $model->tableName() . "_" .$object->xid );
					if ( $importer->endpoint() == false ) {
						$importer->setEndpoint($endpoint);
					}

					$importer->enqueue_story_arc( array( "xid" => $object->xid), true, true );
					$importer->daemonizeProcess();
					sleep(2);
					header('location: ' . Config::Web('/AdminStoryArcs/editStoryArc/' . $oid));
				}
				else {
					Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find requested endpoint" ) );
					$this->view->render('/error/index');
				}
			}
			else {
				Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) );
				$this->view->render('/error/index');
			}
		}
	}

	function comicVineSearch($oid = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$ep_model = Model::Named('Endpoint');
			$points = $ep_model->allForTypeCode(Endpoint_Type::ComicVine);
			if ( $points == false || count($points) == 0) {
				Session::addNegativeFeedback(Localized::GlobalLabel( "PLEASE_ADD_ENDPOINT" ) );
				header('location: ' . Config::Web('/netconfig/index'));
			}
			else {
				$model = Model::Named('Story_Arc');
				if ( $oid > 0 ) {
					$object = $model->objectForId($oid);
					if ( $object != false ) {
						$this->view->object = $object;
					}
				}
				$this->view->searchAction = "/AdminStoryArcs/comicVineSearchAction";
				$this->view->endpoints = $points;
				$this->view->ep_model = $ep_model;
				$this->view->model = $model;
				$this->view->render('/import/comicvine_StoryArc');
			}
		}
	}

	function comicVineSearchAction($oid = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$ep_model = Model::Named('Endpoint');
			$points = $ep_model->allForTypeCode(Endpoint_Type::ComicVine);
			if ( $points == false || count($points) == 0) {
				Session::addNegativeFeedback(Localized::GlobalLabel( "PLEASE_ADD_ENDPOINT" ) );
			}
			else {
				$model = Model::Named('Story_Arc');
				$values = splitPOSTValues($_POST);
				if ( isset($values, $values[Story_Arc::TABLE], $values[Story_Arc::TABLE][Story_Arc::name]) ) {
					$name = $values[Story_Arc::TABLE][Story_Arc::name];
					$epoint = $points[0];
					$connection = new ComicVineConnector($epoint);

					$this->view->endpoint = $epoint;
					$this->view->pub_model = Model::Named("Publisher");
					$this->view->model = $model;
					$this->view->results = $connection->story_arc_search( null, $name );
					$this->view->importAction = "/AdminStoryArcs/comicVineImportAction";
				}
				else {
					Session::addNegativeFeedback( Localized::ModelSearch( Story_Arc::TABLE, Story_Arc::name, "SEARCH_EMPTY" ));
				}
			}

			$this->view->render('/import/comicvine_story_arc_results', true);
		}
	}

	function comicVineImportAction($xid = null, $name = null)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			if ( isset($xid) && is_null($xid) == false) {
				if ( isset($name) == false || strlen($name) == 0) {
					$name = 'Unknown ' . $xid;
				}
				$existing = Model::Named('Story_Arc')->findExternalOrCreate(null, $name, null, null, null, null, $xid, Endpoint_Type::ComicVine, null );
				if ( $existing != false ) {
					Session::addPositiveFeedback( Localized::ModelLabel( 'story_arc', "SUCCESS_CREATE" ) );
				}

				$importer = new ComicVineImporter( StoryArc::TABLE . "_" .$xid );
				if ( $importer->endpoint() == false ) {
					$ep_model = Model::Named('Endpoint');
					$points = $ep_model->allForTypeCode(Endpoint_Type::ComicVine);
					if ( $points == false || count($points) == 0) {
						Session::addNegativeFeedback(Localized::GlobalLabel( "PLEASE_ADD_ENDPOINT" ) );
					}
					else {
						$importer->setEndpoint($points[0]);
					}
				}

				if ( $importer->endpoint() != false ) {
					$importer->enqueue_story_arc( array( "xid" => $xid, "name" => $name), true, true );
					$importer->daemonizeProcess();
				}

				$this->index();
			}
		}
	}
}
