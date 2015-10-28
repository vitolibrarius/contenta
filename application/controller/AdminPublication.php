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
use \Processor as Processor;
use \SQL as SQL;
use db\Qualifier as Qualifier;

use connectors\ComicVineConnector as ComicVineConnector;
use processor\ComicVineImporter as ComicVineImporter;
use exceptions\ImportMediaException as ImportMediaException;
use processor\UploadImport as UploadImport;
use utilities\FileWrapper as FileWrapper;

use controller\Admin as Admin;

use model\Users as Users;
use model\Endpoint as Endpoint;
use model\Endpoint_Type as Endpoint_Type;
use model\Publisher as Publisher;
use model\Publication as Publication;
use model\Character as Character;
use model\Character_Alias as Character_Alias;
use model\Series as Series;
use model\Series_Alias as Series_Alias;
use model\Series_Character as Series_Character;
use model\User_Series as User_Series;
use model\Media as Media;
use model\MediaDBO as MediaDBO;

/**
 * Class Admin
 * The index controller
 */
class AdminPublication extends Admin
{
	function index()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$this->view->addStylesheet("select2.min.css");
			$this->view->addScript("select2.min.js");

			$model = Model::Named('Publication');
			$this->view->model = $model;
			$this->view->render( '/admin/publicationIndex');
		}
	}

	function searchPublication()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Publication');
			$qualifiers = array();
			if ( isset($_GET['name']) && strlen($_GET['name']) > 0) {
				$qualifiers[] = Qualifier::Like( Publication::name, $_GET['name']);
			}
			if ( isset($_GET['issue']) && strlen($_GET['issue']) > 0) {
				$qualifiers[] = Qualifier::Equals( Publication::issue_num, $_GET['issue'] );
			}
			if ( isset($_GET['year']) && strlen($_GET['year']) == 4 ) {
				$start = strtotime("01-01-" . $_GET['year'] . " 00:00");
				$end = strtotime("31-12-" . $_GET['year'] . " 23:59");
				$qualifiers[] = Qualifier::Between( Publication::pub_date, $start, $end );
			}
			if ( isset($_GET['media']) && $_GET['media'] === 'true') {
				$qualifiers[] = Qualifier::GreaterThan( Publication::media_count, 0 );
			}
			if ( isset($_GET['character_id']) && is_array($_GET['character_id']) && count($_GET['character_id']) > 0 ) {
				$pub_idArray = Model::Named("Publication_Character")->publicationIdForCharacterIdArray($_GET['character_id']);
				if ( is_array($pub_idArray) && count($pub_idArray) > 0 ) {
					$qualifiers[] = Qualifier::IN( Publication::id, $pub_idArray );
				}
				else {
					$qualifiers[] = Qualifier::Equals( Publication::id, 0 );
				}
			}
			if ( isset($_GET['series_name']) && strlen($_GET['series_name']) > 0) {
				$select = \SQL::Select( Model::Named('Series'), array(Series::id))
					->where( Qualifier::Like( Series::search_name, normalizeSearchString($_GET['series_name'])) );
				$series_idArray = array_map(function($stdClass) {return $stdClass->{Series::id}; },
					$select->fetchAll());

				if ( is_array($series_idArray) && count($series_idArray) > 0 ) {
					$qualifiers[] = Qualifier::IN( Publication::series_id, $series_idArray );
				}
				else {
					$qualifiers[] = Qualifier::Equals( Publication::id, 0 );
				}
			}
			if ( isset($_GET['story_arc_id']) && is_array($_GET['story_arc_id']) && count($_GET['story_arc_id']) > 0 ) {
				$qualifiers[] = Qualifier::InSubQuery( Publication::id,
					SQL::Select(Model::Named('Story_Arc_Publication'), array("publication_id"))
						->where( Qualifier::IN( "story_arc_id", $_GET['story_arc_id']))
						->limit(0)
				);
			}

			$select = SQL::Select($model);
			if ( count($qualifiers) > 0 ) {
				$select->where( Qualifier::AndQualifier( $qualifiers ));
			}
			$select->orderBy( $model->sortOrder() );

			$this->view->model = $model;
			$this->view->listArray = $select->fetchAll();
			$this->view->editAction = "/AdminPublication/editPublication";
			$this->view->deleteAction = "/AdminPublication/deletePublication";
			$this->view->render( '/admin/publicationCards', true);
		}
	}

	function deletePublication($oid = null)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Publication');
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

	function editPublication($oid = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$this->view->addStylesheet("select2.min.css");
			$this->view->addScript("select2.min.js");

			$model = Model::Named('Publication');
			$this->view->model = $model;
			if ( $oid > 0 ) {
				$this->view->object = $model->objectForId($oid);
			}
			$this->view->saveAction = "/AdminPublication/savePublication";
			$this->view->additionalAction = "/AdminPublication/updatedAdditional";

			$this->view->render( '/edit/publication');
		}
	}

	function savePublication($oid = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Publication');
			$values = splitPOSTValues($_POST);

			if ( $oid > 0 ) {
				$object = $model->objectForId($oid);
				if ( $object != false ) {
					$errors = $model->updateObject($object, $values[$model->tableName()]);
					if ( is_array($errors) ) {
						Session::addNegativeFeedback( Localized::GlobalLabel("Validation Errors") );
						foreach ($errors as $attr => $errMsg ) {
							Session::addValidationFeedback($errMsg);
						}
						$this->editPublication($oid);
					}
					else {
						Session::addPositiveFeedback(Localized::GlobalLabel( "Save Completed" ));
						$this->index();
					}
				}
				else {
					Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) );
					$this->view->render('/error/index');
				}
			}
			else {
				list($obj, $error) = $model->createObject($values[$model->tableName()]);
				if ( is_array($errors) ) {
					Session::addNegativeFeedback( Localized::GlobalLabel("Validation Errors") );
					foreach ($errors as $attr => $errMsg ) {
						Session::addValidationFeedback( $errMsg );
					}
					$this->editPublication();
				}
				else {
					Session::addPositiveFeedback(Localized::GlobalLabel( "Save Completed" ));
					$this->index();
				}
			}
		}
	}

	function updatedAdditional($oid = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Publication');
			$values = splitPOSTValues($_POST);

			if ( $oid > 0 ) {
				$object = $model->objectForId($oid);
				$endpoint = $object->externalEndpoint();
				if ( $endpoint != false ) {
					$importer = new ComicVineImporter( $model->tableName() . "_" .$object->xid );
					if ( $importer->endpoint() == false ) {
						$importer->setEndpoint($endpoint);
					}

					$importer->enqueue_publication( array( "xid" => $object->xid), true, true );
					$importer->daemonizeProcess();
					sleep(2);
					header('location: ' . Config::Web('/AdminPublication/editPublication/' . $oid));
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
				$model = Model::Named('Publication');
				if ( $oid > 0 ) {
					$object = $model->objectForId($oid);
					if ( $object != false ) {
						$this->view->object = $object;
					}
				}
				$this->view->searchAction = "/AdminPublication/comicVineSearchAction";
				$this->view->endpoints = $points;
				$this->view->ep_model = $ep_model;
				$this->view->model = $model;
				$this->view->render('/import/comicvine_publication');
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
				$model = Model::Named('Publication');
				$values = splitPOSTValues($_POST);
				if ( isset($values, $values[Publication::TABLE], $values[Publication::TABLE][Publication::name]) ) {
					$name = $values[Publication::TABLE][Publication::name];
					$year = 0;
					if ( isset($values[Publication::TABLE][Publication::start_year]) ) {
						$year = $values[Publication::TABLE][Publication::start_year];
					}
					$epoint = $points[0];
					$connection = new ComicVineConnector($epoint);

					$this->view->endpoint = $epoint;
					$this->view->pub_model = Model::Named("Publisher");
					$this->view->model = $model;
					$this->view->results = $connection->series_searchFilteredForYear( $name, $year );
					$this->view->importAction = "/AdminPublication/comicVineImportAction";
				}
				else {
					Session::addNegativeFeedback( Localized::ModelSearch( Publication::TABLE, Publication::name, "SEARCH_EMPTY" ));
				}
			}

			$this->view->render('/import/comicvine_publication_results', true);
		}
	}

	function comicVineImportAction($xid = null, $name = null)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			if ( isset($xid) && is_null($xid) == false) {
				if ( isset($name) == false || strlen($name) == 0) {
					$name = 'Unknown ' . $xid;
				}
				$existing = Model::Named('Publication')->findExternalOrCreate(null, $name, null, null, $xid, Endpoint_Type::ComicVine, null, null );
				if ( $existing != false ) {
					Session::addPositiveFeedback( Localized::ModelLabel( 'Publication', "SUCCESS_CREATE" ) );
				}

				$importer = new ComicVineImporter( Publication::TABLE . "_" .$xid );
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
					$importer->importPublicationValues( null, $xid, null, true);
					$importer->daemonizeProcess();
				}

				$this->index();
			}
		}
	}
}
