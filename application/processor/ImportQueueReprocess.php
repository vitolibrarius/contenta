<?php

namespace processor;

use \Processor as Processor;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \Localized as Localized;
use \Metadata as Metadata;

use \model\jobs\Job_Type as Job_Type;
use \model\jobs\Job_Running as Job_Running;
use \model\jobs\Job as Job;

class ImportQueueReprocess extends Processor
{
	const GUID = '5BB0BCDB-D8D9-4456-875A-CD0B1F208B61';

	function __construct($guid = null)
	{
		parent::__construct(ImportQueueReprocess::GUID);
	}

	private function uploadDir()
	{
		$root = Config::GetProcessing();
		return appendPath($root, "UploadImport" );
	}

	public function processData()
	{
		$itemCount = 0;

		$importQueueDirectory = $this->uploadDir();
		if ( is_dir($importQueueDirectory) ) {
			foreach (scandir($importQueueDirectory) as $processKey) {
				if ($processKey == '.' || $processKey == '..') continue;

				/**
				 * stop processing if the system is busy
				 */
				$running = Model::Named("Job_Running")->allObjects();
				if (count($running) >= 6 || $itemCount >= 5) {
					break;
				}

				$fullpath = appendPath($importQueueDirectory, $processKey);
				if ( is_dir($fullpath) ) {
					$process_meta = Metadata::forDirectory($fullpath);
					$status = $process_meta->getMeta(UploadImport::META_STATUS, "UNKNOWN");

					if ( "BUSY_NO_SEARCH" == $status ) {
						$importer = Processor::Named('UploadImport', $processKey);
						if ($importer->resetSearchCriteria() == true) {
							$importer->processData();
							$itemCount++;
						}
						else {
							Logger::logError( 'Failed to reset metadata for ' . $processKey );
						}
					}
				}
			}
		}
	}
}
