<?php

namespace processor;

use \Processor as Processor;
use \Migrator as Migrator;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \Session as Session;

use utilities\Metadata as Metadata;
use utilities\FileWrapper as FileWrapper;

class ImportManager extends Processor
{
	const GUID = '60EB7A5B-DCBE-4A42-827F-180087F31620';
	const IMPORTS = 'imports';

	public static function ImportHashPath( $hash = null )
	{
		if ( is_string($hash) && strlen($hash) > 0) {
			$root = Config::GetProcessing();
			$path = appendPath($root, "UploadImport", $hash );
			if ( is_dir($path) ) {
				return $path;
			}
		}
		return null;
	}

	public static function IsEditable( $hash = null )
	{
		$path = ImportManager::ImportHashPath( $hash );
		if ( is_string($path) && strlen($path) > 0) {
			// ensure no processors are already running
			$job_model = Model::Named('Job_Running');
			$running = $job_model->allForProcessorGUID( null, $hash);
			if ( $running == false ) {
				return true;
			}
		}
		else {
			Session::addNegativeFeedback(Localized::Get("Imports", 'NotEditable'));
		}
		return false;
	}

	function __construct($guid)
	{
		parent::__construct(ImportManager::GUID);
	}

	private function uploadDir()
	{
		$root = Config::GetProcessing();
		return appendPath($root, "UploadImport" );
	}

	public function processData()
	{
		if ( isset($this->currentItems) == false) {
			$importQueue = $this->uploadDir();

			$current = array();
			$old_list = $this->getMeta( ImportManager::IMPORTS );
			if ( isset($old_list) == false || $old_list == false ) {
				$old_list = array();
			}

			if ( is_dir($importQueue) )
			{
				foreach (scandir($importQueue) as $dir) {
					if ($dir == '.' || $dir == '..') continue;

					$fullpath = appendPath($importQueue, $dir);
					if ( is_dir($fullpath) ) {
						if ( isset($old_list[$dir]) == true) {
							$current[$dir] = $old_list[$dir];
							unset( $old_list[$dir] );
						}
						else {
							$process_meta = Metadata::forDirectory($fullpath);
							$current[$dir] = $process_meta->getMeta(UploadImport::META_MEDIA);
						}
					}
				}

				uasort($current, function($a, $b) {
					return strnatcmp($a['filename'], $b['filename']);
				});
			}
			$this->setMeta( ImportManager::IMPORTS, $current );
			$this->currentItems = $current;
		}
		return $this->currentItems;
	}

	function metadataFor($processKey = null) {
		if ( is_string($processKey) ) {
			$allData = $this->processData();
			if ( is_array($allData) && isset($allData[$processKey]) ) {
				return $allData[$processKey];
			}
		}
		return array();
	}

	function chunkedArray() {
		$array = $this->processData();
		return array_chunk ( $array, 10, true);
	}

	function totalChunks() {
		return count($this->chunkedArray());
	}

	function correctChunkIndex($chunkNum = 0) {
		return min( max(0, $chunkNum), ($this->totalChunks() - 1) );
	}

	function chunk($chunkNum = 0) {
		$chunked = $this->chunkedArray();
		$idx = $this->correctChunkIndex($chunkNum);
		return $chunked[$idx];
	}

	function chunkNumberFor($processKey = null) {
		if ( is_null( $processKey ) == false ) {
			$chunks = $this->chunkedArray();
			foreach( $chunks as $idx => $subchunk ) {
				if ( isset($subchunk[$processKey]) ) {
					return $idx;
				}
			}
		}
		return 0;
	}

	function firstThumbnail($processKey = null)
	{
		$image = null;
		$mimeType = null;

		if ( is_null( $processKey ) == false ) {
			$importQueue = $this->uploadDir();
			$processDir = appendPath($importQueue, $processKey);
			if ( is_dir($processDir) ) {
				$process_meta = Metadata::forDirectory($processDir);
				if ( $process_meta->isMeta( UploadImport::META_THUMBNAIL ) ) {
					$thumbailFile = $process_meta->getMeta(UploadImport::META_THUMBNAIL);
					if ( file_exists($thumbailFile) ) {
						$mimeType = 'image/' . file_ext($thumbailFile);
						$image = file_get_contents(appendPath($processDir, $thumbailFile));
					}
				}

				if ( is_null($image)) {
					$filename = $process_meta->getMeta(UploadImport::META_MEDIA_FILENAME);
					$wrapper = FileWrapper::instance(appendPath($importQueue, $processKey, $filename));
					if ( $wrapper != null ) {
						$imageFile = $wrapper->firstImageThumbnailName();
						if ( is_null($imageFile) == false ) {
							$mimeType = 'image/' . file_ext($imageFile);
							$image = $wrapper->wrappedThumbnailForName($imageFile, 100, 100);

							$thumbailFile = "Thumbnail." . file_ext($imageFile);
							file_put_contents(appendPath($processDir, $thumbailFile), $image);
							$process_meta->setMeta(UploadImport::META_THUMBNAIL, $thumbailFile);
						}
					}
				}
			}
		}

		return array( $image, $mimeType );
	}

	function fileWrapper($processKey = null)
	{
		if ( is_null( $processKey ) == false ) {
			$importQueue = $this->uploadDir();
			$processDir = appendPath($importQueue, $processKey);
			if ( is_dir($processDir) ) {
				$process_meta = Metadata::forDirectory($processDir);
				$filename = $process_meta->getMeta(UploadImport::META_MEDIA_FILENAME);
				return FileWrapper::instance(appendPath($importQueue, $processKey, $filename));
			}
		}

		return null;
	}

	function purgeUnprocessed($processKey = null)
	{
		if ( is_null( $processKey ) == false ) {
			$importQueue = $this->uploadDir();
			$processDir = appendPath($importQueue, $processKey);
			if ( is_dir($processDir) ) {
				return destroy_dir($processDir);
			}
		}

		return false;
	}
}
