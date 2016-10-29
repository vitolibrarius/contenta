<?php

namespace controller;

use \Controller as Controller;
use \DataObject as DataObject;
use \Model as Model;
use \Auth as Auth;
use \Logger as Logger;
use \Localized as Localized;
use \Config as Config;
use \Processor as Processor;

use \SQL as SQL;
use db\Qualifier as Qualifier;

use \http\Session as Session;
use \http\HttpGet as HttpGet;
use \http\HttpPost as HttpPost;
use \http\PageParams as PageParams;

use connectors\ComicVineConnector as ComicVineConnector;
use processor\ComicVineImporter as ComicVineImporter;
use exceptions\ImportMediaException as ImportMediaException;
use processor\UploadImport as UploadImport;
use utilities\FileWrapper as FileWrapper;

use controller\Admin as Admin;

use \model\user\Users as Users;
use \model\network\Endpoint as Endpoint;
use \model\network\Endpoint_Type as Endpoint_Type;
use \model\media\Publisher as Publisher;
use \model\media\Publication as Publication;
use \model\media\Character as Character;
use \model\media\Character_Alias as Character_Alias;
use \model\media\Series as Series;
use \model\media\Series_Alias as Series_Alias;
use \model\media\Series_Character as Series_Character;
use \model\media\Media as Media;
use \model\media\MediaDBO as MediaDBO;

/**
 * Class Admin
 * The index controller
 */
class AdminMedia extends Admin
{
	function index()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$this->view->addStylesheet("select2.min.css");
			$this->view->addScript("select2.min.js");

			$parameters = Session::pageParameters( $this, "index" );
			$this->view->params = $parameters;

