<?php

namespace controller;

use \Controller as Controller;
use \DataObject as DataObject;
use \Model as Model;
use \http\Session as Session;;
use \Auth as Auth;
use \Localized as Localized;
use \Logger as Logger;
use \Config as Config;

use \model\user\Users as Users;
use \model\network\Endpoint as Endpoint;
use \model\network\EndpointDBO as EndpointDBO;
use \model\network\Endpoint_TypeDBO as Endpoint_TypeDBO;
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
			$this->view->addStylesheet("select2.min.css");
			$this->view->addScript("select2.min.js");

			if ( $netId > 0 ) {
				$netObj = $model->objectForId($netId);
				if ( $netObj != false ) {
					$this->view->setLocalizedViewTitle("EditRecord");
					$this->view->saveAction = "netconfig/save";
					$this->view->testAction = "netconfig/testConnection";
					$this->view->clearErrorsAction = "netconfig/clearErrors";
					$this->view->object = $netObj;
					$this->view->render( '/edit/endpoint' );
				}
				else {
					Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) . " " . $model->tableName() . " [$netId]");
					Logger::logError("Invalid endpoint requested " . $netId);
					$this->view->render('/error/index');
				}
			}
			else {
				$this->view->setLocalizedViewTitle("NewRecord");
				$this->view->saveAction = "netconfig/edit_new";
				$this->view->testAction = "netconfig/testConnection";
				$this->view->clearErrorsAction = "netconfig/clearErrors";
				$this->view->render( '/edit/endpoint_select_type' );
			}
		}
	}

	function edit_new($typeCode = null)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$this->view->addStylesheet("select2.min.css");
			$this->view->addScript("select2.min.js");

			$model = Model::Named('Endpoint_Type');
			$values = splitPOSTValues($_POST);
			$type = (is_null($typeCode) ? null : $model->objectForId($typeCode));
			if ( isset($values, $values['endpoint'], $values['endpoint']['type_code'])) {
				$type = $model->objectForId($values['endpoint']['type_code']);
			}

			if ( is_null($type) == false) {
				if ( $type instanceof Endpoint_TypeDBO ) {
					$this->view->setLocalizedViewTitle("NewRecord");
					$this->view->saveAction = "netconfig/save";
					$this->view->testAction = "netconfig/testConnection";
					$this->view->endpoint_type = $type;
					$this->view->model = Model::Named('Endpoint');

					$this->view->render( '/edit/endpoint' );
					return;
				}
				else {
					Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) . " " . $model->tableName() . " [$typeCode]");
				}
			}
			else {
				Session::addNegativeFeedback(Localized::GlobalLabel( "No form values" ));
			}

			$this->view->setLocalizedViewTitle("NewRecord");
			$this->view->saveAction = "netconfig/edit_new";
			$this->view->testAction = "netconfig/testConnection";
			$this->view->clearErrorsAction = "netconfig/clearErrors";
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
					list($netObj, $errors) = $model->updateObject($netObj, $values['endpoint']);
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
					Session::addNegativeFeedback( Localized::GlobalLabel( "Failed to find request record" ) . " " . $model->tableName() . " [$netId]" );
					$this->view->render('/error/index');
				}
			}
			else {
				list($obj, $error) = $model->createObject($values['endpoint']);
				if ( is_array($error) ) {
					Session::addNegativeFeedback( Localized::GlobalLabel("Validation Errors") );
					foreach ($error as $attr => $errMsg ) {
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
					Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) . " " . $model->tableName() . " [$netId]" );
				}
			}
			else {
				Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) );
			}
		}
		$this->index();
	}

	function testConnection($netId = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Endpoint');
			if ( $netId > 0 ) {
				$object = $model->objectForId($netId);
				if ( $object instanceof EndpointDBO ) {
					$connection = $object->endpointConnector();
					list( $success, $message ) = $connection->testConnnector();
					if ( $success == false ) {
						$message = Localized::GlobalLabel( "Failed" ) . PHP_EOL . $message;
					}
					else {
						$message = Localized::GlobalLabel( "Success" ) . PHP_EOL . $message;
					}
					echo $message;
				}
				else {
					Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) . " " . $model->tableName() . " [$netId]" );
					echo "could not find $netId";
				}
			}
			else {
				echo "No id";
				Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) );
			}
		}
	}

	function clearErrors($netId = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Endpoint');
			if ( $netId > 0 ) {
				$object = $model->objectForId($netId);
				if ( $object instanceof EndpointDBO ) {
					$object->clearErrorCount();
					sleep(2);
					header('location: ' . Config::Web('/Netconfig/edit/' . $netId));
				}
				else {
					Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) . " " . $model->tableName() . " [$netId]" );
				}
			}
			else {
				Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) );
			}
		}
	}

}
