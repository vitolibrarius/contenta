<?php

namespace processor;

use \Processor as Processor;
use \Migrator as Migrator;
use \Config as Config;
use \Logger as Logger;

use utilities\Metadata as Metadata;
use utilities\FileWrapper as FileWrapper;

class ImportManager extends Processor
{
	const GUID = '60EB7A5B-DCBE-4A42-827F-180087F31620';
	const IMPORTS = 'imports';

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
}
