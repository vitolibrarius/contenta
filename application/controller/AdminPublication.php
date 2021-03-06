<?php

namespace controller;

use \Controller as Controller;
use \DataObject as DataObject;
use \Model as Model;
use \Auth as Auth;
use \Logger as Logger;
use \Localized as Localized;
use \Config as Config;
use \Processor as Processor;
use \SQL as SQL;

use \db\Qualifier as Qualifier;

use \http\Session as Session;
use \http\HttpGet as HttpGet;
use \http\HttpPost as HttpPost;
use \http\PageParams as PageParams;

use connectors\ComicVineConnector as ComicVineConnector;
use processor\ComicVineImporter as ComicVineImporter;
use exceptions\ImportMediaException as ImportMediaException;
use processor\UploadImport as UploadImport;
use utilities\FileWrapper as FileWrapper;

use controller\Admin as Admin;

use \model\user\Users as Users;
use \model\network\Endpoint as Endpoint;
use \model\network\Endpoint_Type as Endpoint_Type;
use \model\media\Publisher as Publisher;
use \model\media\Publication as Publication;
use \model\media\Character as Character;
use \model\media\Character_Alias as Character_Alias;
use \model\media\Series as Series;
use \model\media\Series_Alias as Series_Alias;
use \model\media\Series_Character as Series_Character;
use \model\media\Media as Media;
use \model\media\MediaDBO as MediaDBO;

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

			$parameters = Session::pageParameters( $this, "index" );
			$this->view->params = $parameters;

			$model = Model::Named('Publication');
			$this->view->model = $model;
			$this->view->render( '/admin/publicationIndex');
		}
	}

	function deleteMedia($oid = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			if ( $oid > 0 ) {
				$model = Model::Named('Media');
				$media = $model->objectForId($oid);
				if ($media instanceof MediaDBO ) {
					$publication_id = $media->publication_id;
					$errors = $model->deleteObject($media);
					if ( $errors == false ) {
						Session::addNegativeFeedback( Localized::GlobalLabel("Delete Failure") );
					}
					else {
						Session::addPositiveFeedback(Localized::GlobalLabel( "Delete Completed" ));
					}
					header('location: ' . Config::Web('/AdminPublication/editPublication/' . $publication_id));
				}
				else {
					Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) . " " .var_export($media, true));
					$this->view->render('/error/index');
				}
			}
			else {
				Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) . " " . $model->tableName() . " [$oid]");
				$this->view->render('/error/index');
			}
		}
	}

	function searchPublication($pageNum = null)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$parameters = Session::pageParameters( $this, "index" );
			list( $hasNewValues, $query) = $parameters->updateParametersFromGET( array(
				'searchSeries',
				'searchIssue',
				'searchYear',
				'searchMedia',
				'searchCharacter',
				'searchStoryArcs' )
			);

			$model = Model::Named('Publication');
			$qualifiers = array();
			if ( isset($query['searchIssue']) && strlen($query['searchIssue']) > 0) {
				$qualifiers[] = Qualifier::Equals( Publication::issue_num, $query['searchIssue'] );
			}
			if ( isset($query['searchYear']) && strlen($query['searchYear']) == 4) {
				$start = strtotime("01-01-" . $query['searchYear'] . " 00:00");
				$end = strtotime("31-12-" . $query['searchYear'] . " 23:59");
				$qualifiers[] = Qualifier::Between( Publication::pub_date, $start, $end );
			}
			if ( isset($query['searchMedia']) && $query['searchMedia'] === 'true') {
				$qualifiers[] = Qualifier::GreaterThan( Publication::media_count, 0 );
			}
			if ( isset($query['searchCharacter']) && empty($query['searchCharacter']) == false) {
				$characterIdArray = (is_array($query['searchCharacter']) ? $query['searchCharacter'] : array($query['searchCharacter']));
				$pub_idArray = Model::Named("Publication_Character")->publicationIdForCharacterIdArray($characterIdArray);
				if ( is_array($pub_idArray) && count($pub_idArray) > 0 ) {
					$qualifiers[] = Qualifier::IN( Publication::id, $pub_idArray );
				}
				else {
					$qualifiers[] = Qualifier::Equals( Publication::id, 0 );
				}
			}
			if ( isset($query['searchSeries']) && strlen($query['searchSeries']) > 0) {
				$select = \SQL::Select( Model::Named('Series'), array(Series::id))
					->where( Qualifier::Like( Series::search_name, normalizeSearchString($query['searchSeries'])) );
				$series_idArray = array_map(function($stdClass) {return $stdClass->{Series::id}; },
					$select->fetchAll());

				if ( is_array($series_idArray) && count($series_idArray) > 0 ) {
					$qualifiers[] = Qualifier::IN( Publication::series_id, $series_idArray );
				}
				else {
					$qualifiers[] = Qualifier::Equals( Publication::id, 0 );
				}
			}
			if ( isset($query['searchStoryArcs']) && empty($query['searchStoryArcs']) == false) {
				$storyArcIdArray = (is_array($query['searchStoryArcs']) ? $query['searchStoryArcs'] : array($query['searchStoryArcs']));
				$qualifiers[] = Qualifier::InSubQuery( Publication::id,
					SQL::Select(Model::Named('Story_Arc_Publication'), array("publication_id"))
						->where( Qualifier::IN( "story_arc_id", $storyArcIdArray))
						->limit(0)
				);
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
					Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) . " " . $model->tableName() . " [$oid]" );
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
				$object = $model->objectForId($oid);
				if ( $object != false ) {
					$this->view->object = $object;
				}
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
					list($object, $errors) = $model->updateObject($object, $values[$model->tableName()]);
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
					Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) . " " . $model->tableName() . " [$oid]" );
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
			$points = $ep_model->allForType_code(Endpoint_Type::ComicVine);
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
			$points = $ep_model->allForType_code(Endpoint_Type::ComicVine);
			if ( $points == false || count($points) == 0) {
				Session::addNegativeFeedback(Localized::GlobalLabel( "PLEASE_ADD_ENDPOINT" ) );
			}
			else {
				$model = Model::Named('Publication');
				if ( isset($_POST['searchxid']) || isset($_POST['searchseries_name'])
					|| isset($_POST['searchYear']) || isset($_POST['searchissue'])) {
					$epoint = $points[0];
					$xid = (isset($_POST['searchxid']) ? $_POST['searchxid'] : null);
					$seriesName = (isset($_POST['searchseries_name']) ? $_POST['searchseries_name'] : null);
					$searchYear = (isset($_POST['searchYear']) ? $_POST['searchYear'] : null);
					$searchissue = (isset($_POST['searchissue']) ? $_POST['searchissue'] : null);
					$connection = new ComicVineConnector($epoint);

					$this->view->endpoint = $epoint;
					$this->view->pub_model = Model::Named("Publisher");
					$this->view->model = $model;
					$this->view->results = $connection->issue_search($xid, null, null, null, $searchYear, $searchissue);
					$this->view->importAction = "/AdminPublication/comicVineImportAction";
				}
				else {
					Session::addNegativeFeedback( Localized::ModelSearch( Publication::TABLE, Publication::name, "SEARCH_EMPTY" ). var_export($_POST, true));
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
					$points = $ep_model->allForType_code(Endpoint_Type::ComicVine);
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
