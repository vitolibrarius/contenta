<?php

namespace processor;

use \Processor as Processor;
use \Migrator as Migrator;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;

use utilities\FileWrapper as FileWrapper;
use utilities\MediaFilename as MediaFilename;
use utilities\Metadata as Metadata;
use connectors\ComicVineConnector as ComicVineConnector;

use model\Endpoint_Type as Endpoint_Type;
use model\PublicationDBO as PublicationDBO;

class UploadImport extends Processor
{
	const META_MEDIA = 'source';
	const META_MEDIA_FILENAME = 'source/filename';
	const META_MEDIA_NAME = 'source/name';
	const META_MEDIA_EXT = 'source/extension';
	const META_MEDIA_HASH = 'source/hash';
	const META_MEDIA_SIZE = 'source/size';

	const META_QUERY = 'search';
	const META_QUERY_CLEAN = 'search/clean';
	const META_QUERY_YEAR = 'search/year';
	const META_QUERY_ISSUE = 'search/issue';
	const META_QUERY_NAME = 'search/name';

	const META_STATUS = 'status';

	const META_THUMBNAIL = 'thumbnail';

	const META_RESULTS = 'results';
	const META_RESULTS_ISSUES = 'results/issues';
	const META_RESULTS_VOLUME = 'results/volume';

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

	public function sourceFilename()
	{
		return $this->getMeta(UploadImport::META_MEDIA_FILENAME);
	}

	public function sourceMetaData()
	{
		return $this->getMeta(UploadImport::META_MEDIA);
	}

	public function searchMetaData()
	{
		return $this->getMeta(UploadImport::META_QUERY);
	}

	public function statusMetaData()
	{
		return $this->getMeta(UploadImport::META_STATUS);
	}

	public function sourceFileExtension()
	{
		$ext = $this->getMeta(UploadImport::META_MEDIA_EXT);
		if ( is_null($ext) ) {
			$filename = $this->getMeta(UploadImport::META_MEDIA_FILENAME);
			if ( is_null($filename) ) {
				return '';
			}
			$ext = file_ext($filename);
		}
		return $ext;
	}

	public function sourceFileWrapper()
	{
		$importFile = $this->importFilePath();
		if ( file_exists($importFile) ) {
			return FileWrapper::instance($importFile);
		}
		return false;
	}

	private function generateThumbnail()
	{
		$wrapper = $this->sourceFileWrapper();
		if ( $wrapper != null ) {
			$imageFile = $wrapper->firstImageThumbnailName();
			if ( is_null($imageFile) == false ) {
				$mimeType = 'image/' . file_ext($imageFile);
				$image = $wrapper->wrappedThumbnailForName($imageFile, 100, 100);

				$thumbailFile = "Thumbnail." . file_ext($imageFile);
				file_put_contents($this->workingDirectory($thumbailFile), $image);
				$this->setMeta(UploadImport::META_THUMBNAIL, $thumbailFile);
			}
		}
	}

