<?php

namespace controller;

use \Controller as Controller;
use \DataObject as DataObject;
use \Model as Model;
use \Auth as Auth;
use \http\Session as Session;;
use \Logger as Logger;
use \Localized as Localized;
use controller\Admin as Admin;
use \model\user\Users as Users;
use \model\media\Publisher as Publisher;

/**
 * Class Admin
 * The index controller
 */
class AdminUsers extends Admin
{
	function index()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$user_model = Model::Named('Users');
			$this->view->model = $user_model;
			$this->view->users = $user_model->allObjects();
			$this->view->render( '/admin/userlist');
		}
	}

	function userlist()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$user_model = Model::Named('Users');
			$this->view->model = $user_model;
			$this->view->users = $user_model->allObjects();
			$this->view->render( '/admin/userlist');
		}
	}

	function editUser($oid = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$this->view->addStylesheet("select2.min.css");
			$this->view->addScript("select2.min.js");

			$user_model = Model::Named('Users');
			$this->view->model = Model::Named('Users');
			if ( $oid > 0 ) {
				$object = $model->objectForId($oid);
				if ( $object != false ) {
					$this->view->object = $object;
				}
			}
			$this->view->saveAction = "/AdminUsers/saveUser";
			$this->view->additionalAction = "/AdminUsers/additionalUser";

 			$this->view->userTypes = $user_model->userTypes();
			$this->view->render( '/edit/users');
		}
	}

	function saveUser($uid = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Users');
			$values = \http\HttpPost::getModelValue( 'users', null );
			if ( isset($values, $values['active']) ) {
				$values['active'] = boolValue($values['active'], true);
			}
			$success = true;

			if ( $uid > 0 ) {
				$userObj = $model->objectForId($uid);
				if ( $userObj != false ) {
					list($userObj, $errors) = $model->updateObject($userObj, $values);
					if ( is_array($errors) ) {
						$this->view->validationErrors = $errors;
						$this->editUser($uid);
					}
					else {
						Session::addPositiveFeedback(Localized::GlobalLabel( "Save Completed" ));
						$this->userlist();
					}
				}
				else {
					Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) . " " . $model->tableName() . " [$uid]" );
					$this->view->render('/error/index');
				}
			}
			else {
				list($obj, $errors) = $model->createObject($values);
				if ( is_array($errors) ) {
					$this->view->validationErrors = $errors;
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
					Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) . " " . $model->tableName() . " [$uid]" );
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
					Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) . " " . $model->tableName() . " [$uid]" );
				}
			}
			else {
				Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) );
			}
			$this->editUser($uid);
		}
	}
}
