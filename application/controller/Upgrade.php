<?php

namespace controller;

use \Controller as Controller;
use \DataObject as DataObject;
use \Database as Database;
use \Model as Model;
use \http\Session as Session;
use \http\HttpGet as HttpGet;
use \Logger as Logger;
use \Auth as Auth;

use \utilities\ShellCommand as ShellCommand;
use \utilities\Git as Git;

use \model\user\Users as Users;
use \model\jobs\Job as Job;
use \model\jobs\Job_Type as Job_Type;
use \model\network\Endpoint as Endpoint;
use \model\network\Endpoint_Type as Endpoint_Type;
use \processor\Migration as Migration;


class Upgrade extends Controller
{
	function login()
	{
		if ( Auth::login() == true ) {
			if ( Session::get('user_account_type') == 'admin' ) {
				$this->view->render('/upgrade/index');
			}
			else {
				Session::addNegativeFeedback("Not authorized to perform upgrade actions");
				$this->view->render('/error/index');
			}
		}
		else {
			$this->view->model = Model::Named("Users");
			$this->view->render('/login/index');
		}
	}

	function index()
	{
		try {
			$dbversion = Database::DBVersion();
			$dbpatch = Database::DBPatchLevel();

			$user_model = Model::Named("Users");
			$adminUsers = $user_model->allForAccount_type(Users::AdministratorRole);
		}
		catch ( \PDOException $pdoe ) {
			$adminUsers = array();
		}

		if ( is_array($adminUsers) == false || count($adminUsers) == 0) {
			// no users, show the options
			$this->view->dbversion = $dbversion;
			$this->view->dbpatch = $dbpatch;
			$this->view->render('/upgrade/index');
		}
		else if (Session::get('user_logged_in') == false) {
			// not logged in, so show login panel
			$this->view->loginActionPath = "/Upgrade/login";
			$this->view->model = Model::Named("Users");
			$this->view->render('/login/index');
		}
		else if ( Session::get('user_account_type') != Users::AdministratorRole ) {
			Session::addNegativeFeedback("Not authorized to perform upgrade actions");
			$this->view->render('/error/index');
		}
		else {
			$this->view->dbversion = $dbversion;
			$this->view->dbpatch = $dbpatch;
			$this->view->render('/upgrade/index');
		}
	}

	function migrate()
	{
		try {
			$user_model = Model::Named("Users");
			$adminUsers = $user_model->allForAccount_type(Users::AdministratorRole);
		}
		catch ( \PDOException $pdoe ) {
			$adminUsers = array();
		}

		if ( is_array($adminUsers) == false || count($adminUsers) == 0) {
			// no users, just do it
			$processor = new Migration(currentVersionNumber());
			$processor->processData();

			$this->view->logs = $processor->migrationLogs();
			$this->view->render('/upgrade/completed');
		}
		else if (Session::get('user_logged_in') == false) {
			// not logged in, so show login panel
			$this->view->loginActionPath = "/Upgrade/login";
			$this->view->model = Model::Named("Users");
			$this->view->render('/login/index');
		}
		else if ( Session::get('user_account_type') != Users::AdministratorRole ) {
			Session::addNegativeFeedback("Not authorized to perform upgrade actions");
			$this->view->render('/error/index');
		}
		else {
			$processor = new Migration(currentVersionNumber());
			$processor->processData();

			$this->view->logs = $processor->migrationLogs();
			$this->view->render('/upgrade/completed');
		}
	}

	function upgradeEligibility()
	{
		$user_model = Model::Named("Users");
		$adminUsers = $user_model->allForAccount_type(Users::AdministratorRole);

		if ( is_array($adminUsers) == false || count($adminUsers) == 0) {
			$this->index();
		}
		else if (Session::get('user_logged_in') == false) {
			// not logged in, so show login panel
			$this->view->loginActionPath = "/Upgrade/login";
			$this->view->model = Model::Named("Users");
			$this->view->render('/login/index');
		}
		else if ( Session::get('user_account_type') != Users::AdministratorRole ) {
			Session::addNegativeFeedback("Not authorized to perform upgrade actions");
			$this->view->render('/error/index');
		}
		else {
			$this->view->model = Model::Named("Patch");
			$this->view->patches = Model::Named("Patch")->allObjects();
			$this->view->render('/upgrade/eligibility');
		}
	}

