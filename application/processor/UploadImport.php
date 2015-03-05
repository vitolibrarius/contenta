<?php

namespace processor;

use \Processor as Processor;
use \Migrator as Migrator;
use \Config as Config;
use \Logger as Logger;


class UploadImport extends Processor
{
	const META_MEDIA = 'source';
	const META_MEDIA_FILENAME = 'source/filename';
	const META_MEDIA_NAME = 'source/name';
	const META_MEDIA_HASH = 'source/hash';
	const META_MEDIA_SIZE = 'source/size';

	public static function DomainName()
	{
		return "Import";
	}

	function __construct($guid)
	{
		parent::__construct($guid);
	}

	public function setMediaForImport( $path = null, $filename = null )
	{
		if ( is_null($path) || is_null($filename) ) {
			Logger::logError("Unable to import -null- for media file", __CLASS__, __METHOD__);
			return false;
		}

		if ( file_exists($path) == false ) {
			Logger::logError("No media file at " . $path, __CLASS__, __METHOD__);
			return false;
		}

		$this->createWorkingDirectory();
		$workingFile = sanitize_filename($filename);

		$this->setMeta(UploadImport::META_MEDIA_FILENAME, $workingFile);
		$this->setMeta(UploadImport::META_MEDIA_NAME, $filename);
		$this->setMeta(UploadImport::META_MEDIA_HASH, hash_file(HASH_DEFAULT_ALGO, $path));
		$this->setMeta(UploadImport::META_MEDIA_SIZE, filesize($path));

		if ( is_uploaded_file( $path ) ) {
			move_uploaded_file($path, $this->workingDirectory($workingFile));
		}
		else {
			rename($path, $this->workingDirectory($workingFile)) || die('Failed to move ' . $path);
		}

		return true;
	}

	public function processData()
	{
	}
}
