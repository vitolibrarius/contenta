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

use connectors\ComicVineConnector as ComicVineConnector;
use processor\ComicVineImporter as ComicVineImporter;

use controller\Admin as Admin;

use \model\user\Users as Users;
use \model\network\Endpoint as Endpoint;
use \model\network\Endpoint_Type as Endpoint_Type;
use \model\media\Publisher as Publisher;
use \model\media\Character as Character;
use \model\media\Character_Alias as Character_Alias;

use \SQL as SQL;
use db\Qualifier as Qualifier;

/**
 * Class Admin
 * The index controller
 */
class AdminCharacters extends Admin
{
	function index()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$this->view->addStylesheet("select2.min.css");
			$this->view->addScript("select2.min.js");

			$parameters = Session::pageParameters( $this, "index" );
			$this->view->params = $parameters;
			$model = Model::Named('Character');
			$this->view->model = $model;
			$this->view->render( '/admin/characterIndex');
		}
	}

	function searchCharacters($pageNum = null)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$parameters = Session::pageParameters( $this, "index" );
			list( $hasNewValues, $query) = $parameters->updateParametersFromGET( array( 'publisher_id', 'name', 'popularity' ));

			$model = Model::Named('Character');
			$results = $model->searchQuery( $hasNewValues, $query, $pageNum, $parameters );

			$this->view->params = $parameters;
			$this->view->model = $model;
			$this->view->listArray = $results;
			$this->view->editAction = "/AdminCharacters/editCharacter";
			$this->view->deleteAction = "/AdminCharacters/deleteCharacter";
			$this->view->render( '/admin/characterCards', true);
		}
	}

	function deleteCharacter($oid = null)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Character');
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
					Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) . " " . $model->tableName() . " [$oid]");
				}
			}
			else {
				Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) );
			}
			header('location: ' . Config::Web('/AdminCharacters/index' ));
		}
	}

	function editCharacter($oid = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$this->view->addStylesheet("select2.min.css");
			$this->view->addScript("select2.min.js");

			$model = Model::Named('Character');
			$this->view->model = $model;
			if ( $oid > 0 ) {
				$object = $model->objectForId($oid);
				if ( $object != false ) {
					$this->view->object = $object;
				}
			}
			$this->view->saveAction = "/AdminCharacters/saveCharacter";
			$this->view->additionalAction = "/AdminCharacters/updatedAdditional";
			$this->view->editPublicationAction = "/AdminPublication/editPublication";
			$this->view->editSeriesAction = "/AdminSeries/editSeries";
			$this->view->editStoryArcAction = "/AdminStoryArcs/editStoryArc";

			$this->view->render( '/edit/character');
		}
	}

	function saveCharacter($oid = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Character');
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
						header('location: ' . Config::Web('/AdminCharacters/editCharacter', $oid ));
					}
					else {
						Session::addPositiveFeedback(Localized::GlobalLabel( "Save Completed" ));
						header('location: ' . Config::Web('/AdminCharacters/index' ));
					}
				}
				else {
					Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) . " " . $model->tableName() . " [$oid]");
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
					header('location: ' . Config::Web('/AdminCharacters/editCharacter' ));
				}
				else {
					Session::addPositiveFeedback(Localized::GlobalLabel( "Save Completed" ));
					header('location: ' . Config::Web('/AdminCharacters/index' ));
				}
			}
		}
	}

	function updatedAdditional($oid = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Character');
			$values = splitPOSTValues($_POST);

			if ( $oid > 0 ) {
				$object = $model->objectForId($oid);
				$endpoint = $object->externalEndpoint();
				if ( $endpoint != false ) {
					$importer = new ComicVineImporter( $model->tableName() . "_" .$object->xid );
					if ( $importer->endpoint() == false ) {
						$importer->setEndpoint($endpoint);
					}

					$importer->enqueue_character( array( "xid" => $object->xid), true, true );
					$importer->daemonizeProcess();
					header('location: ' . Config::Web('/AdminCharacters/editCharacter', $oid ));
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
			$points = $ep_model->allForTypeCode(Endpoint_Type::ComicVine, true);
			if ( $points == false || count($points) == 0) {
				Session::addNegativeFeedback(Localized::GlobalLabel( "PLEASE_ADD_ENDPOINT" ) . Endpoint_Type::ComicVine);
				header('location: ' . Config::Web('/netconfig/index'));
			}
			else {
				$model = Model::Named('Character');
				if ( $oid > 0 ) {
					$object = $model->objectForId($oid);
					if ( $object != false ) {
						$this->view->object = $object;
					}
				}
				$this->view->searchAction = "/AdminCharacters/comicVineSearchAction";
				$this->view->endpoints = $points;
				$this->view->ep_model = $ep_model;
				$this->view->model = $model;
				$this->view->render('/import/comicvine_character');
			}
		}
	}

	function comicVineSearchAction($oid = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$ep_model = Model::Named('Endpoint');
			$points = $ep_model->allForTypeCode(Endpoint_Type::ComicVine, true);
			if ( $points == false || count($points) == 0) {
				Session::addNegativeFeedback(Localized::GlobalLabel( "PLEASE_ADD_ENDPOINT" ) );
			}
			else {
				$model = Model::Named('Character');
				$values = splitPOSTValues($_POST);
				if ( isset($values, $values[Character::TABLE], $values[Character::TABLE][Character::name]) ) {
					$name = $values[Character::TABLE][Character::name];
					$epoint = $points[0];
					$connection = new ComicVineConnector($epoint);

					$this->view->endpoint = $epoint;
					$this->view->pub_model = Model::Named("Publisher");
					$this->view->model = $model;
					$this->view->results = $connection->character_search( null, $name );
					$this->view->importAction = "/AdminCharacters/comicVineImportAction";
				}
				else {
					Session::addNegativeFeedback( Localized::ModelSearch( Character::TABLE, Character::name, "SEARCH_EMPTY" ));
				}
			}

			$this->view->render('/import/comicvine_character_results', true);
		}
	}

	function comicVineImportAction($xid = null, $name = null)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			if ( isset($xid) && is_null($xid) == false) {
				if ( isset($name) == false || strlen($name) == 0) {
					$name = 'Unknown ' . $xid;
				}
				$existing = Model::Named('Character')->findExternalOrCreate(null, $name, null, null, null, null, $xid, Endpoint_Type::ComicVine, null );
				if ( $existing != false ) {
					Session::addPositiveFeedback( Localized::ModelLabel( 'character', "SUCCESS_CREATE" ) );
				}

				$importer = new ComicVineImporter( Character::TABLE . "_" .$xid );
				if ( $importer->endpoint() == false ) {
					$ep_model = Model::Named('Endpoint');
					$points = $ep_model->allForTypeCode(Endpoint_Type::ComicVine, true);
					if ( $points == false || count($points) == 0) {
						Session::addNegativeFeedback(Localized::GlobalLabel( "PLEASE_ADD_ENDPOINT" ) );
					}
					else {
						$importer->setEndpoint($points[0]);
					}
				}

				if ( $importer->endpoint() != false ) {
					$importer->enqueue_character( array( "xid" => $xid, "name" => $name), true, true );
					$importer->daemonizeProcess();
				}

				header('location: ' . Config::Web('/AdminCharacters/index' ));
			}
		}
	}
}
