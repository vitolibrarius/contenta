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
use utilities\Stopwatch as Stopwatch;

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
							$current[$dir]['status'] = $process_meta->getMeta('status');
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
					$thumbnailFile = $process_meta->getMeta(UploadImport::META_THUMBNAIL);
					$thumbnailPath = appendPath($processDir, $thumbnailFile);
					if ( file_exists($thumbnailPath) ) {
						$mimeType = 'image/' . file_ext($thumbnailFile);
						$image = file_get_contents($thumbnailPath);
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

							$thumbnailFile = "Thumbnail." . file_ext($imageFile);
							file_put_contents(appendPath($processDir, $thumbnailFile), $image);
							$process_meta->setMeta(UploadImport::META_THUMBNAIL, $thumbnailFile);
						}
					}
				}
			}
		}
		return array( $image, $mimeType );
	}

	function indexedThumbnail($processKey = null, $idx = 0, $width = 100, $height = 100)
	{
		$image = null;
		$mimeType = null;

		if ( is_null( $processKey ) == false ) {
			$importQueue = $this->uploadDir();
			$processDir = appendPath($importQueue, $processKey);
			if ( is_dir($processDir) ) {
				$process_meta = Metadata::forDirectory($processDir);
				$indexKey = appendPath( UploadImport::META_INDEXED_THUMBNAIL, $idx."_".$width."x".$height );
				if ( $process_meta->isMeta( $indexKey ) ) {
					$thumbnailFile = $process_meta->getMeta($indexKey);
					$thumbnailPath = appendPath($processDir, $thumbnailFile);
					if ( file_exists($thumbnailPath) ) {
						$mimeType = 'image/' . file_ext($thumbnailFile);
						$image = file_get_contents($thumbnailPath);
					}
				}

				if ( is_null($image)) {
					$filename = $process_meta->getMeta(UploadImport::META_MEDIA_FILENAME);
					$wrapper = FileWrapper::instance(appendPath($importQueue, $processKey, $filename));
					if ( $wrapper != null ) {
						$filelist = $wrapper->wrapperContents();
						$intDex = intval($idx);

						if (($intDex >= 0) && ($intDex < count($filelist))) {
							$imageFile = $filelist[$intDex];
							$mimeType = 'image/' . file_ext($filelist[$intDex]);
							$image = $wrapper->wrappedThumbnailForName($imageFile, $width, $height);

							$thumbnailFile =  $idx."_".$width."x".$height.".".file_ext($imageFile);
							file_put_contents(appendPath($processDir, $thumbnailFile), $image);
							$process_meta->setMeta($indexKey, $thumbnailFile);
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
