<?php

namespace processor;

use \Processor as Processor;
use \Migrator as Migrator;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;

use \Exception as Exception;
use exceptions\ImportMediaException as ImportMediaException;

use \interfaces\ProcessStatusReporter as ProcessStatusReporter;

use \model\media\Book as Book;

class EpubImporter extends UploadImport
{
	function __construct($guid = '')
	{
		if ( empty( $guid ) ) {
			$guid = uuid();
		}
		parent::__construct($guid);
		$root = Config::GetProcessing();
		$processingRoot = appendPath($root, "UploadImport" );
		makeRequiredDirectory($processingRoot, 'processing subdirectory for ' . "UploadImport" );

		$this->workingDir = appendPath($processingRoot, $guid);
	}

	public function readXML($data = '') {
		libxml_use_internal_errors(true);
		$xml = simplexml_load_string($data);
		$xmlErrors = libxml_get_errors();
		libxml_clear_errors();
		return $xml;
	}

	public function epubTOC() {
	}

	public function epubOPF() {
		$xml = false;
		$wrapper = $this->sourceFileWrapper();
		$content = $wrapper->wrapperContents();
		foreach( $content as $item ) {
			$originalExt = file_ext($item);
			if ( in_array(strtolower($originalExt), array('opf')) == true ) {
				$data = $wrapper->wrappedDataForName($item);
				$xml = $this->readXML($data);
			}
		}

		$meta = array();
		foreach ($xml->getNamespaces(true) as $namespace) {
			$ns_dc = $xml->metadata->children($namespace);
			foreach ($ns_dc as $key => $item) {
				if ( $key == "meta" ) {
					$newKey = null;
					$newValue = null;
					foreach($item->attributes() as $a => $b) {
						if ( $a == "name" ) {
							$newKey = $b->__toString();
						}
						else {
							$newValue = $b->__toString();
						}

						if ( $newKey != null && $newValue != null ) {
							$meta[$newKey] = $newValue;
							$newKey = null;
							$newValue = null;
						}
					}
				}
				else {
					$meta[$key] = $item->__toString();
				}
			}
		}
		return $meta;
	}

	public function processData(ProcessStatusReporter $reporter = null)
	{
		$wrapper = $this->sourceFileWrapper();
		$testStatus = $wrapper->testWrapper($errorCode);
		if ($errorCode != 0 ) {
			$this->setStatusMetaData( "MEDIA_CORRUPT" );
			Logger::logInfo( "Media corrupt " . $testStatus, __method__, $this->sourceFilename());
			return;
		}

		$this->setStatusMetaData( "Finishing Import" );

		try {
			$bookMeta = $this->epubOPF();
			$name = $bookMeta["title"];
			$desc = $bookMeta["description"];
			$author = $bookMeta["creator"];

			$epubType = Model::Named( "Media_Type" )->epub();
			$filename = $this->getMeta(UploadImport::META_MEDIA_FILENAME);
			$hash = $this->getMeta(UploadImport::META_MEDIA_HASH);
			$size = $this->getMeta(UploadImport::META_MEDIA_SIZE);

/**
	const id = 'id';
	const type_code = 'type_code';
	const filename = 'filename';
	const original_filename = 'original_filename';
	const checksum = 'checksum';
	const created = 'created';
	const size = 'size';
	const name = 'name';
	const author = 'author';
	const desc = 'desc';
	const pub_date = 'pub_date';
	const pub_order = 'pub_order';
*/
			list($book, $errors) = Model::Named( "Book" )->createObject( array(
				"mediaType" => $epubType,
				Book::filename => sanitize_filename($filename),
				Book::original_filename => $filename,
				Book::checksum => $hash,
				Book::size => $size,
				Book::name => $name,
				Book::desc => $desc,
				Book::author => $author
				)
			);
			if ( $book instanceof \model\media\BookDBO ) {
				$thumbname = $wrapper->firstImageThumbnailName();
				$image = $wrapper->wrappedThumbnailForName($thumbname, 200, 200);
				$thumbnailFile = "Thumbnail." . file_ext($thumbname);
				$path = hashedPath("book", $book->pkValue(), $thumbnailFile);
				file_put_contents($path, $image);

				if ( rename( $this->importFilePath(), $book->contentaPath()) ) {
					$this->setPurgeOnExit(true);
				}
				else {
					$errors= error_get_last();
					Logger::logError(  "MOVE ERROR: " . $errors['type'] . ' - ' . $errors['message'], __method__, $this->sourceFilename());
					return false;
				}
			}
			else {
				throw new ImportMediaException( "Create Media error " . var_export($errors, true));
			}
		}
		catch ( ImportMediaException $me ) {
			Logger::logError( $me->getMessage(), __method__, $this->sourceFilename());
			$this->setStatusMetaData( $me->getMessage() );
		}
		catch ( \Exception $e ) {
			Logger::logException( $e );
			$this->setStatusMetaData( "IMPORTER_ERROR" );
		}
	}
}