	public function setMediaForImport( $path = null, $filename = null )
	{
		if ( is_null($path) || is_null($filename) ) {
			Logger::logError("Unable to import -null- for media file", $this->type, $this->sourceFilename());
			return false;
		}

		if ( file_exists($path) == false ) {
			Logger::logError("No media file at " . $path, $this->type, $this->sourceFilename());
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
					Logger::logWarning($this->getMeta(UploadImport::META_MEDIA_FILENAME) . " identified as RAR", $this->type, $this->sourceFilename() );
					$newFilename = file_ext_strip($workingFile) . '.cbr';
					$this->renameMedia( $newFilename );
				}
				else {
					Logger::logWarning($this->getMeta(UploadImport::META_MEDIA_FILENAME) . " not a valid archive", $this->type, $this->sourceFilename() );
					return false;
				}
			}
		}

		if ($this->getMeta(UploadImport::META_MEDIA_EXT) == 'cbr' ) {
			return $this->convert_cbr();
		}

		Logger::logError("Accepting import", $this->type, $this->sourceFilename());
		return true;
	}

	public function resetSearchCriteria()
	{
		$this->setMeta(UploadImport::META_QUERY, null);
		$this->setMeta(UploadImport::META_RESULTS, null);

		$mediaFilename = new MediaFilename($this->getMeta(UploadImport::META_MEDIA_NAME), true);
		$this->setMeta( UploadImport::META_QUERY, $mediaFilename->updateFileMetaData());
		return true;
	}

	function setSearchCriteria($seriesname = null, $issue = null, $year = null)
	{
		$this->setMeta(UploadImport::META_RESULTS, null);

		if (isset($seriesname)) {
			$this->setMeta(UploadImport::META_QUERY_NAME, $seriesname);
		}

		if (isset($issue)) {
			$this->setMeta(UploadImport::META_QUERY_ISSUE, $issue);
		}

		if (isset($year)) {
			$this->setMeta(UploadImport::META_QUERY_YEAR, $year);
		}

		return true;
	}

	public function processSearch()
	{
		// update the query values, but only if they are not already set
		if ( $this->isMeta(UploadImport::META_QUERY) == false ) {
			$mediaFilename = new MediaFilename($this->getMeta(UploadImport::META_MEDIA_NAME), true);
			$this->setMeta( UploadImport::META_QUERY, $mediaFilename->updateFileMetaData());
		}

		$ep_model = Model::Named('Endpoint');
		$points = $ep_model->allForTypeCode(Endpoint_Type::ComicVine);
		if ($points == false || count($points) == 0) {
			$this->setMeta( UploadImport::META_STATUS, "NO_ENDPOINTS" );
			Logger::logInfo( "No ComicVine Endpoints defined ", $this->type, $this->sourceFilename());
			return false;
		}

		$connection = new ComicVineConnector($points[0]);
		$issue = $connection->issue_searchFilteredForSeriesYear(
			$this->getMeta(UploadImport::META_QUERY_ISSUE),
			$this->getMeta(UploadImport::META_QUERY_NAME),
			$this->getMeta(UploadImport::META_QUERY_YEAR)
		);
		if ( $issue == false )
		{
			$this->setMeta( UploadImport::META_STATUS, "NO_MATCHES" );
			Logger::logInfo( "No ComicVine matches, unable to import", $this->type, $this->sourceFilename());
			return false;
		}

		$this->setMeta( UploadImport::META_RESULTS_ISSUES, $issue );
		if (count($issue) > 1) {
			$this->setMeta( UploadImport::META_STATUS, "MULTIPLE_MATCHES" );
			Logger::logInfo( "Multiple ComicVine matches, unable to import", $this->type, $this->sourceFilename());
			return false;
		}

		$this->setMeta( UploadImport::META_STATUS, "COMICVINE_SUCCESS" );
		return true;
	}

	public function selectMatchingIssue( $issueId = null )
	{
		if ( is_string($issueId) && strlen($issueId) > 0 ) {
			$issues = $this->getMeta( UploadImport::META_RESULTS_ISSUES );
			if ( is_array($issues) ) {
				$result = array_filterForKeyValue( $issues, array( "id"=>$issueId));
				$this->setMeta( UploadImport::META_RESULTS_ISSUES, $result );
				return true;
			}
		}
		return false;
	}

	public function processData()
	{
		Logger::logInfo( "processingData start", $this->type, $this->sourceFilename() );

		$wrapper = FileWrapper::instance($this->importFilePath());
		$testStatus = $wrapper->testWrapper($errorCode);
		if ($errorCode != 0 || $this->getMeta(UploadImport::META_MEDIA_EXT) != 'cbz' ) {
			$this->setMeta( UploadImport::META_STATUS, "MEDIA_CORRUPT" );
			Logger::logInfo( "Media corrupt " . $testStatus, $this->type, $this->sourceFilename());
			return;
		}

		if ($this->hasResultsMetadata() == false && $this->processSearch() == false ) {
			$this->setMeta( UploadImport::META_STATUS, "NO_METADATA_FOUND");
			Logger::logError( "No media metadata found for importing", $this->type, $this->sourceFilename());
			$this->generateThumbnail();
			return;
		}

		$issue = $this->getMeta( UploadImport::META_RESULTS_ISSUES );
		if (count($issue) > 1) {
			$this->setMeta( UploadImport::META_STATUS, "MULTIPLE_METADATA");
			Logger::logError( "Multiple media metadata found for importing", $this->type, $this->sourceFilename());
			$this->generateThumbnail();
			return;
		}

		$matchingIssue = $issue[0];

		$ep_model = Model::Named('Endpoint');
		$points = $ep_model->allForTypeCode(Endpoint_Type::ComicVine);
		if ($points == false || count($points) == 0) {
			$this->setMeta( UploadImport::META_STATUS, "NO_ENDPOINTS" );
			Logger::logInfo( "No ComicVine Endpoints defined ", $this->type, $this->sourceFilename());
			return false;
		}

		Logger::logInfo( "Found match importing "
			. array_valueForKeypath( "volume/name", $matchingIssue)
			. " - " . array_valueForKeypath( "issue_number", $matchingIssue)
			. " - " . array_valueForKeypath( "cover_date", $matchingIssue)
			 , $this->type, $this->sourceFilename());

		$importer = Processor::Named('ComicVineImporter', $this->sourceFilename() );
		$importer->setEndpoint($points[0]);

		$importer->enqueue_series( array(
			"xid" => array_valueForKeypath( "volume/id", $matchingIssue),
			"name" => array_valueForKeypath( "volume/name", $matchingIssue),
			), true, true
		);

		$importer->enqueue_publication( array(
			"xid" => array_valueForKeypath( "id", $matchingIssue),
			"name" => array_valueForKeypath( "name", $matchingIssue),
			"issue" => array_valueForKeypath( "issue_number", $matchingIssue),
			), true, true
		);

		try {
			$importer->processData();

			$cbzType = Model::Named( "Media_Type" )->cbz();
			$pub_xid = array_valueForKeypath( "id", $matchingIssue);
			$publication = Model::Named("Publication")->objectForExternal($pub_xid, $importer->endpointTypeCode());
			$filename = $this->getMeta(UploadImport::META_MEDIA_FILENAME);
			$hash = $this->getMeta(UploadImport::META_MEDIA_HASH);
			$size = $this->getMeta(UploadImport::META_MEDIA_SIZE);

			$media = Model::Named( "Media" )->create( $publication, $cbzType, $filename, $hash, $size );
			if ( $media instanceof model\MediaDBO ) {
				if ( rename( $this->importFilePath(), $media->contentaPath()) ) {
					@copy( $this->workingDirectory(Metadata::DefaultFilename), $media->contentaMetadataPath());
					$this->setPurgeOnExit(true);
				}
				else {
					$errors= error_get_last();
					Logger::logError(  "MOVE ERROR: " . $errors['type'] . ' - ' . $errors['message'], $this->type, $this->sourceFilename());
					return false;
				}
			}
			else {
				Logger::logError( "Media error " . var_export($media, true), $this->type, $this->sourceFilename());
			}
		}
		catch ( \Exception $e ) {
			Logger::logException( $e );
			$this->setMeta( UploadImport::META_STATUS, "IMPORTER_ERROR" );
			return;
		}

		Logger::logInfo( "processingData end", $this->type, $this->sourceFilename());
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
				Logger::logWarning( $filename . " identified as ZIP format", $this->type, $this->sourceFilename() );
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
					$this->setMeta(UploadImport::META_MEDIA_HASH, $newWrapper->wrapperHash());
					return true;
				}
			}
		}
		return false;
	}

	/** comic vine metadata */
	public function hasResultsMetadata()
	{
		$issues = $this->getMeta(UploadImport::META_RESULTS_ISSUES);
		return (is_array($issues) && count($issues) > 0);
	}

	public function issueMetadata()
	{
		return $this->getMeta(UploadImport::META_RESULTS_ISSUES);
	}

	public function volumeMetadata()
	{
		return $this->getMeta(UploadImport::META_RESULTS_VOLUME);
	}
}
