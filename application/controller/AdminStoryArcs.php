<?php

namespace controller;

use \Controller as Controller;
use \DataObject as DataObject;
use \Model as Model;
use \Auth as Auth;
use \Logger as Logger;
use \Localized as Localized;
use \Config as Config;

use \SQL as SQL;
use db\Qualifier as Qualifier;

use \http\Session as Session;
use \http\HttpGet as HttpGet;
use \http\HttpPost as HttpPost;
use \http\PageParams as PageParams;

use connectors\ComicVineConnector as ComicVineConnector;
use processor\ComicVineImporter as ComicVineImporter;

use controller\Admin as Admin;

use \model\user\Users as Users;
use \model\network\Endpoint as Endpoint;
use \model\network\Endpoint_Type as Endpoint_Type;
use \model\media\Publisher as Publisher;
use \model\media\Character as Character;
use \model\media\Character_Alias as Character_Alias;
use \model\media\Story_Arc as Story_Arc;
use \model\media\Story_Arc_Character as Story_Arc_Character;
use \model\media\Series as Series;

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

			$parameters = Session::pageParameters( $this, "index" );
			$this->view->params = $parameters;

			$model = Model::Named('Story_Arc');
			$this->view->model = $model;
			$this->view->render( '/admin/story_arcIndex');
		}
	}

	function searchStoryArcs($pageNum = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$parameters = Session::pageParameters( $this, "index" );
			$parameters->setPageSize(18);
			list( $hasNewValues, $query) = $parameters->updateParametersFromGET( array(
				'searchSeries', 'searchCharacter', 'searchPublisher', 'searchWanted', 'searchName' )
			);

			$model = Model::Named('Story_Arc');
			$qualifiers = array();
			if ( isset($query['searchName']) && strlen($query['searchName']) > 0) {
				$qualifiers[] = Qualifier::Like( Story_Arc::name, $query['searchName'] );
			}
			if ( isset($query['searchPublisher']) && intval($query['searchPublisher']) > 0 ) {
				$qualifiers[] = Qualifier::Equals( Story_Arc::publisher_id, $query['searchPublisher'] );
			}
			if ( isset($query['searchSeries']) && strlen($query['searchSeries']) > 0) {
				$select = \SQL::Select( Model::Named('Series'), array(Series::id))
					->where( Qualifier::Like( Series::search_name, normalizeSearchString($query['searchSeries'])) );
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
			if ( isset($query['searchCharacter']) && empty($query['searchCharacter']) == false) {
				$characterIdArray = (is_array($query['searchCharacter']) ? $query['searchCharacter'] : array($query['searchCharacter']));
				$idArray = Model::Named("Story_Arc_Character")->storyArcIdForCharacterIdArray($characterIdArray);
				if ( is_array($idArray) && count($idArray) > 0 ) {
					$qualifiers[] = Qualifier::IN( Story_Arc::id, $idArray );
				}
				else {
					$qualifiers[] = Qualifier::Equals( Story_Arc::id, 0 );
				}
			}
			if ( isset($query['searchWanted']) && $query['searchWanted'] == '1') {
				$qualifiers[] = Qualifier::Equals( Story_Arc::pub_wanted, 1 );
			}

			if ( $hasNewValues ) {
				if ( count($qualifiers) > 0 ) {
					$count = SQL::Count( $model, null, Qualifier::AndQualifier( $qualifiers ) )->fetch();
				}
				else {
					$count = SQL::Count( $model )->fetch();
				}

				$parameters->queryResults($count->count);
			}
			else {
				if ( is_null( $pageNum) ) {
					$pageNum = $parameters->valueForKey( PageParams::PAGE_SHOWN, 0 );
				}
				else {
					$parameters->setValueForKey( PageParams::PAGE_SHOWN, $pageNum );
				}
			}

			$select = SQL::Select($model);
			if ( count($qualifiers) > 0 ) {
				$select->where( Qualifier::AndQualifier( $qualifiers ));
			}
			$select->limit($parameters->pageSize());
			$select->offset($parameters->pageShown());
			$select->orderBy( $model->sortOrder() );

			$this->view->model = $model;
			$this->view->params = $parameters;
			$this->view->listArray = $select->fetchAll();
			$this->view->editAction = "/AdminStoryArcs/editStoryArc";
			$this->view->wantedAction = "/AdminStoryArcs/toggleWantedStoryArc";
			$this->view->render( '/admin/story_arcCards', true);
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
					Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) . " " . $model->tableName() . " [$oid]" );
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
				$object = $model->objectForId($oid);
				if ( $object != false ) {
					$this->view->object = $object;
				}
			}
			$this->view->saveAction = "/AdminStoryArcs/saveStoryArc";
			$this->view->additionalAction = "/AdminStoryArcs/updatedAdditional";
			$this->view->editPublicationAction = "/AdminPublication/editPublication";
			$this->view->editCharacterAction = "/AdminCharacters/editCharacter";
			$this->view->editSeriesAction = "/AdminSeries/editSeries";

			$this->view->render( '/edit/story_arc');
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
					list($object, $errors) = $model->updateObject($object, $values[$model->tableName()]);
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
					Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) . " " . $model->tableName() . " [$oid]" );
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

	function toggleWantedStoryArc($oid = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			if ( $oid > 0 ) {
				$model = Model::Named('Story_Arc');
				$object = $model->objectForId($oid);
				if ( $object != false ) {
					$oldIsWanted = $object->isWanted();
					$newIsWanted = ($object->isWanted() ? MODEL::TERTIARY_FALSE : MODEL::TERTIARY_TRUE);
					$values[Story_Arc::pub_wanted] = $newIsWanted;
					list($object, $errors) = $model->updateObject($object, $values);
					if ( is_array($errors) ) {
						Session::addNegativeFeedback( Localized::GlobalLabel("Validation Errors") );
						foreach ($errors as $attr => $errMsg ) {
							Session::addValidationFeedback($errMsg);
						}
					}
					else {
						$object = $model->refreshObject( $object );
						if ( $newIsWanted == MODEL::TERTIARY_TRUE ) {
							// now a wanted series, ensure the publications are updated
							$endpoint = $object->externalEndpoint();
							if ( $endpoint != false ) {
								$importer = new ComicVineImporter( $model->tableName() . "_" .$object->xid );
								$importer->setEndpoint($endpoint);
								$importer->refreshPublicationsForObject( $object );
								$importer->daemonizeProcess();
							}
							echo json_encode(array(Story_Arc::pub_wanted => true) );
						}
						else {
							echo json_encode(array(Story_Arc::pub_wanted => false) );
						}
					}
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
			$points = $ep_model->allForType_code(Endpoint_Type::ComicVine);
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
			$points = $ep_model->allForType_code(Endpoint_Type::ComicVine);
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
					$points = $ep_model->allForType_code(Endpoint_Type::ComicVine);
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
