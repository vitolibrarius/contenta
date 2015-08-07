<?php

namespace controller;

use \Controller as Controller;
use \DataObject as DataObject;
use \Model as Model;
use \Auth as Auth;
use \Session as Session;
use \Logger as Logger;
use \Localized as Localized;
use model\Users as Users;
use model\Publisher as Publisher;

/**
 * Class Admin
 * The index controller
 */
class Image extends Controller
{
	/**
	 * Handles what happens when user moves to URL/index/index, which is the same like URL/index or in this
	 * case even URL (without any controller/action) as this is the default controller-action when user gives no input.
	 */
	function index()
	{
		if (Auth::handleLogin()) {
			$this->view->render( '/error/index' );
		}
	}

	private function imageResponse( $graphicFileName = null, $type = 'png' )
	{
		$fileModTime = filemtime($graphicFileName);
		$fileEtag = hash(HASH_DEFAULT_ALGO, $graphicFileName . $fileModTime);

		if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
			$modDate = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
		}

		$clientEtag = '';
		if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
			$clientEtag = trim($_SERVER['HTTP_IF_NONE_MATCH']);
		}

		if (isset($modDate) && (strtotime($modDate) == $fileModTime) && $clientEtag == $fileEtag) {
			// browser cache content IS current, so we just respond '304 Not Modified'
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', $fileModTime).' GMT', true, 304);
		}
		else {
			// Image not cached or cache outdated, we respond '200 OK' and output the image.
			header('Last-Modified: '.gmdate('D, d M Y H:i:s', $fileModTime).' GMT', true, 200);
			header('Etag: ' . $fileEtag);
			header('Cache-Control: no-transform,public');
			header('Content-Type: image/' . $type);
			header('Content-transfer-encoding: binary');
			header('Content-length: ' . filesize($graphicFileName));
			readfile($graphicFileName);
		}
	}

	function icon($table = null, $id = null)
	{
		if (isset($table, $id)) {
			$image = hashedImagePath( $table, $id, Model::IconName );
			if ( is_null($image) ) {
				$image = 'public/img/default_icon_' . $table . '.png';
			}

			$this->imageResponse( $image, file_ext($image));
		}
	}

	function thumbnail($table = null, $id = null)
	{
		Logger::logWarning("Thumbnail for: $table / $id ", $table, $id );
		if (isset($table, $id)) {
			$image = hashedImagePath( $table, $id, Model::ThumbnailName );
			Logger::logWarning("Thumbnail image: $image", $table, $id );
			if ( is_null($image) ) {
				$image = 'public/img/default_thumbnail_' . $table . '.png';
			}

			$this->imageResponse( $image, file_ext($image));
		}
	}
}
