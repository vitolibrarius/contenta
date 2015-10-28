<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use model\Media_Type as Media_Type;
use utilities\FileWrapper as FileWrapper;

class MediaDBO extends DataObject
{
	public $publication_id;
	public $type_id;
	public $filename;
	public $original_file;
	public $created;
	public $checksum;
	public $size;

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

	public function mediaType() {
		if ( isset($this->type_id) ) {
			return Model::Named("Media_Type")->objectForId($this->type_id);
		}
		return false;
	}

	public function publication() {
		if ( isset($this->publication_id) ) {
			$model = Model::Named('Publication');
			return $model->objectForId($this->publication_id);
		}
		return false;
	}

	public function __toString()
	{
		return $this->displayName() . ' (' . $this->pkValue() . ') ';
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
