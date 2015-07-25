<?php

namespace controller;

use \Controller as Controller;
use \DataObject as DataObject;
use \Model as Model;
use \Session as Session;
use \Auth as Auth;
use \Localized as Localized;
use \Logger as Logger;
use model\Users as Users;
use model\Endpoint as Endpoint;

/**
 * Class Endpoint
 */
class Netconfig extends Controller
{
	function index()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Endpoint');
			$this->view->model = $model;
			$this->view->endpoints = $model->allObjects();
			$this->view->render( '/netconfig/index' );
		}
	}

	function edit($netId = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Endpoint');
			$this->view->model = $model;
			$this->view->addStylesheet("select2.css");
			$this->view->addScript("select2.min.js");

			if ( $netId > 0 ) {
				$netObj = $model->objectForId($netId);
				if ( $netObj != false ) {
					$this->view->setLocalizedViewTitle("EditRecord");
					$this->view->saveAction = "netconfig/save";
					$this->view->object = $netObj;
					$this->view->render( '/edit/endpoint' );
				}
				else {
					Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ));
					Logger::logError("Invalid endpoint requested " . $netId);
					$this->view->render('/error/index');
				}
			}
			else {
				$this->view->setLocalizedViewTitle("NewRecord");
				$this->view->saveAction = "netconfig/edit_new";
				$this->view->render( '/edit/endpoint_select_type' );
			}
		}
	}

	function edit_new()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$this->view->addStylesheet("select2.css");
			$this->view->addScript("select2.min.js");

			$values = splitPOSTValues($_POST);
			if ( isset($values, $values['endpoint'], $values['endpoint']['type_id']) ) {
				$model = Model::Named('Endpoint_Type');
				$type = $model->objectForId($values['endpoint']['type_id']);
				if ( is_a($type, "model\\Endpoint_TypeDBO" ) ) {
					$this->view->setLocalizedViewTitle("NewRecord");
					$this->view->saveAction = "netconfig/save";
					$this->view->endpoint_type = $type;
					$this->view->model = Model::Named('Endpoint');

					$this->view->render( '/edit/endpoint' );
					return;
				}
				else {
					Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ));
				}
			}
			else {
				Session::addNegativeFeedback(Localized::GlobalLabel( "No form values" ));
			}

			$this->view->setLocalizedViewTitle("NewRecord");
			$this->view->saveAction = "netconfig/edit_new";
			$this->view->model = Model::Named('Endpoint');
			$this->view->render( '/edit/endpoint_select_type' );
		}
	}

	function save($netId = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Endpoint');
			$values = splitPOSTValues($_POST);
			$success = true;

			if ( $netId > 0 ) {
				$netObj = $model->objectForId($netId);
				if ( $netObj != false ) {
					$errors = $model->updateObject($netObj, $values['endpoint']);
					if ( is_array($errors) ) {
						Session::addNegativeFeedback( Localized::GlobalLabel("Validation Errors") );
						foreach ($errors as $attr => $errMsg ) {
							Session::addValidationFeedback($errMsg);
						}
						$this->edit($netId);
					}
					else {
						Session::addPositiveFeedback(Localized::GlobalLabel( "Save Completed" ));
						$this->index();
					}
				}
				else {
					Session::addNegativeFeedback( Localized::GlobalLabel( "Failed to find request record" ) );
					$this->view->render('/error/index');
				}
			}
			else {
				list($obj, $error) = $model->createObject($values['endpoint']);
				if ( is_array($errors) ) {
					Session::addNegativeFeedback( Localized::GlobalLabel("Validation Errors") );
					foreach ($errors as $attr => $errMsg ) {
						Session::addValidationFeedback( $errMsg );
					}
					$this->edit_new();
				}
				else {
					Session::addPositiveFeedback(Localized::GlobalLabel( "Save Completed" ));
					$this->index();
				}
			}
		}
	}

	function delete($netId = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Endpoint');
			if ( $netId > 0 ) {
				$object = $model->objectForId($netId);
				if ( $object != false ) {
					$errors = $model->deleteObject($object);
					if ( $errors == false ) {
						Session::addNegativeFeedback( Localized::GlobalLabel("Delete Failure") );
						$this->edit($netId);
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
		}
		$this->index();
	}

}
