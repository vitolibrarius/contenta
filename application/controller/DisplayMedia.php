<?php

namespace controller;

use \Controller as Controller;
use \DataObject as DataObject;
use \Model as Model;
use \Auth as Auth;
use \Logger as Logger;
use \Localized as Localized;
use \Config as Config;

use \http\Session as Session;
use \http\HttpGet as HttpGet;
use \http\HttpPost as HttpPost;
use \http\PageParams as PageParams;

use \SQL as SQL;
use db\Qualifier as Qualifier;

use \model\user\Users as Users;
use \model\media\Publisher as Publisher;
use \model\media\Character as Character;
use \model\media\Story_Arc as Story_Arc;
use \model\pull_list\Pull_List as Pull_List;

/**
 * Class Admin
 * The index controller
 */
class DisplayMedia extends Controller
{
	function slideshow( $id )
	{
		if (Auth::handleLogin()) {
			$media_model = Model::Named('Media');
			$user_model = Model::Named('Users');

			$user = $user_model->objectForId(Session::get('user_id'));
			$mediaObj = $media_model->objectForId($id);
			if ( $mediaObj != false )
			{
				$this->view->addStylesheet("slideshow.css");
				$this->view->addModule("libs");
				$this->view->addModule("contenta_ng.js");
				$this->view->addModule("common");

				$this->view->publication = $mediaObj->publication();
				$this->view->readingItem = Model::Named("Reading_Item")->createReadingItemPublication($user, $mediaObj->publication());
				$this->view->fileWrapper = $mediaObj->fileWrapper();
				$this->view->etag = $mediaObj->checksum;
				$this->view->imgRoot = "DisplayMedia/imageNamed";
				$this->view->media = $mediaObj;
				$this->view->render( '/media/slideshow' , true );
			}
			else {
				Session::addNegativeFeedback("Failed to find requested media" );
				$this->view->render('/error/index');
			}
		}
	}

	function imageNamed()
	{
		if (Auth::handleLogin()) {
			$name = HttpGet::get('name', '');
			$mediaId = HttpGet::get('media', '');
			Logger::logWarning("Looking for media $mediaId with name '$name'");

			$image = null;
			$mimeType = 'image/' . file_ext($name);
			$media_model = Model::Named('Media');
			$mediaObj = $media_model->objectForId($mediaId);

			if ( $mediaObj != false ) {
				$wrapper = $mediaObj->fileWrapper();
				if ( $wrapper != null ) {
					$image = $wrapper->wrappedDataForName($name);
					if ( $image == false ) {
						Session::addNegativeFeedback("Failed to find '$name'" );
					}
				}
				else {
					Logger::logWarning('No image wrapper???');
				}
			}
			else {
				Session::addNegativeFeedback("Failed to find requested media" );
			}

			$this->view->renderImage('public/img/default_thumbnail_publication.png', $image, $mimeType);
		}
	}
}
