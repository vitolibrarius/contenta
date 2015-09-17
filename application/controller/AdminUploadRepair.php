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
use \SQL as SQL;
use db\Qualifier as Qualifier;

use processor\ComicVineImporter as ComicVineImporter;
use processor\UploadImport as UploadImport;
use processor\ImportManager as ImportManager;

use controller\Admin as Admin;

use model\Users as Users;
use model\Series as Series;
use model\SeriesDBO as SeriesDBO;
use model\Publication as Publication;
use model\PublicationDBO as PublicationDBO;
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
				list( $image, $mimeType ) = $processor->firstThumbnail( $processKey );
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
				list( $image, $mimeType ) = $processor->indexedThumbnail($processKey, $idx, 200, 200);
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
					$filelist = $wrapper->imageContents();
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
				if ($importer->resetSearchCriteria() == false) {
					Session::addNegativeFeedback(Localized::Get("Upload", 'Failed to reset metadata')
						. ' ' . $importer->getMeta(UploadImport::META_MEDIA_NAME));
				}
				$importer->daemonizeProcess();
				Session::addPositiveFeedback(Localized::Get("Upload", 'Reprocessing')
					. ' ' . $importer->getMeta(UploadImport::META_MEDIA_NAME));
				echo "<b>working ..</b>";

				$importMgr = Processor::Named("ImportManager", 0);
				$importMgr->clearMetadataFor( $processKey );
			}
		}
	}

	function deleteUnprocessed($processKey = null)
	{
		if (Auth::handleLogin() && Auth::requireRole('admin')) {
			if ( ImportManager::IsEditable($processKey) == true ) {
				$importMgr = Processor::Named("ImportManager", 0);
				$page = $importMgr->chunkNumberFor($processKey);
				$metadata = $importMgr->metadataFor( $processKey );
				$filename = array_valueForKeypath( "filename", $metadata);

				if ( $importMgr->purgeUnprocessed($processKey) ) {
					Session::addPositiveFeedback('Deleted in process files for ' . $filename);
				}
				else {
					Logger::logInfo("Error destroying working directory ", "Filename",  $filename);
					Session::addNegativeFeedback( 'Failed to deleted in process files for ' . $filename);
				}

				header('location: ' . Config::Web( get_short_class($this), 'index', $importMgr->chunkNumberFor($processKey)));
			}
			else {
				Session::addNegativeFeedback(Localized::Get("Upload", 'NotEditable'));
				header('location: ' . Config::Web( get_short_class($this), 'index'));
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
				Session::addNegativeFeedback(Localized::Get("Upload", 'NotEditable'));
				header('location: ' . Config::Web( get_short_class($this), 'index'));
			}
		}
	}

	function editUnprocessedManually($processKey = null)
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

				$this->view->saveAction = appendPath("/AdminUploadRepair/editUnprocessedManually_publication", $processKey);
				$this->view->model = Model::Named('Series');

				$this->view->addStylesheet("select2.min.css");
				$this->view->addStylesheet("slideshow.css");

				$this->view->addScript("slideshow.js");
				$this->view->addScript("select2.min.js");

				$this->view->render( '/upload/process_' . $ext . '_manualSeries');
			}
			else {
				Session::addNegativeFeedback(Localized::Get("Upload", 'NotEditable'));
				header('location: ' . Config::Web( get_short_class($this), 'index'));
			}
		}
	}

	function editUnprocessedManually_seriesList($processKey = null)
	{
		if (Auth::handleLogin() && Auth::requireRole('admin')) {
			if ( ImportManager::IsEditable($processKey) == true ) {
				$model = Model::Named('Series');
				$qualifiers = array();
				if ( isset($_GET['name']) && strlen($_GET['name']) > 0) {
					$qualifiers[] = Qualifier::Like( Series::search_name, normalizeSearchString($_GET['name']));
				}
				if ( isset($_GET['year']) && strlen($_GET['year']) == 4 ) {
					$qualifiers[] = Qualifier::Equals( Series::start_year, $_GET['year'] );
				}
				if ( isset($_GET['publisher_id']) && intval($_GET['publisher_id']) > 0 ) {
					$qualifiers[] = Qualifier::Equals( Series::publisher_id, $_GET['publisher_id'] );
				}

				$select = SQL::Select($model);
				if ( count($qualifiers) > 0 ) {
					$select->where( Qualifier::AndQualifier( $qualifiers ));
				}
				$select->orderBy( $model->sortOrder() );

				$this->view->model = $model;
				$this->view->key = $processKey;
				$this->view->listArray = $select->fetchAll();
				$this->view->selectAction = "/AdminUploadRepair/editUnprocessedManually_publication/" . $processKey;
				$this->view->render( '/admin/seriesCards', true);
			}
			else {
				Session::addNegativeFeedback(Localized::Get("Upload", 'NotEditable'));
				header('location: ' . Config::Web( get_short_class($this), 'index'));
			}
		}
	}

	function editUnprocessedManually_publication($processKey = null, $seriesId = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole('admin')) {
			if ( ImportManager::IsEditable($processKey) == true ) {
				$model = Model::Named('Series');
				$seriesObj = null;

				if ( isset($seriesId) == false || $seriesId == 0) {
					$values = splitPOSTValues($_POST);
					if ( isset($values[$model->tableName()], $values[$model->tableName()][Series::pub_wanted]) == false ) {
						$values[$model->tableName()][Series::pub_wanted] = Model::TERTIARY_FALSE;
					}

					list($seriesObj, $error) = $model->createObject($values[$model->tableName()]);
					if ( is_array($errors) ) {
						Session::addNegativeFeedback( Localized::GlobalLabel("Validation Errors") );
						foreach ($errors as $attr => $errMsg ) {
							Session::addValidationFeedback( $errMsg );
						}
					}
				}
				else {
					$seriesObj = $model->objectForId($seriesId);
				}

				if ( empty($seriesObj) == false ) {
					$this->view->seriesObj = $seriesObj;
					$this->view->series_id = $seriesObj->id;

					$processor = Processor::Named("UploadImport", $processKey);

					$this->view->source = $processor->sourceMetaData();
					$this->view->search = $processor->searchMetaData();
					$this->view->fileWrapper = $processor->sourceFileWrapper();
					$this->view->key = $processKey;
					$this->view->processor = $processor;

					$ext = $processor->sourceFileExtension();

					$this->view->model = Model::Named('Publication');
					$this->view->listArray = $seriesObj->publications();

					$this->view->addStylesheet("select2.min.css");
					$this->view->addStylesheet("slideshow.css");

					$this->view->addScript("slideshow.js");
					$this->view->addScript("select2.min.js");
					$this->view->saveAction = appendPath("/AdminUploadRepair/editUnprocessedManually_selectMatch", $processKey, $seriesId);

					$this->view->render( '/upload/process_' . $ext . '_manualPublication');
				}
				else {
					$this->editUnprocessedManually_seriesList($processKey);
				}
			}
			else {
				Session::addNegativeFeedback(Localized::Get("Upload", 'NotEditable'));
				header('location: ' . Config::Web( get_short_class($this), 'index'));
			}
		}
	}

	function editUnprocessedManually_selectMatch($processKey = null, $seriesId = 0, $publicationId = 0)
	{
		Logger::logInfo( "editUnprocessedManually_selectMatch starting", $processKey, $seriesId."/".$publicationId);
		if (Auth::handleLogin() && Auth::requireRole('admin')) {
			if ( ImportManager::IsEditable($processKey) == true ) {
				$model = Model::Named('Series');
				$pub_model = Model::Named('Publication');
				$seriesObj = $model->objectForId($seriesId);

				if ( $seriesObj instanceof SeriesDBO ) {
					Logger::logInfo( "Found series " . $seriesObj->name, $processKey, $seriesId."/".$publicationId);

					if ( isset($publicationId) == false || $publicationId == 0) {
						Logger::logInfo( "Creating new publication", $processKey, $seriesId."/".$publicationId);
						$values = splitPOSTValues($_POST);
						list($publicationObj, $error) = $pub_model->createObject($values[$pub_model->tableName()]);
						if ( is_array($errors) ) {
							Session::addNegativeFeedback( Localized::GlobalLabel("Validation Errors") );
							foreach ($errors as $attr => $errMsg ) {
								Session::addValidationFeedback( $errMsg );
							}
						}
					}
					else {
						$publicationObj = $pub_model->objectForId($publicationId);
					}

					if ( $publicationObj instanceof PublicationDBO ) {
						Logger::logInfo( "Found publication " . $publicationObj->xid, $processKey, $seriesId."/".$publicationId);
						$importMgr = Processor::Named("ImportManager", 0);
						$processor = Processor::Named("UploadImport", $processKey);
						if ( $processor != false ) {
							$message = $processor->getMeta(UploadImport::META_MEDIA_NAME);

							if ( $processor->selectMatchingPublication( $publicationObj ) == false) {
								Logger::logInfo( "selectMatchingPublication error", $processKey, $seriesId."/".$publicationId);
								$this->view->key = $processKey;
								$this->view->issue = $processor->issueMetaData();
								$this->view->series_model = Model::Named('Series');
								$this->view->render( '/processing/comicViewResults', true);
							}
							else {
								Session::addPositiveFeedback(Localized::Get("Upload", 'Processing') . ' "' . $message . '"' );
								$processor->daemonizeProcess();
								sleep(2);
								header('location: ' . Config::Web( get_short_class($this), 'index', $importMgr->chunkNumberFor($processKey)));
							}
						}
						else {
							Session::addNegativeFeedback(Localized::Get("Upload", 'Failed to load processor'));
							header('location: ' . Config::Web( get_short_class($this), 'index', $importMgr->chunkNumberFor($processKey)));
						}
					}
					else {
						Logger::logInfo( "No publication found !!", $processKey, $seriesId."/".$publicationId);
						$this->editUnprocessedManually_seriesList($processKey);
					}
				}
				else {
					Logger::logInfo( "No series found!!!", $processKey, $seriesId."/".$publicationId);
					Session::addNegativeFeedback(Localized::Get("Upload", 'UnidentifiedManualSeries'));
					$this->editUnprocessedManually_seriesList($processKey);
				}
			}
			else {
				Logger::logInfo( "No editable?", $processKey, $seriesId."/".$publicationId);
				Session::addNegativeFeedback(Localized::Get("Upload", 'NotEditable'));
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
							Session::addNegativeFeedback(Localized::Get("Upload", 'Failed to reset metadata'));
						}
					}
					else if (isset($_POST['search'])) {
						if ($processor->setSearchCriteria($_POST['series'], $_POST['issue'], $_POST['year']) == false) {
							Session::addNegativeFeedback(Localized::Get("Upload", 'Failed to update metadata'));
						}
					}
					else {
						Session::addNegativeFeedback(Localized::Get("Upload", 'Unknown form submit') . ' ' . var_export($_POST, false));
					}
					$processor->processSearch();

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
				Session::addNegativeFeedback(Localized::Get("Upload", 'Not Active'));
				header('location: ' . Config::Web( get_short_class($this), 'index'));
			}
		}
	}

	function cbz_initialComicVine($processKey) {
		if (Auth::handleLogin() && Auth::requireRole('admin')) {
			if ( ImportManager::IsEditable($processKey) == true ) {
				$processor = Processor::Named("UploadImport", $processKey);
				if ( $processor != false ) {
					$this->view->key = $processKey;
					$this->view->issue = $processor->issueMetadata();
					$this->view->series_model = Model::Named('Series');
					$this->view->render( '/upload/ComicVineResults', true);
				}
			}
			else
			{
				Session::addNegativeFeedback(Localized::Get("Upload", 'Not Active'));
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
				Session::addNegativeFeedback(Localized::Get("Upload", 'Not Active'));
				header('location: ' . Config::Web( get_short_class($this), 'index'));
			}
		}
	}

	function comicVine_accept($processKey, $issue_id)
	{
		if (Auth::handleLogin() && Auth::requireRole('admin')) {
			if ( ImportManager::IsEditable($processKey) == true ) {
				$importMgr = Processor::Named("ImportManager", 0);
				$importMgr->clearMetadataFor( $processKey );

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
						Session::addPositiveFeedback(Localized::Get("Upload", 'Processing') . ' "' . $message . '"' );
						$processor->daemonizeProcess();
						sleep(2);
						header('location: ' . Config::Web( get_short_class($this), 'index', $importMgr->chunkNumberFor($processKey)));
					}
				}
				else {
					Session::addNegativeFeedback(Localized::Get("Upload", 'Failed to load processor'));
					header('location: ' . Config::Web( get_short_class($this), 'index', $importMgr->chunkNumberFor($processKey)));
				}
			}
			else {
				Session::addNegativeFeedback(Localized::Get("Upload", 'Not Active'));
				header('location: ' . Config::Web( get_short_class($this), 'index', $importMgr->chunkNumberFor($processKey)));
			}
		}
	}
}


