<?php

namespace controller;

use \Controller as Controller;
use \DataObject as DataObject;
use \Config as Config;
use \Model as Model;
use \Auth as Auth;
use \http\Session as Session;;
use \Logger as Logger;
use \Localized as Localized;
use \Processor as Processor;

use \model\user\Users as Users;

use exceptions\ImportMediaException as ImportMediaException;
use processor\UploadImport as UploadImport;
use utilities\FileWrapper as FileWrapper;

/**
 * Class Error
 * The index controller
 */
class Upload extends Controller
{
	/**
	 * Handles what happens when user moves to URL/index/index, which is the same like URL/index or in this
	 * case even URL (without any controller/action) as this is the default controller-action when user gives no input.
	 */
	function index()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$this->view->render( '/upload/index' );
		}
	}

	function allowedMimeTypes()
	{
		return array("application/octet-stream");
	}

	function upload()
	{
		if (Auth::handleLogin() && Auth::requireRole(Users::AdministratorRole)) {
			$this->serviceRequest();
			$this->view->render('/upload/index');
		}
	}

	function service($userHash = null)
	{
		if ( Auth::handleLoginWithAPI($userHash) && Auth::requireRole(Users::AdministratorRole)) {
			$this->serviceRequest();
			$this->view->render('/upload/service', true);
		}
	}

	function serviceRequest()
	{
		$uploadSuccess = false;

		if ( isset($_FILES['mediaFile']) == false )
		{
			$max_post_bytes = convertToBytes(ini_get('post_max_size'));
			$max_upload_bytes = convertToBytes(ini_get('upload_max_filesize'));
			// Error occurred if method is POST, and content length is too long

			if ($_SERVER['CONTENT_LENGTH'] > $max_post_bytes) {
				Session::addNegativeFeedback( Localized::Get("Upload", 'UPLOAD_ERR_FORM_SIZE'));
			}
			if ($_SERVER['CONTENT_LENGTH'] > $max_upload_bytes) {
				Session::addNegativeFeedback( Localized::Get("Upload", 'UPLOAD_ERR_INI_SIZE'));
			}
			Session::addNegativeFeedback( Localized::Get("Upload", "No media available"));
			http_response_code(400); // Bad Request
		}
		else if ($_FILES["mediaFile"]["error"] > 0)
		{
			switch ($code) {
				case UPLOAD_ERR_INI_SIZE:
					Session::addNegativeFeedback( Localized::Get("Upload", 'UPLOAD_ERR_INI_SIZE'));
					break;
				case UPLOAD_ERR_FORM_SIZE:
					Session::addNegativeFeedback( Localized::Get("Upload", 'UPLOAD_ERR_FORM_SIZE'));
					break;
				case UPLOAD_ERR_PARTIAL:
					Session::addNegativeFeedback( Localized::Get("Upload", 'UPLOAD_ERR_PARTIAL'));
					break;
				case UPLOAD_ERR_NO_FILE:
					Session::addNegativeFeedback( Localized::Get("Upload", 'UPLOAD_ERR_NO_FILE'));
					break;
				case UPLOAD_ERR_NO_TMP_DIR:
					Session::addNegativeFeedback( Localized::Get("Upload", 'UPLOAD_ERR_NO_TMP_DIR'));
					break;
				case UPLOAD_ERR_CANT_WRITE:
					Session::addNegativeFeedback( Localized::Get("Upload", 'UPLOAD_ERR_CANT_WRITE'));
					break;
				case UPLOAD_ERR_EXTENSION:
					Session::addNegativeFeedback( Localized::Get("Upload", 'UPLOAD_ERR_EXTENSION'));
					break;

				default:
					Session::addNegativeFeedback( Localized::Get("Upload", 'Upload Error'));
					break;
			}

			http_response_code(400); // Bad Request
		}
		else if ( empty($_FILES['mediaFile']['name']) )
		{
			Session::addNegativeFeedback( Localized::Get("Upload", "No file selected"));
			http_response_code(400); // Bad Request
		}
		else if ( in_array($_FILES['mediaFile']['type'], $this->allowedMimeTypes()) == false )
		{
			Session::addNegativeFeedback( Localized::Get("Upload", "Unsupported Mime Type" ) . ' "' . $_FILES['mediaFile']['type'] . '"');
			http_response_code(415); // Unsupported Media Type
		}
		else
		{
			$extension = file_ext($_FILES['mediaFile']['name']);
			$wrapper = FileWrapper::force($_FILES['mediaFile']['tmp_name'], $extension);
			$validFormat = true;
			if ( $wrapper->testWrapper() != null ) {
				// something is wrong with the file
				switch( $extension ) {
					case 'cbz':
						// check to see if it is actually a cbr
						$rar_wrapper = FileWrapper::force($_FILES['mediaFile']['tmp_name'], 'cbr');
						if ( $rar_wrapper->testWrapper() != null ) {
							$validFormat = false;
						}
						break;
					case 'cbr':
						// check to see if it is actually a cbz
						$rar_wrapper = FileWrapper::force($_FILES['mediaFile']['tmp_name'], 'cbz');
						if ( $rar_wrapper->testWrapper() != null ) {
							$validFormat = false;
						}
						break;
					default:
						$validFormat = false;
						break;
				}
			}

			if ( $validFormat == true ) {
				$contentSize = filesize($_FILES['mediaFile']['tmp_name']);
				$contentMin = convertToBytes( Config::GetInteger( "UploadImport/MinSize", 5) . "mb");
				$contentMax = convertToBytes( Config::GetInteger( "UploadImport/MaxSize", 100) . "mb");
				$contentHash = hash_file(HASH_DEFAULT_ALGO, $_FILES['mediaFile']['tmp_name']);
				$root = Config::GetProcessing();
				$workingDir = appendPath($root, "UploadImport", $contentHash );
				$existing = Model::Named('Media')->objectForChecksum($contentHash);

				if ( is_dir($workingDir) == true ) {
					Session::addNegativeFeedback( Localized::Get("Upload", 'Hash Exists') .' "'. $_FILES['mediaFile']['name'] . '"' );
					http_response_code(415); // Unsupported Media Type
				}
				else if ( $existing instanceof \model\media\MediaDBO ) {
					Session::addNegativeFeedback(Localized::Get("Upload", 'Already imported') .' "'. $existing->publication()->searchString() .'"');
					http_response_code(415); // Unsupported Media Type
				}
				else {
					try {
						$running = Model::Named("Job_Running")->allForProcessor('UploadImport');

						$importer = Processor::Named('UploadImport', $contentHash);
						$importer->setMediaForImport($_FILES['mediaFile']['tmp_name'], basename($_FILES['mediaFile']['name']));

						if ( $contentSize < $contentMin ) {
							$importer->generateThumbnails();
							$importer->resetSearchCriteria();
							$importer->setMeta( UploadImport::META_STATUS, "SMALL_FILE");
						}
						else if ( $contentSize > $contentMax ) {
							$importer->generateThumbnails();
							$importer->resetSearchCriteria();
							$importer->setMeta( UploadImport::META_STATUS, "LARGE_FILE");
						}
						else if ( count($running) >= 6 ) {
							$importer->generateThumbnails();
							$importer->resetSearchCriteria();
							$importer->setMeta( UploadImport::META_STATUS, "BUSY_NO_SEARCH");
						}
						else {
							$importer->daemonizeProcess();
						}

						Session::addPositiveFeedback(Localized::Get("Upload", 'Upload success') .' "'. $_FILES['mediaFile']['name'] .'"');
						$uploadSuccess = true;
					}
					catch ( ImportMediaException $me ) {
						Session::addNegativeFeedback( Localized::Get("Upload", $me->getMessage() ));
						http_response_code(415); // Unsupported Media Type
					}
				}
			}
			else {
				Session::addNegativeFeedback( Localized::Get("Upload", "FILE_CORRUPT" ) . ' "' . $_FILES['mediaFile']['name'] . '"');
				http_response_code(415); // Unsupported Media Type
			}
		}
		return $uploadSuccess;
	}
}
