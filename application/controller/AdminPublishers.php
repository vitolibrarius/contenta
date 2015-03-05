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

use endpoints\ComicVineConnector as ComicVineConnector;
use processor\ComicVineImporter as ComicVineImporter;

use controller\Admin as Admin;

use model\Users as Users;
use model\Endpoint as Endpoint;
use model\Endpoint_Type as Endpoint_Type;
use model\Publisher as Publisher;

/**
 * Class Admin
 * The index controller
 */
class AdminPublishers extends Admin
{
	function publisherlist()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Publisher');
			$this->view->model = $model;
			$this->view->list = $model->allObjects();
			$this->view->editAction = "/AdminPublishers/editPublisher";
			$this->view->deleteAction = "/AdminPublishers/deletePublisher";
			$this->view->render( '/publishers/index');
		}
	}

	function deletePublisher($pubId = null)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Publisher');
			if ( $pubId > 0 ) {
				$object = $model->objectForId($pubId);
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
			$this->publisherlist();
		}
	}

	function editPublisher($pubId = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$this->view->addStylesheet("select2.css");
			$this->view->addScript("select2.min.js");

			$model = Model::Named('Publisher');
			$this->view->model = $model;
			if ( $pubId > 0 ) {
				$this->view->object = $model->objectForId($pubId);
			}
			$this->view->saveAction = "/AdminPublishers/savePublisher";
			$this->view->additionalAction = "/AdminPublishers/updatedAdditional";

			$this->view->render( '/edit/publisher');

			Logger::loginfo("Editing publisher " . $pubId, Session::get('user_name'), Session::get('user_id'));
		}
	}

	function savePublisher($pubId = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Publisher');
			$values = splitPOSTValues($_POST);

			if ( $pubId > 0 ) {
				$object = $model->objectForId($pubId);
				if ( $object != false ) {
					$errors = $model->updateObject($object, $values[$model->tableName()]);
					if ( is_array($errors) ) {
						Session::addNegativeFeedback( Localized::GlobalLabel("Validation Errors") );
						foreach ($errors as $attr => $errMsg ) {
							Session::addValidationFeedback($errMsg);
						}
						$this->editPublisher($pubId);
					}
					else {
						Session::addPositiveFeedback(Localized::GlobalLabel( "Save Completed" ));
						$this->publisherlist();
					}
				}
				else {
					Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) );
					$this->view->render('/error/index');
				}
			}
			else {
				$errors = $model->createObject($values[$model->tableName()]);
				if ( is_array($errors) ) {
					Session::addNegativeFeedback( Localized::GlobalLabel("Validation Errors") );
					foreach ($errors as $attr => $errMsg ) {
						Session::addValidationFeedback( $errMsg );
					}
					$this->editPublisher();
				}
				else {
					Session::addPositiveFeedback(Localized::GlobalLabel( "Save Completed" ));
					$this->publisherlist();
				}
			}
		}
	}

	function updatedAdditional($pubId = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Publisher');
			$values = splitPOSTValues($_POST);

			if ( $pubId > 0 ) {
				$object = $model->objectForId($pubId);
				$endpoint = $object->externalEndpoint();
				if ( $endpoint != false && isset($object->xid) ) {
					$importer = new ComicVineImporter( $model->tableName() . "_" .$object->xid );
					if ( $importer->endpoint() == false ) {
						$importer->setEndpoint($endpoint);
					}

					$importer->importPublisherValues( null, $object->xid, null);
					$importer->processData();
					$this->editPublisher($pubId);
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

	function comicVineSearch($pubId = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$ep_model = Model::Named('Endpoint');
			$points = $ep_model->allForTypeCode(Endpoint_Type::ComicVine);
			if ( $points == false || count($points) == 0) {
				Session::addNegativeFeedback(Localized::GlobalLabel( "PLEASE_ADD_ENDPOINT" ) );
				header('location: ' . Config::Web('/netconfig/index'));
			}
			else {
				$model = Model::Named('Publisher');
				if ( $pubId > 0 ) {
					$object = $model->objectForId($pubId);
					if ( $object != false ) {
						$this->view->object = $object;
					}
				}
				$this->view->searchAction = "/AdminPublishers/comicVineSearchAction";
				$this->view->endpoints = $points;
				$this->view->ep_model = $ep_model;
				$this->view->model = $model;
				$this->view->render('/import/comicvine_publisher');
			}
		}
	}

	function comicVineSearchAction($pubId = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$ep_model = Model::Named('Endpoint');
			$points = $ep_model->allForTypeCode(Endpoint_Type::ComicVine);
			if ( $points == false || count($points) == 0) {
				Session::addNegativeFeedback(Localized::GlobalLabel( "PLEASE_ADD_ENDPOINT" ) );
			}
			else {
				$model = Model::Named('Publisher');
				$values = splitPOSTValues($_POST);
				if ( isset($values, $values[Publisher::TABLE], $values[Publisher::TABLE][Publisher::name]) ) {
					$pubName = $values[Publisher::TABLE][Publisher::name];
					$epoint = $points[0];
					$connection = new ComicVineConnector($epoint);

					$this->view->model = $model;
					$this->view->results = $connection->queryForPublisherName( $pubName );
					$this->view->importAction = "/AdminPublishers/comicVineImportAction";
				}
				else {
					Session::addNegativeFeedback( Localized::ModelSearch( Publisher::TABLE, Publisher::name, "SEARCH_EMPTY" ));
				}
			}

			$this->view->render('/import/comicvine_publisher_results', true);
		}
	}

	function comicVineImportAction($xid = null)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			if ( isset($xid) && is_null($xid) == false) {
				$importer = new ComicVineImporter( Publisher::TABLE . "_" .$xid );
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
					$importer->importPublisherValues( null, $xid, null);
					$importer->processData();
				}
				$this->publisherlist();
			}
		}
	}
}