	function gitPull()
	{
		$user_model = Model::Named("Users");
		$adminUsers = $user_model->allForAccount_type(Users::AdministratorRole);

		if ( is_array($adminUsers) == false || count($adminUsers) == 0) {
			$this->index();
		}
		else if (Session::get('user_logged_in') == false) {
			// not logged in, so show login panel
			$this->view->loginActionPath = "/Upgrade/login";
			$this->view->model = Model::Named("Users");
			$this->view->render('/login/index');
		}
		else if ( Session::get('user_account_type') != Users::AdministratorRole ) {
			Session::addNegativeFeedback("Not authorized to perform upgrade actions");
			$this->view->render('/error/index');
		}
		else {
			$git = new \utilities\Git(SYSTEM_PATH);
			$this->view->git_results = $git->pull();
			if ( isset($this->view->git_results, $this->view->git_results['status']) ) {
				if ( $this->view->git_results['status'] == 0 ) {
					Logger::logWarning("Software updated. " . $this->view->git_results['stdout']);
				}
				else {
					Logger::logError("Software update failed. " . $this->view->git_results['stdout'] . PHP_EOL . $this->view->git_results['stderr']);
				}
				$this->view->render('/upgrade/git_results');
			}
			else {
				Session::addNegativeFeedback("Unexpected error");
				Logger::logError("An error occured. ");
				$this->view->render('/error/index');
			}
		}
	}

