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
use \Processor as Processor;

use connectors\ComicVineConnector as ComicVineConnector;
use processor\ComicVineImporter as ComicVineImporter;
use processor\UploadImport as UploadImport;
use processor\ImportManager as ImportManager;

use controller\Admin as Admin;

use model\Users as Users;
use model\Endpoint as Endpoint;
use model\Endpoint_Type as Endpoint_Type;
use model\Publisher as Publisher;
use model\Character as Character;
use model\Character_Alias as Character_Alias;

/**
 * Class Admin
 * The index controller
 */
class AdminUploadRepair extends Admin
{
	function index($chunkNum = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$processor = Processor::Named("ImportManager", 0);

			$model = Model::Named("Job_Running");
			$model->clearFinishedProcesses();

			$idx = $processor->correctChunkIndex($chunkNum);
			$this->view->job_model = $model;
			$this->view->active = $processor->chunk($idx);;
			$this->view->pageCurrent = $idx;
			$this->view->pageCount = $processor->totalChunks();
			$this->view->render( '/upload/processing' );
		}
	}

	function firstThumbnail($processKey = null)
	{
		if (Auth::handleLogin() && Auth::requireRole('admin')) {
			$image = null;
			$mimeType = null;
			if ( is_null($processKey) == false ) {
				$processor = Processor::Named("ImportManager", 0);
				$wrapper = $processor->fileWrapper($processKey);
				if ( $wrapper != null ) {
					$filename = $wrapper->firstImageThumbnailName();
					if ( is_null($filename) == false ) {
						$mimeType = 'image/' . file_ext($filename);
						$image = $wrapper->wrappedThumbnailForName($filename, 100, 100);
					}
				}
			}
			$this->view->renderImage('public/img/default_thumbnail_publication.png', $image, $mimeType);
		}
	}

	function thumbnail($processKey, $idx = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole('admin')) {
			$image = null;
			$mimeType = null;
			if ( is_null($processKey) == false ) {
				$processor = Processor::Named("ImportManager", 0);
				$wrapper = $processor->fileWrapper($processKey);
				if ( $wrapper != null ) {
					$filelist = $wrapper->wrapperContents();
					$intDex = intval($idx);

					if (($intDex >= 0) && ($intDex < count($filelist))) {
						$mimeType = 'image/' . file_ext($filelist[$intDex]);
						$image = $wrapper->wrappedThumbnailForName($filelist[$intDex], 200, 200);
					}
					else {
						Logger::logWarning('thumbnail bad index?');
					}
				}
				else {
					Logger::logWarning('thumbnail no processor?');
				}
			}
			else {
				Logger::logWarning('thumbnail source missing?');
			}
			$this->view->renderImage('public/img/default_thumbnail_publication.png', $image, $mimeType);
		}
	}

	function fullsized($processKey, $idx = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole('admin')) {
			$image = null;
			$mimeType = null;
			if ( is_null($processKey) == false ) {
				$processor = Processor::Named("ImportManager", 0);
				$wrapper = $processor->fileWrapper($processKey);
				if ( $wrapper != null ) {
					$filelist = $wrapper->wrapperContents();
					$intDex = intval($idx);

					if (($intDex >= 0) && ($intDex < count($filelist))) {
						$mimeType = 'image/' . file_ext($filelist[$intDex]);
						$image = $wrapper->wrappedDataForName($filelist[$intDex]);
					}
					else {
						Logger::logWarning('thumbnail bad index?');
					}
				}
				else {
					Logger::logWarning('thumbnail no processor?');
				}
			}
			else {
				Logger::logWarning('thumbnail source missing?');
			}
			$this->view->renderImage('public/img/default_thumbnail_publication.png', $image, $mimeType);
		}
	}

	function reprocess($processKey = null)
	{
		if (Auth::handleLogin() && Auth::requireRole('admin')) {
			if ( is_null($processKey) == false ) {
				$importer = Processor::Named('UploadImport', $processKey);
				$_SESSION["feedback_positive"][] = 'Reprocessing ' . $importer->getMeta(UploadImport::META_MEDIA_NAME);
				$importer->daemonizeProcess();
				echo "<b>working ..</b>";
			}
		}
	}

	function editUnprocessed($processKey = null)
	{
		if (Auth::handleLogin() && Auth::requireRole('admin')) {
			if ( ImportManager::IsEditable($processKey) == true ) {
				$processor = Processor::Named("UploadImport", $processKey);

				$this->view->source = $processor->sourceMetaData();
				$this->view->search = $processor->searchMetaData();
				$this->view->fileWrapper = $processor->sourceFileWrapper();
				$this->view->key = $processKey;
				$this->view->processor = $processor;

				$ext = $processor->sourceFileExtension();
				$this->view->addStylesheet("slideshow.css");
				$this->view->addScript("slideshow.js");
				$this->view->render( '/upload/process_' . $ext);
			}
			else {
				$_SESSION["feedback_negative"][] = 'Not editable';
				header('location: ' . Config::Web( get_short_class($this), 'index'));
			}
		}
	}

	/*************** comic vine */
	function cbz_updateMetadata($processKey)
	{
		if (Auth::handleLogin() && Auth::requireRole('admin')) {
			if ( ImportManager::IsEditable($processKey) == true ) {
				$processor = Processor::Named("UploadImport", $processKey);
				if ( $processor != false ) {
					if (isset($_POST['reset'])) {
						if ($processor->resetSearchCriteria() == false) {
							$_SESSION["feedback_negative"][] = 'Failed to reset metadata';
						}
					}
					else if (isset($_POST['search'])) {
						if ($processor->setSearchCriteria($_POST['series'], $_POST['issue'], $_POST['year']) == false) {
							$_SESSION["feedback_negative"][] = 'Failed to update metadata';
						}
					}
					else {
						$_SESSION["feedback_negative"][] = 'Unknown form submit ' . var_export($_POST, false);
					}

					$this->view->source = $processor->sourceMetaData();
					$this->view->search = $processor->searchMetaData();
					$this->view->fileWrapper = $processor->sourceFileWrapper();
					$this->view->key = $processKey;
					$this->view->processor = $processor;

					$ext = $processor->sourceFileExtension();
					$this->view->addStylesheet("slideshow.css");
					$this->view->addScript("slideshow.js");
					$this->view->render( '/upload/process_' . $ext);
				}
			}
			else {
				$_SESSION["feedback_negative"][] = 'Not a active process';
				header('location: ' . Config::Web( get_short_class($this), 'index'));
			}
		}
	}

	function cbz_initialComicVine($processKey) {
		if (Auth::handleLogin() && Auth::requireRole('admin')) {
			if ( ImportManager::IsEditable($processKey) == true ) {
				$processor = Processor::Named("UploadImport", $processKey);

				if ($processor->hasResultsMetadata() ) {
					$this->view->key = $processKey;
					$this->view->issue = $processor->issueMetadata();
					$this->view->series_model = Model::Named('Series');
					$this->view->render( '/upload/ComicVineResults', true);
				}
				else {
					// missing fileparts, run reset
// 					http_response_code(203); // Partial Information
					http_response_code(204); // Partial Information
				}
			}
			else
			{
				$_SESSION["feedback_negative"][] = 'Not a active process';
				header('location: ' . Config::Web( get_short_class($this), 'index'));
			}
		}
	}

	function cbz_refreshComicVine($processKey) {
		if (Auth::handleLogin() && Auth::requireRole('admin')) {
			if ( ImportManager::IsEditable($processKey) == true ) {
				$processor = Processor::Named("UploadImport", $processKey);

				if ( $processor != false ) {
					$processor->processSearch();

					$this->view->key = $processKey;
					$this->view->issue = $processor->issueMetadata();
					$this->view->series_model = Model::Named('Series');
					$this->view->render( '/upload/ComicVineResults', true);
				}
			}
			else
			{
				$_SESSION["feedback_negative"][] = 'Not a active process';
				header('location: ' . Config::Web( get_short_class($this), 'index'));
			}
		}
	}

	function comicVine_accept($processKey, $issue_id)
	{
		if (Auth::handleLogin() && Auth::requireRole('admin')) {
			if ( ImportManager::IsEditable($processKey) == true ) {
				$processor = Processor::Named("UploadImport", $processKey);
				if ( $processor != false ) {
					$message = $processor->getMeta(UploadImport::META_MEDIA_NAME);

					if ( $processor->selectMatchingIssue($issue_id) == false) {
						$this->view->key = $processKey;
						$this->view->issue = $processor->issueMetaData();
						$this->view->series_model = Model::Named('Series');
						$this->view->render( '/processing/comicViewResults', true);
					}
					else {
						$_SESSION["feedback_positive"][] = 'Processing "' . $message . '"';
						$processor->daemonizeProcess();
						sleep(4);
						header('location: ' . Config::Web( get_short_class($this), 'index'));
					}
				}
				else {
					$_SESSION["feedback_positive"][] = 'Failed to load processor CBZ';
					header('location: ' . Config::Web( get_short_class($this), 'index'));
				}
			}
			else {
				$_SESSION["feedback_negative"][] = 'Not a active process';
				header('location: ' . Config::Web( get_short_class($this), 'index'));
			}
		}
	}
}


