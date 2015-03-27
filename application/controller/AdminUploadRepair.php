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

			$idx = $processor->correctChunkIndex($chunkNum);
			$this->view->active = $processor->chunk($idx);;
			$this->view->pageCurrent = $idx;
			$this->view->pageCount = $processor->totalChunks();
			$this->view->render( '/upload/processing');
		}
	}

	function firstThumbnail($processKey = null)
	{
		if (Auth::handleLogin() && Auth::requireRole('admin')) {
			if ( is_null($processKey) == false ) {
				$mimeType = null;
				$image = null;
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
}


