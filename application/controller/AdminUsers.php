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
class AdminUsers extends Admin
{
	function userlist()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
// 			$this->view->viewTitle = "Users";
			$user_model = Model::Named('Users');
			$this->view->model = $user_model;
			$this->view->users = $user_model->allObjects();
			$this->view->render( '/admin/userlist');
		}
	}

	function editUser($uid = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$this->view->addStylesheet("select2.min.css");
			$this->view->addScript("select2.min.js");

			$user_model = Model::Named('Users');
			$this->view->model = Model::Named('Users');
			if ( $uid > 0 ) {
				$this->view->object = $user_model->objectForId($uid);
			}
			$this->view->saveAction = "/AdminUsers/saveUser";
			$this->view->additionalAction = "/AdminUsers/additionalUser";

 			$this->view->userTypes = $user_model->userTypes();
			$this->view->render( '/edit/users');

			Logger::loginfo("Editing user " . $uid, Session::get('user_name'), Session::get('user_id'));
		}
	}

	function saveUser($uid = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Users');
			$values = splitPOSTValues($_POST);
			if ( isset($values, $values['users']) ) {
				if ( isset($values['users']['active']) && $values['users']['active'] === "on" ) {
					$values['users']['active'] = true;
				}
				else {
					$values['users']['active'] = false;
				}
			}
			$success = true;

			if ( $uid > 0 ) {
				$userObj = $model->objectForId($uid);
				if ( $userObj != false ) {
					$errors = $model->updateObject($userObj, $values['users']);
					if ( is_array($errors) ) {
						Session::addNegativeFeedback( Localized::GlobalLabel("Validation Errors") );
						foreach ($errors as $attr => $errMsg ) {
							Session::addValidationFeedback($errMsg);
						}
						$this->editUser($uid);
					}
					else {
						Session::addPositiveFeedback(Localized::GlobalLabel( "Save Completed" ));
						$this->userlist();
					}
				}
				else {
					Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) );
					$this->view->render('/error/index');
				}
			}
			else {
				list($obj, $error) = $model->createObject($values['users']);
				if ( is_array($errors) ) {
					Session::addNegativeFeedback( Localized::GlobalLabel("Validation Errors") );
					foreach ($errors as $attr => $errMsg ) {
						Session::addValidationFeedback( $errMsg );
					}
					$this->editUser();
				}
				else {
					Session::addPositiveFeedback(Localized::GlobalLabel( "Save Completed" ));
					$this->userlist();
				}
			}
		}
	}

	function deleteUser($uid = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Users');
			if ( $uid > 0 ) {
				$userObj = $model->objectForId($uid);
				if ( $userObj != false ) {
					$errors = $model->deleteObject($userObj);
					if ( $errors == false ) {
						Session::addNegativeFeedback( Localized::GlobalLabel("Delete Failure") );
						$this->editUser($uid);
					}
					else {
						Session::addPositiveFeedback(Localized::GlobalLabel( "Delete Completed" ));
						$this->userlist();
					}
				}
				else {
					Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) );
					$this->userlist();
				}
			}
			else {
				Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) );
				$this->userlist();
			}
		}
	}

	function additionalUser($uid = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Users');
			if ( $uid > 0 ) {
				$userObj = $model->objectForId($uid);
				if ( $userObj != false ) {
					if ( isset($_POST, $_POST['generateAPI']) ) {
						$errors = $model->generateAPIHash($userObj);
						if ( $errors == false ) {
							Session::addNegativeFeedback( Localized::ModelValidation($model->tableName(), Users::api_hash, "GenerateError") );
						}
					}
					else {
						Session::addNegativeFeedback( "<pre>" . var_export($_POST, true) . "</pre>");
					}
				}
				else {
					Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) );
				}
			}
			else {
				Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) );
			}
			$this->editUser($uid);
		}
	}
}
