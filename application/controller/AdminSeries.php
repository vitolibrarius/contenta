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
use \model\media\Series as Series;
use \model\media\Series_Alias as Series_Alias;
use \model\media\Series_Character as Series_Character;

/**
 * Class Admin
 * The index controller
 */
class AdminSeries extends Admin
{
	function index()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$this->view->addStylesheet("select2.min.css");
			$this->view->addScript("select2.min.js");

			$parameters = Session::pageParameters( $this, "index" );
			$this->view->params = $parameters;

			$model = Model::Named('Series');
			$this->view->model = $model;
			$this->view->render( '/admin/seriesIndex');
		}
	}

	function searchSeries($pageNum = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$parameters = Session::pageParameters( $this, "index" );
			$parameters->setPageSize(12);
			list( $hasNewValues, $query) = $parameters->updateParametersFromGET( array(
				'searchPublisher', 'searchName', 'searchWanted', 'searchYear' )
			);

			$model = Model::Named('Series');
			$qualifiers = array();
			if ( isset($query['searchName']) && strlen($query['searchName']) > 0) {
				$qualifiers[] = Qualifier::Like( Series::search_name, normalizeSearchString($query['searchName']));
			}
			if ( isset($query['searchYear']) && strlen($query['searchYear']) == 4 ) {
				$qualifiers[] = Qualifier::Equals( Series::start_year, $query['searchYear'] );
			}
			if ( isset($query['searchPublisher']) && intval($query['searchPublisher']) > 0 ) {
				$qualifiers[] = Qualifier::Equals( Series::publisher_id, $query['searchPublisher'] );
			}
			if ( isset($query['searchWanted']) && $query['searchWanted'] == '1') {
				$qualifiers[] = Qualifier::Equals( Series::pub_wanted, 1 );
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
			$this->view->toggleWantedAction = "/AdminSeries/toggleWantedSeries";
			$this->view->editAction = "/AdminSeries/editSeries";
			$this->view->wantedAction = "/AdminSeries/toggleWantedSeries";
			$this->view->render( '/admin/seriesCards', true);
		}
	}

	function deleteSeries($oid = null)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Series');
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
			header('location: ' . Config::Web('/AdminSeries/index' ));
		}
	}

	function editSeries($oid = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$this->view->addStylesheet("select2.min.css");
			$this->view->addScript("select2.min.js");

			$model = Model::Named('Series');
			$this->view->model = $model;
			if ( $oid > 0 ) {
				$series = $model->objectForId($oid);
				if ( $series != false ) {
					$this->view->object = $series;
				}
			}
			$this->view->saveAction = "/AdminSeries/saveSeries";
			$this->view->additionalAction = "/AdminSeries/updatedAdditional";
			$this->view->editPublicationAction = "/AdminPublication/editPublication";
			$this->view->editCharacterAction = "/AdminCharacters/editCharacter";
			$this->view->editStoryArcAction = "/AdminStoryArcs/editStoryArc";

			$this->view->render( '/edit/series');
		}
	}

	function toggleWantedSeries($oid = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			if ( $oid > 0 ) {
				$model = Model::Named('Series');
				$object = $model->objectForId($oid);
				if ( $object != false ) {
					$oldIsWanted = $object->isWanted();
					$newIsWanted = ($object->isWanted() ? MODEL::TERTIARY_FALSE : MODEL::TERTIARY_TRUE);
					$values[Series::pub_wanted] = $newIsWanted;
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
							echo json_encode(array("toggled_on" => true) );
						}
						else {
							echo json_encode(array("toggled_on" => false) );
						}
					}
				}
			}
		}
	}

	function saveSeries($oid = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Series');
			$values = splitPOSTValues($_POST);
			if ( isset($values[$model->tableName()], $values[$model->tableName()][Series::pub_wanted]) == false ) {
				$values[$model->tableName()][Series::pub_wanted] = Model::TERTIARY_FALSE;
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
						$this->editSeries($oid);
					}
					else {
						if ( $object->isWanted() && $oldIsWanted == false ) {
							// now a wanted series, ensure the publications are updated
							$endpoint = $object->externalEndpoint();
							if ( $endpoint != false ) {
								$importer = new ComicVineImporter( $model->tableName() . "_" .$object->xid );
								$importer->setEndpoint($endpoint);
								$importer->refreshPublicationsForObject( $object );
								$importer->daemonizeProcess();
							}
						}
						Session::addPositiveFeedback(Localized::GlobalLabel( "Save Completed" ));
						header('location: ' . Config::Web('/AdminSeries/editSeries/' . $oid));
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
					$this->editSeries();
				}
				else {
					Session::addPositiveFeedback(Localized::GlobalLabel( "Save Completed" ));
					header('location: ' . Config::Web('/AdminSeries/index' ));
				}
			}
		}
	}

	function updatedAdditional($oid = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Series');
			$values = splitPOSTValues($_POST);

			if ( $oid > 0 ) {
				$object = $model->objectForId($oid);
				$endpoint = $object->externalEndpoint();
				if ( $endpoint != false ) {
					$importer = new ComicVineImporter( $model->tableName() . "_" .$object->xid );
					if ( $importer->endpoint() == false ) {
						$importer->setEndpoint($endpoint);
					}

					$importer->enqueue_series( array( "xid" => $object->xid), true, true );
					$importer->daemonizeProcess();
					sleep(2);
					header('location: ' . Config::Web('/AdminSeries/editSeries/' . $oid));
				}
				else {
					Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find requested endpoint" ) . " " . $model->tableName() . " [$oid]" );
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
				$model = Model::Named('Series');
				if ( $oid > 0 ) {
					$object = $model->objectForId($oid);
					if ( $object != false ) {
						$this->view->object = $object;
					}
				}
				$this->view->searchAction = "/AdminSeries/comicVineSearchAction";
				$this->view->endpoints = $points;
				$this->view->ep_model = $ep_model;
				$this->view->model = $model;
				$this->view->render('/import/comicvine_series');
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
				$model = Model::Named('Series');
				$values = splitPOSTValues($_POST);
				if ( isset($values, $values[Series::TABLE], $values[Series::TABLE][Series::name]) ) {
					$name = $values[Series::TABLE][Series::name];
					$year = 0;
					if ( isset($values[Series::TABLE][Series::start_year]) ) {
						$year = $values[Series::TABLE][Series::start_year];
					}
					$epoint = $points[0];
					$connection = new ComicVineConnector($epoint);

					$this->view->endpoint = $epoint;
					$this->view->pub_model = Model::Named("Publisher");
					$this->view->model = $model;
					$this->view->results = $connection->series_searchFilteredForYear( $name, $year );
					$this->view->importAction = "/AdminSeries/comicVineImportAction";
				}
				else {
					Session::addNegativeFeedback( Localized::ModelSearch( Series::TABLE, Series::name, "SEARCH_EMPTY" ));
				}
			}

			$this->view->render('/import/comicvine_series_results', true);
		}
	}

	function comicVineImportAction($xid = null, $name = null)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			if ( isset($xid) && is_null($xid) == false) {
				if ( isset($name) == false || strlen($name) == 0) {
					$name = 'Unknown ' . $xid;
				}
				$existing = Model::Named('Series')->findExternalOrCreate(null, $name, null, null, $xid, Endpoint_Type::ComicVine, null, null );
				if ( $existing != false ) {
					Session::addPositiveFeedback( Localized::ModelLabel( 'Series', "SUCCESS_CREATE" ) );
				}

				$importer = new ComicVineImporter( Series::TABLE . "_" .$xid );
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
					$importer->enqueue_series( array( "xid" => $xid, "name" => $name ), true, true );
					$importer->daemonizeProcess();
				}

				header('location: ' . Config::Web('/AdminSeries/editSeries/' . $existing->id));
			}
		}
	}
}
