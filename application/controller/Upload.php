<?php

namespace controller;

use \Controller as Controller;
use \DataObject as DataObject;
use \Model as Model;
use \Auth as Auth;
use \Session as Session;
use \Logger as Logger;
use \Localized as Localized;
use \Processor as Processor;

use model\Users as Users;

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
		$this->serviceRequest();
		$this->view->render('/upload/index');
	}

	function service()
	{
		$this->serviceRequest();
		$this->view->render('/upload/service', true);
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
			Session::addNegativeFeedback( Localized::Get("Upload", "Unsupported Mime Type" ) . $_FILES['mediaFile']['type']);
			http_response_code(415); // Unsupported Media Type
		}
		else
		{
			$extension = file_ext($_FILES['mediaFile']['name']);
			$contentHash = hash_file(HASH_DEFAULT_ALGO, $_FILES['mediaFile']['tmp_name']);
			$importer = Processor::Named('UploadImport', $contentHash);
			if ( $importer->setMediaForImport($_FILES['mediaFile']['tmp_name'], basename($_FILES['mediaFile']['name'])) == true ) {
				$_SESSION["feedback_positive"][] = 'Upload success';
				$uploadSuccess = true;
			}
			else {
				Session::addNegativeFeedback('Identified as ' . ' (' . $pubObj->name . ')  issue ' . $pubObj->issue);
			}
		}
		return $uploadSuccess;
	}
}