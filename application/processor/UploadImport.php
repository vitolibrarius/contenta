<?php

namespace processor;

use \Processor as Processor;
use \Migrator as Migrator;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \Metadata as Metadata;

use processor\ImportManager as ImportManager;
use utilities\FileWrapper as FileWrapper;
use utilities\MediaFilename as MediaFilename;
use connectors\ComicVineConnector as ComicVineConnector;

use exceptions\ImportMediaException as ImportMediaException;
use \interfaces\ProcessStatusReporter as ProcessStatusReporter;

use \model\network\Endpoint_Type as Endpoint_Type;
use \model\media\PublicationDBO as PublicationDBO;
use \model\media\Media as Media;

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
	const META_QUERY_CVID = 'search/comicvineid';

	const META_STATUS = 'status';
	const META_IS_AUTOMATED = 'isAutomatedSelection';

	const META_THUMBNAIL = 'thumbnail';
	const META_INDEXED_THUMBNAIL = 'images';

	const META_RESULTS = 'results';
	const META_RESULTS_ISSUES = 'results/issues';
	const META_RESULTS_VOLUME = 'results/volume';

	function __construct($guid)
	{
		parent::__construct($guid);
		$this->setJobDescription( "Processing " . $this->sourceFilename() );
	}

	private function renameMedia( $newFilename = null )
	{
		if ( is_null($newFilename) == false ) {
			$dest = $this->workingDirectory($newFilename);
			(file_exists($dest) == false) || safe_unlink($dest) || die( "Unable to delete old $dest");
			rename($this->importFilePath(), $dest) || die( "Failed to rename to $dest");

			$this->setMeta(UploadImport::META_MEDIA_FILENAME, $newFilename);
			$this->setMeta(UploadImport::META_MEDIA_EXT, file_ext($newFilename));
		}
	}

	public function setIsAutomatedImport($yesNo = true)
	{
		$this->setMetaBoolean( UploadImport::META_IS_AUTOMATED, $yesNo );
	}

	/** automated mode by default */
	public function isAutomatedImport()
	{
		return $this->getMetaBoolean(UploadImport::META_IS_AUTOMATED, true);
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

	public function setStatusMetaData($status = null)
	{
		// 		Logger::logInfo("setStatusMetaData( $status ) ", __method__,  $this->sourceFilename());
		if ( is_string($status) && strlen($status) > 0) {
			$this->setMeta( UploadImport::META_STATUS, $status );
			ImportManager::UpdateStatus( $this->guid, $status );
		}
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

	public function generateThumbnails()
	{
		$wrapper = $this->sourceFileWrapper();
		if ( $wrapper != null ) {
			$this->setMeta("sourceFileWrapper", get_class($wrapper));

			// small thumbnail
			$thumbnail = $this->getMeta( UploadImport::META_THUMBNAIL );
			if ( is_string($thumbnail) == false || file_exists($this->workingDirectory($thumbnail)) == false ) {
				$imageFile = $wrapper->firstImageThumbnailName();
				$this->setMeta("firstImageThumbnailName", $imageFile);
				if ( is_null($imageFile) == false ) {
					$mimeType = 'image/' . file_ext($imageFile);
					$image = $wrapper->wrappedThumbnailForName($imageFile, 100, 100);

					$thumbnailFile = "Thumbnail." . file_ext($imageFile);
					file_put_contents($this->workingDirectory($thumbnailFile), $image);
					$this->setMeta(UploadImport::META_THUMBNAIL, $thumbnailFile);
				}
			}

			// generate a bigger image for the first detail inspect page
			$idx = 0;
			$indexKey = $idx . "_200x200";
			$indexPath = appendPath( UploadImport::META_INDEXED_THUMBNAIL, $indexKey );
			$thumbnail = $this->getMeta( $indexPath );
			if ( is_string($thumbnail) == false || file_exists($this->workingDirectory($thumbnail)) == false ) {
				$filelist = $wrapper->imageContents();
				$imageFile = $filelist[$idx];
				if ( is_null($imageFile) == false ) {
					$mimeType = 'image/' . file_ext($imageFile);
					$image = $wrapper->wrappedThumbnailForName($imageFile, 200, 200);

					$thumbnailFile = $indexKey . "." . file_ext($imageFile);
					file_put_contents($this->workingDirectory($thumbnailFile), $image);
					$this->setMeta($indexKey, $thumbnailFile);
				}
			}
		}
	}

	public function setMediaForImport( $path = null, $filename = null )
	{
		if ( is_null($path) || is_null($filename) ) {
			throw new ImportMediaException( "NULL_FILENAME" );
		}

		if ( file_exists($path) == false ) {
			throw new ImportMediaException( "FILE_DOES_NOT_EXIST" );
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
		else if (rename($path, $this->workingDirectory($workingFile)) == false ) {
			throw new ImportMediaException('Failed to move ' . $path);
		}

		if ($this->getMeta(UploadImport::META_MEDIA_EXT) == 'cbz' ) {
			$zip_wrapper = FileWrapper::instance($this->importFilePath());
			if ( $zip_wrapper->testWrapper() != null ) {
				// not a valid ZIP file
				$rar_wrapper = FileWrapper::force($this->importFilePath(), 'cbr');
				if ( $rar_wrapper->testWrapper() == null ) {
					// it is a valid RAR file, so rename it and reprocess
					Logger::logWarning($this->getMeta(UploadImport::META_MEDIA_FILENAME) . " identified as RAR", __method__, $this->sourceFilename() );
					$newFilename = file_ext_strip($workingFile) . '.cbr';
					$this->renameMedia( $newFilename );
				}
				else {
					throw new ImportMediaException( "FILE_CORRUPT" );
				}
			}
		}

		if ($this->getMeta(UploadImport::META_MEDIA_EXT) == 'cbr' ) {
			return $this->convert_cbr();
		}

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

	function setSearchCriteria($seriesname = null, $issue = null, $year = null, $comicvineid = null)
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

		if (isset($comicvineid)) {
			$this->setMeta(UploadImport::META_QUERY_CVID, $comicvineid);
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
			$this->setStatusMetaData( "NO_ENDPOINTS" );
			Logger::logInfo( "No ComicVine Endpoints defined ", __method__, $this->sourceFilename());
			return false;
		}

		$connection = new ComicVineConnector($points[0]);
		if ( $this->isMeta(UploadImport::META_QUERY_CVID) == true ) {
			$issue = $connection->issue_search(
				null,
				$this->getMeta(UploadImport::META_QUERY_CVID),
				null,
				null,
				$this->getMeta(UploadImport::META_QUERY_YEAR),
				$this->getMeta(UploadImport::META_QUERY_ISSUE)
			);
		}
		else {
			$issue = $connection->issue_searchFilteredForSeriesYear(
				$this->getMeta(UploadImport::META_QUERY_ISSUE),
				$this->getMeta(UploadImport::META_QUERY_NAME),
				$this->getMeta(UploadImport::META_QUERY_YEAR)
			);
		}

		if ( $issue == false )
		{
			$this->setStatusMetaData( "NO_MATCHES" );
			return false;
		}

		$this->setMeta( UploadImport::META_RESULTS_ISSUES, $issue );
		if (count($issue) > 1) {
			$this->setStatusMetaData( "MULTIPLE_MATCHES" );
			return false;
		}

		$this->setStatusMetaData( "COMICVINE_SUCCESS" );
		return true;
	}

	public function selectMatchingIssue( $issueId = null )
	{
		if ( is_string($issueId) && strlen($issueId) > 0 ) {
			$issues = $this->getMeta( UploadImport::META_RESULTS_ISSUES );
			if ( is_array($issues) ) {
				$result = array_filterForKeyValue( $issues, array( "id"=>$issueId));
				$this->setMeta( UploadImport::META_RESULTS_ISSUES, $result );
				$this->setStatusMetaData( "Finishing Import" );
				return true;
			}
		}
		return false;
	}

	public function selectMatchingPublication( PublicationDBO $publication = null )
	{
		if ( is_null($publication) == false ) {
			// clear issues from any autmated searches
			$this->setMeta( UploadImport::META_RESULTS_ISSUES, null );

			$series = $publication->series();
			$pub_issue = array();
			array_setValueForKeypath( "volume/name", $series->name, $pub_issue );
			array_setValueForKeypath( "volume/id", $series->xid, $pub_issue );
			array_setValueForKeypath( "id", $publication->xid, $pub_issue );
			array_setValueForKeypath( "issue_number", $publication->issue_num, $pub_issue );
			array_setValueForKeypath( "name", $publication->name, $pub_issue );
			array_setValueForKeypath( "cover_date", $publication->formattedDate( "pub_date", "Y-M-D" ), $pub_issue );

			$this->setMeta( UploadImport::META_RESULTS_ISSUES, array( $pub_issue ) );

			return true;
		}
		return false;
	}

	public function processData(ProcessStatusReporter $reporter = null)
	{
		$wrapper = FileWrapper::instance($this->importFilePath());
		$testStatus = $wrapper->testWrapper($errorCode);
		if ($errorCode != 0 || $this->getMeta(UploadImport::META_MEDIA_EXT) != 'cbz' ) {
			$this->setStatusMetaData( "MEDIA_CORRUPT" );
			Logger::logInfo( "Media corrupt " . $testStatus, __method__, $this->sourceFilename());
			return;
		}

		if ($this->hasResultsMetadata() == false && $this->processSearch() == false ) {
			$this->generateThumbnails();
			return;
		}

		$issue = $this->getMeta( UploadImport::META_RESULTS_ISSUES );
		if (count($issue) > 1) {
			$this->setStatusMetaData( "MULTIPLE_METADATA");
			Logger::logError( "Multiple media metadata found for importing", __method__, $this->sourceFilename());
			$this->generateThumbnails();
			return;
		}

		$matchingIssue = $issue[0];

		$ep_model = Model::Named('Endpoint');
		$points = $ep_model->allForTypeCode(Endpoint_Type::ComicVine);
		if ($points == false || count($points) == 0) {
			$this->setStatusMetaData( "NO_ENDPOINTS" );
			Logger::logInfo( "No ComicVine Endpoints defined ", __method__, $this->sourceFilename());
			return false;
		}

		if ( $this->isAutomatedImport() ) {
			$existingPublication = Model::Named("Publication")->objectForExternal(
				array_valueForKeypath( "id", $matchingIssue),
				Endpoint_Type::ComicVine
			);

			if ( $existingPublication instanceof PublicationDBO ) {
				$media = $existingPublication->media();

				if (is_array($media) && count($media) > 0) {
					$this->setStatusMetaData( "EXISTING_MEDIA" );
					Logger::logInfo( "Media already imported for " .  $existingPublication->searchString(),
						__method__, $this->sourceFilename());
					return false;
				}
			}
		}

		$this->setStatusMetaData( "Finishing Import" );
// 		Logger::logInfo( "Found match importing "
// 			. array_valueForKeypath( "volume/name", $matchingIssue)
// 			. " - " . array_valueForKeypath( "issue_number", $matchingIssue)
// 			. " - " . array_valueForKeypath( "cover_date", $matchingIssue)
// 			 , __method__, $this->sourceFilename());

		$importer = Processor::Named('ComicVineImporter', $this->guid );
		$importer->setEndpoint($points[0]);

		$existingSeries = Model::Named("Series")->objectForExternal(
			array_valueForKeypath( "volume/id", $matchingIssue),
			Endpoint_Type::ComicVine
		);
		$seriesNeedsUpdate = ($existingSeries == false || $existingSeries->needsEndpointUpdate());
		$importer->enqueue_series( array(
			"xid" => array_valueForKeypath( "volume/id", $matchingIssue),
			"name" => array_valueForKeypath( "volume/name", $matchingIssue),
			), $seriesNeedsUpdate, $seriesNeedsUpdate
		);

		$importer->enqueue_publication( array(
			"xid" => array_valueForKeypath( "id", $matchingIssue),
			"name" => array_valueForKeypath( "name", $matchingIssue),
			"issue" => array_valueForKeypath( "issue_number", $matchingIssue),
			), true, true
		);

		try {
			$importer->processData($reporter);

			$cbzType = Model::Named( "Media_Type" )->cbz();
			$pub_xid = array_valueForKeypath( "id", $matchingIssue);
			$publication = Model::Named("Publication")->objectForExternal($pub_xid, Endpoint_Type::ComicVine);
			$filename = $this->getMeta(UploadImport::META_MEDIA_FILENAME);
			$hash = $this->getMeta(UploadImport::META_MEDIA_HASH);
			$size = $this->getMeta(UploadImport::META_MEDIA_SIZE);

			list($media, $errors) = Model::Named( "Media" )->createObject( array(
				"publication" => $publication,
				"mediaType" => $cbzType,
				Media::original_filename => $filename,
				Media::checksum => $hash,
				Media::size => $size
				)
			);
			if ( $media instanceof \model\media\MediaDBO ) {
				if ( rename( $this->importFilePath(), $media->contentaPath()) ) {
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

	public function convert_cbr()
	{
		$temp = $this->workingDirectory("cbr");
		if ( is_dir($temp) ) {
			destroy_dir($temp) || die( "unable to delete $temp" );
		}
		safe_mkdir( $temp ) || die( "Unable to create $temp" );

		$filename = $this->getMeta(UploadImport::META_MEDIA_FILENAME);
		$wrapper = FileWrapper::instance($this->importFilePath());
		$success = $wrapper->testWrapper( $error );
		if ( $success != null && $error == 10) {
			// RARX_NOFILES, check for zip
			$zipFileList = zipFileList($this->importFilePath());
			if (is_array($zipFileList)) {
				Logger::logWarning( $filename . " identified as ZIP format", __method__, $this->sourceFilename() );
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
				(file_exists($dest) == false) || safe_unlink($dest) || die( "Unable to delete old $dest");
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
