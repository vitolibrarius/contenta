<?php

namespace controller;

use \Controller as Controller;
use \DataObject as DataObject;
use \Model as Model;
use \Auth as Auth;
use \Session as Session;
use \Logger as Logger;
use \Localized as Localized;
use controller\Admin as Admin;
use model\Users as Users;
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
			$this->view->additionalAction = null;

			$this->view->render( '/edit/publisher');

			Logger::loginfo("Editing publisher " . $pubId, Session::get('user_name'), Session::get('user_id'));
		}
	}

	function savePublisher($pubId = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Publisher');
			$values = splitPOSTValues($_POST);
			$success = true;

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
}
