<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\media\Media as Media;

/* import related objects */
use \model\media\Media_Type as Media_Type;
use \model\media\Media_TypeDBO as Media_TypeDBO;
use \model\media\Publication as Publication;
use \model\media\PublicationDBO as PublicationDBO;

class MediaDBO extends _MediaDBO
{
	public function publisher() {
		$publication = $this->publication();
		if ( $publication != false) {
			return $publication->publisher();
		}
		return false;
	}

	public function displayName() {
		$type = $this->mediaType();
		return (empty($type) ? 'Unknown' : $type->name) . " " . $this->filename;
	}

	public function formattedSize() {
		return (isset($this->size) ? formatSizeUnits($this->size) : '');
	}

	public function contentaPath()
	{
		return parent::mediaPath( $this->filename );
	}

	function fileWrapper()
	{
		$path = parent::mediaPath( $this->filename );
		if ( file_exists($path) ) {
			return FileWrapper::instance($path);
		}

		return null;
	}

	function indexedThumbnail($idx = 0, $width = 100, $height = 100)
	{
		$image = null;
		$mimeType = null;

		$wrapper = $this->fileWrapper();
		if ( $wrapper != null ) {
			$filelist = $wrapper->imageContents();
			$intDex = intval($idx);

			if (($intDex >= 0) && ($intDex < count($filelist))) {
				$imageFile = $filelist[$intDex];
				$mimeType = 'image/' . file_ext($filelist[$intDex]);
				$image = $wrapper->wrappedThumbnailForName($imageFile, $width, $height);
			}
		}
		return array( $image, $mimeType );
	}
}

?>
