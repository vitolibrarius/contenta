<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \utilities\FileWrapper as FileWrapper;

use \model\media\Book as Book;

/* import related objects */
use \model\media\Media_Type as Media_Type;
use \model\media\Media_TypeDBO as Media_TypeDBO;

class BookDBO extends _BookDBO
{
	public function formattedSize() {
		return (isset($this->size) ? formatSizeUnits($this->size) : '');
	}

	public function publisher() { return false; }

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
