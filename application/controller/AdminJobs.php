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

use model\Users as Users;
use model\Job as Job;
use model\Job_Type as Job_Type;
use model\Publisher as Publisher;
use model\Character as Character;
use model\Character_Alias as Character_Alias;
use model\Series as Series;

use \SQL as SQL;
use db\Qualifier as Qualifier;

/**
 * Class job
 */
class AdminJobs extends Admin
{
	function index()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
		$table_fields = \SQL::pragma_TableInfo(Job::TABLE);
		if ( isset($table_fields[ Job::elapsed ]) == false ) {
			\SQL::raw("ALTER TABLE " . Job::TABLE . " ADD COLUMN " . Job::elapsed . " integer", null,"Adding the elapsed column to Job");
		}

			$model = Model::Named('Job');
			$this->view->model = $model;
			$this->view->objects = $model->allObjects();
			$this->view->render( '/jobs/index' );
		}
	}

	function execute($jobId = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Job');
			if ( $jobId > 0 ) {
				$obj = $model->objectForId($jobId);
				if ( $obj != false ) {
					$pid = DaemonizeJob( $obj );
					Session::addPositiveFeedback(Localized::GlobalLabel( "Running process id " ) . $pid);
					sleep(2);
					header('location: ' . Config::Web('/AdminJobs/index'));
				}
				else {
					Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ));
					Logger::logError("Invalid job requested " . $jobId);
					$this->view->render('/error/index');
				}
			}
			else {
				Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ));
				Logger::logError("Invalid job requested " . $jobId);
				$this->view->render('/error/index');
			}
		}
	}

	function runningIndex()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$this->view->render( '/jobs/runningIndex' );
		}
	}

	function ajax_runningTable()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Job_Running');
			$model->clearFinishedProcesses();

			$this->view->model = $model;
			$this->view->objects = $model->allObjects();
			$this->view->render( '/jobs/running_table', true );
		}
	}

	function edit($jobId = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Job');
			$this->view->model = $model;
			$this->view->addStylesheet("select2.css");
			$this->view->addScript("select2.min.js");

			if ( $jobId > 0 ) {
				$obj = $model->objectForId($jobId);
				if ( $obj != false ) {
					$this->view->setLocalizedViewTitle("EditRecord");
					$this->view->saveAction = "AdminJobs/save";
					$this->view->object = $obj;
					$this->view->render( '/edit/job' );
				}
				else {
					Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ));
					Logger::logError("Invalid job requested " . $jobId);
					$this->view->render('/error/index');
				}
			}
			else {
				$this->view->setLocalizedViewTitle("NewRecord");
				$this->view->saveAction = "AdminJobs/edit_new";
				$this->view->render( '/edit/job_select_type' );
			}
		}
	}

	function edit_new()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$this->view->addStylesheet("select2.css");
			$this->view->addScript("select2.min.js");

			$values = splitPOSTValues($_POST);
			if ( isset($values, $values['job'], $values['job']['type_id']) ) {
				$model = Model::Named('job_Type');
				$type = $model->objectForId($values['job']['type_id']);
				if ( is_a($type, "model\\Job_TypeDBO" ) ) {
					$this->view->setLocalizedViewTitle("NewRecord");
					$this->view->saveAction = "AdminJobs/save";
					$this->view->job_type = $type;
					$this->view->model = Model::Named('Job');

					$this->view->render( '/edit/job' );
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
			$this->view->saveAction = "AdminJobs/edit_new";
			$this->view->model = Model::Named('Job');
			$this->view->render( '/edit/job_select_type' );
		}
	}

	function save($jobId = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Job');
			$values = splitPOSTValues($_POST);
			$success = true;

			if ( $jobId > 0 ) {
				$obj = $model->objectForId($jobId);
				if ( $obj != false ) {
					$errors = $model->updateObject($obj, $values['job']);
					if ( is_array($errors) ) {
						Session::addNegativeFeedback( Localized::GlobalLabel("Validation Errors") );
						foreach ($errors as $attr => $errMsg ) {
							Session::addValidationFeedback($errMsg);
						}
						$this->edit($jobId);
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
				$errors = $model->createObject($values['job']);
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

	function delete($jobId = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('job');
			if ( $jobId > 0 ) {
				$object = $model->objectForId($jobId);
				if ( $object != false ) {
					$errors = $model->deleteObject($object);
					if ( $errors == false ) {
						Session::addNegativeFeedback( Localized::GlobalLabel("Delete Failure") );
						$this->edit($jobId);
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