	function reviewDefaultData()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$this->view->setLocalizedViewTitle("Initialize-Restore-Data");
			$this->view->controllerAction = "reviewDefaultData";
			$this->view->render('/upgrade/defaultDataReview');
		}
	}

	function createRss()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Endpoint');
			$endpointType = Model::Named( "Endpoint_Type" ) ->RSS();
			$values = array(
				"endpointType" => $endpointType,
				Endpoint::name => "rss.binsearch.net (a.b.comics.dcp)",
				Endpoint::base_url => "http://rss.binsearch.net/rss.php?max=50&g=alt.binaries.comics.dcp",
				Endpoint::enabled => true,
				Endpoint::compressed => true
			);
			$model->createObject( $values );

			$this->view->setLocalizedViewTitle("Initialize-Restore-Data");
			$this->view->controllerAction = "reviewDefaultData";
			$this->view->render('/upgrade/defaultDataReview');
		}
	}

	function createJob($type = null, $endpointId = null)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			if ( is_null( $type ) == false ) {
				$job_type_model = Model::Named( "Job_Type" );
				$job_model = Model::Named( "Job" );
				$jobType = $job_type_model->objectForCode($type);
				if ( $jobType != false ) {
					switch ($type) {
						case Job_Type::character:
						case Job_Type::publication:
						case Job_Type::publisher:
						case Job_Type::series:
						case Job_Type::story_arc:
							$comicVineArray = Model::Named('Endpoint')->allForTypeCode(Endpoint_Type::ComicVine, true);
							if (is_array($comicVineArray) && count($comicVineArray) == 1 ) {
								$values = array(
									Job::type_code => $type,
									"endpoint" => $comicVineArray[0],
									Job::minute => "15",
									Job::hour => "1",
									Job::dayOfWeek => "*",
									Job::enabled => true,
									Job::one_shot => false
								);
								$job_model->createObject( $values );
							}
							break;

						case Job_Type::newznab_search:
							$values = array(
								Job::type_code => $type,
								Job::minute => "43",
								Job::hour => "3",
								Job::dayOfWeek => "*",
								Job::enabled => true,
								Job::one_shot => false
							);
							$job_model->createObject( $values );
							break;
						case Job_Type::previewsworld:
							$pwArray = Model::Named('Endpoint')->allForTypeCode(Endpoint_Type::PreviewsWorld, true);
							if (is_array($pwArray) && count($pwArray) == 1 ) {
								$values = array(
									Job::type_code => $type,
									"endpoint" => $pwArray[0],
									Job::minute => "15",
									Job::hour => "1",
									Job::dayOfWeek => "3",
									Job::enabled => true,
									Job::one_shot => false
								);
								$job_model->createObject( $values );
							}
							break;

						case Job_Type::reprocessor:
							$values = array(
								Job::type_code => $type,
								Job::minute => "0/20",
								Job::hour => "*",
								Job::dayOfWeek => "*",
								Job::enabled => true,
								Job::one_shot => false
							);
							$job_model->createObject( $values );
						case Job_Type::rss: break;
						case Job_Type::sabnzbd:
							$sabArray = Model::Named('Endpoint')->allForTypeCode(Endpoint_Type::SABnzbd, true);
							if (is_array($sabArray) && count($sabArray) == 1 ) {
								$values = array(
									Job::type_code => $type,
									"endpoint" => $sabArray[0],
									Job::minute => "*/12",
									Job::hour => "*",
									Job::dayOfWeek => "*",
									Job::enabled => true,
									Job::one_shot => false
								);
								$job_model->createObject( $values );
							}
						break;
						default:
							Session::addNegativeFeedback("Unknown type for job $type");
							$this->view->render('/error/index');
							break;
					}

					$this->view->setLocalizedViewTitle("Initialize-Restore-Data");
					$this->view->controllerAction = "reviewDefaultData";
					$this->view->render('/upgrade/defaultDataReview');
				}
				else {
					Session::addNegativeFeedback("Unexpected error, failed to find job $type");
					$this->view->render('/error/index');
				}
			}
			else {
				Session::addNegativeFeedback("Unexpected error, no job type specified");
				$this->view->render('/error/index');
			}
		}
	}

	function recalcStats()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			\SQL::raw( "update publication set media_count = (select count(*) from media "
				. " where media.publication_id = publication.id)" );
			\SQL::raw( "update series set pub_count = (select count(*) from publication "
				. " where publication.series_id = series.id)" );
			\SQL::raw( "update series set pub_available = (select count(*)
				from publication where publication.series_id = series.id AND publication.media_count > 0)" );
			\SQL::raw( "update story_arc set pub_count = "
				. "(select count(*) from story_arc_publication join publication "
				. " on story_arc_publication.publication_id = publication.id"
				. " where story_arc_publication.story_arc_id = story_arc.id)" );
			\SQL::raw( "update story_arc set pub_available = "
				. "(select count(*) from story_arc_publication join publication "
				. " on story_arc_publication.publication_id = publication.id"
				. " where story_arc_publication.story_arc_id = story_arc.id AND publication.media_count > 0)" );

			\SQL::raw( "update series set pub_cycle = (
				select (julianday(max(pub_date), 'unixepoch') - julianday(min(pub_date), 'unixepoch')) / count(*)
				from publication where publication.series_id = series.id)" );

			\SQL::raw( "update story_arc set pub_cycle = (
				select (julianday(max(publication.pub_date), 'unixepoch') - julianday(min(publication.pub_date), 'unixepoch')) / count(*)
				from story_arc_publication join publication on story_arc_publication.publication_id = publication.id
				where story_arc_publication.story_arc_id = story_arc.id)" );

			\SQL::raw( "update series set pub_active = (
				select (((julianday('now') - julianday(max(pub_date), 'unixepoch'))/365) < 1)
				from publication where publication.series_id = series.id)" );

			\SQL::raw( "update story_arc set pub_active = (
				select (((julianday('now') - julianday(max(pub_date), 'unixepoch'))/365) < 1)
				from story_arc_publication join publication on story_arc_publication.publication_id = publication.id
				where story_arc_publication.story_arc_id = story_arc.id)" );

			$this->view->setLocalizedViewTitle("Initialize-Restore-Data");
			$this->view->controllerAction = "reviewDefaultData";
			$this->view->render('/upgrade/defaultDataReview');
		}
	}
}