			$model = Model::Named('Media');
			$this->view->model = $model;
			$this->view->render( '/admin/mediaIndex');
		}
	}

	function iconForMedia($mediaId = null, $idx = 0)
	{
		if (Auth::handleLogin()) {
			$image = null;
			$mimeType = null;
			if ( is_null($mediaId) == false ) {
				$model = Model::Named('Media');
				$media = $model->objectForId($mediaId);
				if ($media instanceof MediaDBO ) {
					list($image, $mimeType) = $media->indexedThumbnail($idx, 125, 125);
				}
			}
			else {
				Logger::logWarning('thumbnail source missing?');
			}
			$this->view->renderImage('public/img/default_thumbnail_publication.png', $image, $mimeType);
		}
	}

	function thumbnailForMedia($mediaId = null, $idx = 0)
	{
		if (Auth::handleLogin()) {
			$image = null;
			$mimeType = null;
			if ( is_null($mediaId) == false ) {
				$model = Model::Named('Media');
				$media = $model->objectForId($mediaId);
				if ($media instanceof MediaDBO ) {
					list($image, $mimeType) = $media->indexedThumbnail($idx, 200, 200);
				}
			}
			else {
				Logger::logWarning('thumbnail source missing?');
			}
			$this->view->renderImage('public/img/default_thumbnail_publication.png', $image, $mimeType);
		}
	}

	function searchMedia($pageNum = null)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$parameters = Session::pageParameters( $this, "index" );
			$parameters->setPageSize(5);
			list( $hasNewValues, $query) = $parameters->updateParametersFromGET( array(
				'searchSeries', 'searchCharacter', 'searchStoryArcs', 'searchIssue', 'searchMedia', 'searchYear' )
			);

			$model = Model::Named('Publication');
			$qualifiers = array();
			if ( isset($query['searchIssue']) && strlen($query['searchIssue']) > 0) {
				$qualifiers[] = Qualifier::Equals( Publication::issue_num, $query['searchIssue'] );
			}
			if ( isset($query['searchYear']) && strlen($query['searchYear']) == 4 ) {
				$start = strtotime("01-01-" . $query['searchYear'] . " 00:00");
				$end = strtotime("31-12-" . $query['searchYear'] . " 23:59");
				$qualifiers[] = Qualifier::Between( Publication::pub_date, $start, $end );
			}
			if ( isset($query['searchMedia']) && $query['searchMedia'] == '1') {
				$select = \SQL::Count( Model::Named('Media'), array(Media::publication_id), null, array(array("desc" => "count(*)")) )
					->having( array("count(*) > 1") );
				$pub_idArray = array_map(function($stdClass) {return $stdClass->{Media::publication_id}; },
					$select->fetchAll());

				if ( is_array($pub_idArray) && count($pub_idArray) > 0 ) {
					$qualifiers[] = Qualifier::IN( Publication::id, $pub_idArray );
				}
				else {
					$qualifiers[] = Qualifier::Equals( Publication::id, 0 );
				}
			}
			if ( isset($query['searchCharacter']) && empty($query['searchCharacter']) == false) {
				$characterIdArray = (is_array($query['searchCharacter']) ? $query['searchCharacter'] : array($query['searchCharacter']));
				$pub_idArray = Model::Named("Publication_Character")->publicationIdForCharacterIdArray($characterIdArray);
				if ( is_array($pub_idArray) && count($pub_idArray) > 0 ) {
					$qualifiers[] = Qualifier::IN( Publication::id, $pub_idArray );
				}
				else {
					$qualifiers[] = Qualifier::Equals( Publication::id, 0 );
				}
			}
			if ( isset($query['searchSeries']) && strlen($query['searchSeries']) > 0) {
				$select = \SQL::Select( Model::Named('Series'), array(Series::id, Series::name))
					->where( Qualifier::Like( Series::search_name, normalizeSearchString($query['searchSeries'])) )
					->orderBy(array(Series::name));
				$series_idArray = array_map(function($stdClass) {return $stdClass->{Series::id}; },
					$select->fetchAll());

				if ( is_array($series_idArray) && count($series_idArray) > 0 ) {
					$qualifiers[] = Qualifier::IN( Publication::series_id, $series_idArray );
				}
				else {
					$qualifiers[] = Qualifier::Equals( Publication::id, 0 );
				}
			}
			if ( isset($query['searchStoryArcs']) && empty($query['searchStoryArcs']) == false) {
				$storyArcIdArray = (is_array($query['searchStoryArcs']) ? $query['searchStoryArcs'] : array($query['searchStoryArcs']));
				$qualifiers[] = Qualifier::InSubQuery( Publication::id,
					SQL::Select(Model::Named('Story_Arc_Publication'), array("publication_id"))
						->where( Qualifier::IN( "story_arc_id", $storyArcIdArray))
						->limit(0)
				);
			}

			// restrict to only publication that have at least 1 media
			$qualifiers[] = Qualifier::GreaterThan( Publication::media_count, 0 );

			if ( $hasNewValues ) {
				if ( count($qualifiers) > 0 ) {
					$count = SQL::Count( $model, null, Qualifier::AndQualifier( $qualifiers ) )->fetch();
				}
				else {
					$count = SQL::Count( $model )->fetch();
				}

				$parameters->queryResults($count->count);
			}
			else {
				if ( is_null( $pageNum) ) {
					$pageNum = $parameters->valueForKey( PageParams::PAGE_SHOWN, 0 );
				}
				else {
					$parameters->setValueForKey( PageParams::PAGE_SHOWN, $pageNum );
				}
			}

			$select = SQL::Select($model);
			if ( count($qualifiers) > 0 ) {
				$select->where( Qualifier::AndQualifier( $qualifiers ));
			}
			$select->limit($parameters->pageSize());
			$select->offset($parameters->pageShown());
			$select->orderBy( array( "series_id", "issue_num", "pub_date") );

			$this->view->model = $model;
			$this->view->params = $parameters;
			$this->view->listArray = $select->fetchAll();
			$this->view->render( '/admin/mediaCards', true);
		}
	}

	function reprocessMedia($oid = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			if ( $oid > 0 ) {
				$model = Model::Named('Media');
				$media = $model->objectForId($oid);
				if ($media instanceof MediaDBO ) {
					$publication_id = $media->publication_id;
					$source = $media->contentaPath();
					$contentHash = hash_file(HASH_DEFAULT_ALGO, $source);
					$root = Config::GetProcessing();
					$workingDir = appendPath($root, "UploadImport", $contentHash );

					if ( is_dir($workingDir) == true ) {
						Session::addNegativeFeedback( Localized::Get("Upload", 'Hash Exists') .' "'. $media->filename . '"' );
					}
					else {
						try {
							$importFilename = (isset($media->original_filename) ? $media->original_filename : $media->filename);
							$importer = Processor::Named('UploadImport', $contentHash);
							$importer->setMediaForImport($source, $importFilename);
							$importer->generateThumbnails();
							$importer->resetSearchCriteria();
							$importer->setMeta( UploadImport::META_STATUS, "REPROCESS");

							Session::addPositiveFeedback(Localized::Get("Upload", 'ReprocessRequested') .' "'. $importFilename .'"');

							// now remove the old media record
							$errors = $model->deleteObject($media);
							if ( $errors == false ) {
								Session::addNegativeFeedback( Localized::GlobalLabel("Delete Failure") );
							}

							header('location: ' . Config::Web('/AdminUploadRepair/editUnprocessed/' . $contentHash));
						}
						catch ( ImportMediaException $me ) {
							Session::addNegativeFeedback( Localized::Get("Upload", $me->getMessage() ));
							http_response_code(415); // Unsupported Media Type
						}
					}
				}
				else {
					Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) . " " .var_export($media, true));
					$this->view->render('/error/index');
				}
			}
			else {
				Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) . " " . $model->tableName() . " [$oid]");
				$this->view->render('/error/index');
			}
		}
	}

	function deleteMedia($oid = 0)
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			if ( $oid > 0 ) {
				$model = Model::Named('Media');
				$media = $model->objectForId($oid);
				if ($media instanceof MediaDBO ) {
					$errors = $model->deleteObject($media);
					if ( $errors == false ) {
						$this->view->renderJson( Localized::GlobalLabel( "Delete Failure" ));
					}
					else {
						$this->view->renderJson( Localized::GlobalLabel( "Delete Completed" ));
					}
				}
				else {
					$this->view->renderJson( Localized::GlobalLabel( "Failed to find request record" ) . " " . $model->tableName() . " [$oid]");
				}
			}
			else {
				$this->view->renderJson( Localized::GlobalLabel( "Failed to find request record" ));
			}
		}
	}
}
