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

use connectors\ComicVineConnector as ComicVineConnector;
use processor\ComicVineImporter as ComicVineImporter;
use exceptions\ImportMediaException as ImportMediaException;
use processor\UploadImport as UploadImport;
use utilities\FileWrapper as FileWrapper;

use controller\Admin as Admin;

use model\Users as Users;
use model\Endpoint as Endpoint;
use model\Endpoint_Type as Endpoint_Type;
use model\Publisher as Publisher;
use model\Publication as Publication;
use model\Character as Character;
use model\Character_Alias as Character_Alias;
use model\Series as Series;
use model\Series_Alias as Series_Alias;
use model\Series_Character as Series_Character;
use model\User_Series as User_Series;
use model\Media as Media;
use model\MediaDBO as MediaDBO;

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

	function searchMedia()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$model = Model::Named('Publication');
			$qualifiers = array();
			if ( isset($_GET['name']) && strlen($_GET['name']) > 0) {
				$qualifiers[] = Qualifier::Like( Publication::name, $_GET['name']);
			}
			if ( isset($_GET['issue']) && strlen($_GET['issue']) > 0) {
				$qualifiers[] = Qualifier::Equals( Publication::issue_num, $_GET['issue'] );
			}
			if ( isset($_GET['year']) && strlen($_GET['year']) == 4 ) {
				$start = strtotime("01-01-" . $_GET['year'] . " 00:00");
				$end = strtotime("31-12-" . $_GET['year'] . " 23:59");
				$qualifiers[] = Qualifier::Between( Publication::pub_date, $start, $end );
			}
			if ( isset($_GET['media']) && $_GET['media'] === 'true') {
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
			if ( isset($_GET['character_id']) && is_array($_GET['character_id']) && count($_GET['character_id']) > 0 ) {
				$pub_idArray = Model::Named("Publication_Character")->publicationIdForCharacterIdArray($_GET['character_id']);
				if ( is_array($pub_idArray) && count($pub_idArray) > 0 ) {
					$qualifiers[] = Qualifier::IN( Publication::id, $pub_idArray );
				}
				else {
					$qualifiers[] = Qualifier::Equals( Publication::id, 0 );
				}
			}
			if ( isset($_GET['series_name']) && strlen($_GET['series_name']) > 0) {
				$select = \SQL::Select( Model::Named('Series'), array(Series::id, Series::name))
					->where( Qualifier::Like( Series::search_name, normalizeSearchString($_GET['series_name'])) )
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
			if ( isset($_GET['story_arc_id']) && is_array($_GET['story_arc_id']) && count($_GET['story_arc_id']) > 0 ) {
				$qualifiers[] = Qualifier::InSubQuery( Publication::id,
					SQL::Select(Model::Named('Story_Arc_Publication'), array("publication_id"))
						->where( Qualifier::IN( "story_arc_id", $_GET['story_arc_id']))
						->limit(0)
				);
			}
			$qualifiers[] = Qualifier::GreaterThan( Publication::media_count, 0 );

			$select = SQL::Select($model);
			if ( count($qualifiers) > 0 ) {
				$select->where( Qualifier::AndQualifier( $qualifiers ));
			}
			$select->orderBy( array( "series_id", "issue_num", "pub_date") );

			$this->view->model = $model;
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
				Session::addNegativeFeedback(Localized::GlobalLabel( "Failed to find request record" ) . " " .$oid);
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
					$this->view->renderJson( Localized::GlobalLabel( "Failed to find request record" ));
				}
			}
			else {
				$this->view->renderJson( Localized::GlobalLabel( "Failed to find request record" ));
			}
		}
	}
}
