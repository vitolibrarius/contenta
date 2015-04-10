<?php

namespace processor;

use \Processor as Processor;
use \Migrator as Migrator;
use \Config as Config;
use \Logger as Logger;

use utilities\FileWrapper as FileWrapper;
use utilities\MediaFilename as MediaFilename;

class UploadImport extends Processor
{
	const META_MEDIA = 'source';
	const META_MEDIA_FILENAME = 'source/filename';
	const META_MEDIA_NAME = 'source/name';
	const META_MEDIA_EXT = 'source/extension';
	const META_MEDIA_HASH = 'source/hash';
	const META_MEDIA_SIZE = 'source/size';

	const META_QUERY = 'search';

	function __construct($guid)
	{
		parent::__construct($guid);
	}

	private function renameMedia( $newFilename = null )
	{
		if ( is_null($newFilename) == false ) {
			$dest = $this->workingDirectory($newFilename);
			(file_exists($dest) == false) || unlink($dest) || die( "Unable to delete old $dest");
			rename($this->importFilePath(), $dest) || die( "Failed to rename to $dest");

			$this->setMeta(UploadImport::META_MEDIA_FILENAME, $newFilename);
			$this->setMeta(UploadImport::META_MEDIA_EXT, file_ext($newFilename));
		}
	}

	public function importFilePath()
	{
		$filename = $this->getMeta(UploadImport::META_MEDIA_FILENAME);
		return $this->workingDirectory($filename);
	}

	public function importFileWrapper()
	{
		$importFile = $this->importFilePath();
		if ( file_exists($importFile) ) {
			return FileWrapper::instance($importFile);
		}
		return false;
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
		$this->setMeta(UploadImport::META_MEDIA_NAME, file_ext_strip($filename));
		$this->setMeta(UploadImport::META_MEDIA_EXT, file_ext($filename));
		$this->setMeta(UploadImport::META_MEDIA_HASH, hash_file(HASH_DEFAULT_ALGO, $path));
		$this->setMeta(UploadImport::META_MEDIA_SIZE, filesize($path));

		if ( is_uploaded_file( $path ) ) {
			move_uploaded_file($path, $this->workingDirectory($workingFile));
		}
		else {
			rename($path, $this->workingDirectory($workingFile)) || die('Failed to move ' . $path);
		}

		if ($this->getMeta(UploadImport::META_MEDIA_EXT) == 'cbz' ) {
			$zip_wrapper = FileWrapper::instance($this->importFilePath());
			if ( $zip_wrapper->testWrapper() != null ) {
				// not a valid ZIP file
				$rar_wrapper = FileWrapper::force($this->importFilePath(), 'cbr');
				if ( $rar_wrapper->testWrapper() == null ) {
					// it is a valid RAR file, so rename it and reprocess
					Logger::logWarning($this->getMeta(UploadImport::META_MEDIA_FILENAME) . " identified as RAR", $this->type, $this->guid );
					$newFilename = file_ext_strip($workingFile) . '.cbr';
					$this->renameMedia( $newFilename );
				}
				else {
					Logger::logWarning($this->getMeta(UploadImport::META_MEDIA_FILENAME) . " not a valid archive", $this->type, $this->guid );
					return false;
				}
			}
		}

		if ($this->getMeta(UploadImport::META_MEDIA_EXT) == 'cbr' ) {
			return $this->convert_cbr();
		}

		return true;
	}

	public function processData()
	{
		Logger::logInfo( "processingData start", $this->type, $this->guid );

		$wrapper = FileWrapper::instance($this->importFilePath());
		$testStatus = $wrapper->testWrapper($errorCode);
		if ($errorCode == 0 && $this->getMeta(UploadImport::META_MEDIA_EXT) == 'cbz' ) {
			if ( $this->isMeta(UploadImport::META_QUERY) == false ) {
				$mediaFilename = new MediaFilename($this->getMeta(UploadImport::META_MEDIA_NAME), true);
				$this->setMeta( UploadImport::META_QUERY, $mediaFilename->updateFileMetaData());
			}
		}
		else {
			Logger::logInfo( "Media corrupt " . $testStatus, $this->type, $this->guid);
		}

		Logger::logInfo( "processingData end", $this->type, $this->guid);
	}

	public function convert_cbr()
	{
		$temp = $this->workingDirectory("cbr");
		if ( is_dir($temp) ) {
			destroy_dir($temp) || die( "unable to delete $temp" );
		}
		mkdir( $temp ) || die( "Unable to create $temp" );

		$filename = $this->getMeta(UploadImport::META_MEDIA_FILENAME);
		$wrapper = FileWrapper::instance($this->importFilePath());
		$success = $wrapper->testWrapper( $error );
		if ( $success != null && $error == 10) {
			// RARX_NOFILES, check for zip
			$zipFileList = zipFileList($this->importFilePath());
			if (is_array($zipFileList)) {
				Logger::logWarning( $filename . " identified as ZIP format" );
				$newFilename = file_ext_strip($filename) . '.cbz';
				$this->renameMedia( $newFilename );
				return true;
			}
		}
		else {
			$success = $wrapper->unwrapToDirectory( $temp );
			if ( $success == true )
			{
				$newFilename = file_ext_strip($filename) . '.cbz';
				$dest = $this->workingDirectory($newFilename);
				(file_exists($dest) == false) || unlink($dest) || die( "Unable to delete old $dest");
				$newWrapper = FileWrapper::createWrapperForSource($temp, $dest, 'cbz');
				if ( $newWrapper != false && $newWrapper->testWrapper() == null )
				{
					$this->setMeta(UploadImport::META_MEDIA_FILENAME, $newFilename);
					$this->setMeta(UploadImport::META_MEDIA_EXT, 'cbz');
					return true;
				}
			}
		}
		return false;
	}
}
