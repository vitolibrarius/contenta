<?php

namespace processor;

use \Processor as Processor;
use \Migrator as Migrator;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \Session as Session;
use \Localized as Localized;
use \Metadata as Metadata;

use utilities\FileWrapper as FileWrapper;
use utilities\Stopwatch as Stopwatch;
use utilities\Lock as Lock;

class ImportManager extends Processor
{
	const GUID = '60EB7A5B-DCBE-4A42-827F-180087F31620';
	const PENDING = 'pending';
	const IMPORTS = 'imports';

    var $sorted_pending = null;

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

	public static function UpdateStatus( $hash = null, $status = "UNKNOWN" )
	{
		$path = ImportManager::ImportHashPath( $hash );
		if ( is_string($path) && strlen($path) > 0) {
			$root = Config::GetProcessing();
			$path = appendPath($root, "ImportManager", ImportManager::GUID );
			if ( is_dir($path) ) {
				$metafile = Metadata::forDirectory($path, Metadata::TYPE_SQLITE);
				$metafile->setMeta( appendPath( ImportManager::PENDING, $hash ), $status);
			}
		}
	}

	function __construct($guid)
	{
		parent::__construct(ImportManager::GUID);
	}

	function __destruct()
	{
		if ( $this->metadata()->metaCount( ImportManager::PENDING ) == 0 ) {
			$this->purgeOnExit = true;
		}
		parent::__destruct();
	}

	private function uploadDir()
	{
		$root = Config::GetProcessing();
		return appendPath($root, "UploadImport" );
	}

	public function processData()
	{
		if ( is_null($this->sorted_pending) ) {
			$lockfile = $this->workingDirectory( "updating.lock" );
			$lock = new Lock($lockfile);
			if (($pid = $lock->lock()) !== false) {
				$oldPending = $this->getMeta( ImportManager::PENDING );
				if ( is_array($oldPending) == false) {
					$oldPending = array();
				}

				$importQueueDirectory = $this->uploadDir();
				if ( is_dir($importQueueDirectory) )
				{
					foreach (scandir($importQueueDirectory) as $dir) {
						if ($dir == '.' || $dir == '..') continue;

						if (isset($oldPending[$dir])) {
							unset( $oldPending[$dir] );
						}

						$fullpath = appendPath($importQueueDirectory, $dir);
						$metapath = appendPath( ImportManager::PENDING, $dir );
						if ( is_dir($fullpath) && $this->isMeta($metapath) == false) {
							$status = $this->getMeta( appendPath(ImportManager::PENDING, $dir), "UNKNOWN");
							$this->setMeta( appendPath( ImportManager::PENDING, $dir ), $status);
						}
					}

					// remove from queue since the directory is gone
					foreach( $oldPending as $dir => $status ) {
						$this->setMeta( appendPath( ImportManager::PENDING, $dir ), null);
						$this->setMeta( appendPath( ImportManager::IMPORTS, $dir ), null);
					}
				}

				$lock->unlock();
			}

			$this->sorted_pending = $this->getMeta( ImportManager::PENDING, array());
			ksort($this->sorted_pending);
		}

		return $this->sorted_pending;
	}

	function metadataFor($processKey = null) {
		if ( is_string($processKey) ) {
			$status = $this->getMeta( appendPath( ImportManager::PENDING, $processKey ), "UNKNOWN");
			$allData = $this->getMeta( appendPath( ImportManager::IMPORTS, $processKey));
			if ( is_array($allData) ) {
				$allData['status'] = $status;
				return $allData;
			}

			// data not loaded yet?
			$fullpath = appendPath($this->uploadDir(), $processKey);
			if (is_dir($fullpath)) {
				$process_meta = Metadata::forDirectory($fullpath);
				$allData = $process_meta->getMeta(UploadImport::META_MEDIA);
				if ( is_array($allData) ) {
					$status = $process_meta->getMeta('status', "UNKNOWN");
					$this->setMeta( appendPath( ImportManager::PENDING, $processKey), $status);
					$this->setMeta( appendPath( ImportManager::IMPORTS, $processKey), $allData );
					$allData['status'] = $status;
					return $allData;
				}
			}
		}
		return array();
	}

	function clearMetadataFor($processKey = null) {
		if ( is_string($processKey) ) {
			$this->setMeta( appendPath(ImportManager::IMPORTS, $processKey), null );
		}
	}


	function chunkedArray() {
		$array = $this->processData();
		return array_chunk ( $array, 8, true);
	}

	function totalChunks() {
		return count($this->chunkedArray());
	}

	function correctChunkIndex($chunkNum = 0) {
		return min( max(0, $chunkNum), ($this->totalChunks() - 1) );
	}

	function chunk($chunkNum = 0) {
		$allChunks = $this->chunkedArray();
		$idx = $this->correctChunkIndex($chunkNum);
		$list = ($idx < 0 ? array() : $allChunks[$idx]);
		$chunkMeta = array();
		foreach( $list as $hash => $status ) {
			$chunkMeta[$hash] = $this->metadataFor( $hash );
		}
		return $chunkMeta;
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
						$filelist = $wrapper->imageContents();
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
