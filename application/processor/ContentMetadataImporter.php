<?php

namespace processor;

use \Processor as Processor;
use \Migrator as Migrator;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \Exception as Exception;

use \model\user\Users as Users;
use model\Publisher as Publisher;
use model\Character as Character;
use model\Series as Series;
use model\Publication as Publication;
use \model\network\Endpoint as Endpoint;
use \model\network\Endpoint_Type as Endpoint_Type;
use \model\network\EndpointDBO as EndpointDBO;
use model\Story_Arc as Story_Arc;
use model\Story_Arc_Character as Story_Arc_Character;
use model\Story_Arc_Series as Story_Arc_Series;

abstract class ContentMetadataImporter extends EndpointImporter
{
	const META_ENQUEUE_ROOT = 	'enqueued/';
	const META_IMPORT_ROOT = 	'import/';
	const META_DATA_ROOT =	 	'data/';
	const META_PURGE_ROOT = 	'purge/';
	const META_FULL_ROOT =	 	'fullmetal/';

	const META_IMPORT_TYPE = 		'meta_type';
	const META_IMPORT_XID = 		'xid';
	const META_IMPORT_FORCE =		'meta_force';
	const META_IMPORT_FORCE_ICON =	'image/overwrite';
	const META_IMPORT_SMALL_ICON =	'image/small';
	const META_IMPORT_LARGE_ICON =	'image/large';
	const META_IMPORT_RELATIONSHIP = 'relationships';

	public $strictErrors = true;

	function __construct($guid)
	{
		parent::__construct($guid);
	}

	public function importImages( $mediaObject, array $cvDetails = array() )
	{
		if ( $mediaObject->hasAdditionalMedia() ) {
			$forceImages = array_valueForKeypath(ComicVineImporter::META_IMPORT_FORCE_ICON, $cvDetails);
			if ( $forceImages == true || $mediaObject->hasIcons() == false ) {
				$imageURL = array_valueForKeypath(ComicVineImporter::META_IMPORT_SMALL_ICON, $cvDetails);
				if ( is_null($imageURL) == false ) {
					$this->importImage( $mediaObject, Model::IconName, $imageURL );
				}

				$imageURL = array_valueForKeypath(ComicVineImporter::META_IMPORT_LARGE_ICON, $cvDetails);
				if ( is_null($imageURL) == false ) {
					$this->importImage( $mediaObject, Model::ThumbnailName, $imageURL );
				}
			}
// 			else {
// 				Logger::logInfo( "$mediaObject Not importing images (forceImages = $forceImages, hasIcons = " . $mediaObject->hasIcons() . ")",
// 					$this->type, $this->guid );
// 			}
		}
		return false;
	}

	public function importImage( $mediaObject, $imagename = Model::IconName, $sourceurl )
	{
		if ( isset($sourceurl) && $mediaObject->hasAdditionalMedia() ) {
			$filename = downloadImage($sourceurl, $this->workingDirectory(), $imagename );
			if ( empty($filename) == false ) {
				$imageFile = $mediaObject->imagePath($imagename);
				if ( is_file($imageFile) ) {
					safe_unlink($imageFile) || die("failed to remove old file? " . $imageFile);
				}

				$newfile = $mediaObject->mediaPath($filename);
				rename(appendPath($this->workingDirectory(), $filename), $newfile) || die("failed install new file? " . $newfile);
			}
			else {
				Logger::logError( "$mediaObject no file for $sourceurl", $this->type, $this->guid );
			}
		}
		else {
			Logger::logError( "$mediaObject does not support images", $this->type, $this->guid );
		}
		return false;
	}

	/**
	 * Set meta data for async processing
	 */
	public function enqueue($model = null, array $importValues = array(), $forceMeta = false, $forceImages = false)
	{
		if ( is_null($model) || ($model instanceof \Model) == false) {
			throw new Exception("Destination Model is required " . var_export($model, true));
		}

		// this should never happen since the xid is required
		if ( isset($importValues[ContentMetadataImporter::META_IMPORT_XID]) == false ||
			strlen($importValues[ContentMetadataImporter::META_IMPORT_XID]) == 0) {
			throw new Exception("External ID '" . ContentMetadataImporter::META_IMPORT_XID . "'is required" . var_export($importValues, true));
		}

		$importKeys = $model->allColumnNames();
		if ( count($importKeys) == 0) {
			throw new Exception("Import Keys are required");
		}

		$queue_key = $model->tableName() . "_" . $importValues[ContentMetadataImporter::META_IMPORT_XID];
		$data_path = appendPath( ContentMetadataImporter::META_DATA_ROOT, $queue_key);
		if ( $this->isMeta( $data_path ) == false ) {
			// data lot loaded
			foreach( $importKeys as $key ) {
				$value = array_valueForKeypath($key, $importValues);
				if ( $value != null ) {
					$this->setMeta( appendPath( $data_path, $key), $value);
				}
			}
			$this->setMeta( appendPath( $data_path, "xsource"), $this->endpointTypeCode());
			$this->setMetaBoolean( appendPath( $data_path, ContentMetadataImporter::META_IMPORT_FORCE), $forceMeta );
			$this->setMetaBoolean( appendPath( $data_path, ContentMetadataImporter::META_IMPORT_FORCE_ICON), $forceImages );
			$this->setMeta( appendPath( $data_path, ContentMetadataImporter::META_IMPORT_TYPE), $model->tableName() );

			if ( $forceMeta == true ) {
				$this->setMeta(
					appendPath(ContentMetadataImporter::META_FULL_ROOT, $model->tableName(), $queue_key),
					$importValues[ContentMetadataImporter::META_IMPORT_XID]
				);
			}
		}

		// check to see if the record has been pre-processed
		$imported_path = appendPath( ContentMetadataImporter::META_IMPORT_ROOT, $queue_key);
		if ( $this->isMeta( $imported_path ) == false ) {
			// not pre-processed, it may or may not be enqueued
			$this->setMeta( appendPath( ContentMetadataImporter::META_ENQUEUE_ROOT, $queue_key), $queue_key );
		}

		return $this->getMeta( $data_path );
	}

	public function enqueue_publisher(array $importValues = array(), $forceMeta = false, $forceImages = false)
	{
		return $this->enqueue( Model::Named('Publisher'), $importValues, $forceMeta, $forceImages);
	}

	public function enqueue_series(array $importValues = array(), $forceMeta = false, $forceImages = false)
	{
		return $this->enqueue( Model::Named('Series'), $importValues, $forceMeta, $forceImages);
	}

	public function enqueue_character(array $importValues = array(), $forceMeta = false, $forceImages = false)
	{
		return $this->enqueue( Model::Named('Character'), $importValues, $forceMeta, $forceImages);
	}

	public function enqueue_publication(array $importValues = array(), $forceMeta = false, $forceImages = false)
	{
		return $this->enqueue( Model::Named('Publication'), $importValues, $forceMeta, $forceImages);
	}

	public function enqueue_story_arc(array $importValues = array(), $forceMeta = false, $forceImages = false)
	{
		return $this->enqueue( Model::Named('Story_Arc'), $importValues, $forceMeta, $forceImages);
	}

	// purge records?
	public function purge_publisher(array $metaRecord = array())
	{
		if ( isset($metaRecord[ContentMetadataImporter::META_IMPORT_XID]) == false ||
			strlen($metaRecord[ContentMetadataImporter::META_IMPORT_XID]) == 0) {
			throw new Exception("External ID '" . ContentMetadataImporter::META_IMPORT_XID . "'is required" . var_export($metaRecord, true));
		}

		$xid = array_valueForKeypath( ContentMetadataImporter::META_IMPORT_XID, $metaRecord );
		$object = Model::Named("Publisher")->objectForExternal($xid, $this->endpointTypeCode());
		if ( $object != false ) {
			// bump the xupdated date so this object wont take an update slot for awhile
			return Model::Named("Publisher")->updateObject( $object, array( "xupdated" => time()) );
		}

		return true;
	}

	public function purge_series(array $metaRecord = array())
	{
		if ( isset($metaRecord[ContentMetadataImporter::META_IMPORT_XID]) == false ||
			strlen($metaRecord[ContentMetadataImporter::META_IMPORT_XID]) == 0) {
			throw new Exception("External ID '" . ContentMetadataImporter::META_IMPORT_XID . "'is required" . var_export($metaRecord, true));
		}

		$xid = array_valueForKeypath( ContentMetadataImporter::META_IMPORT_XID, $metaRecord );
		$object = Model::Named("Series")->objectForExternal($xid, $this->endpointTypeCode());
		if ( $object != false ) {
			// bump the xupdated date so this object wont take an update slot for awhile
			return Model::Named("Series")->updateObject( $object, array( "xupdated" => time()) );
		}

		return true;
	}

	public function purge_character(array $metaRecord = array())
	{
		if ( isset($metaRecord[ContentMetadataImporter::META_IMPORT_XID]) == false ||
			strlen($metaRecord[ContentMetadataImporter::META_IMPORT_XID]) == 0) {
			throw new Exception("External ID '" . ContentMetadataImporter::META_IMPORT_XID . "'is required" . var_export($metaRecord, true));
		}

		$xid = array_valueForKeypath( ContentMetadataImporter::META_IMPORT_XID, $metaRecord );
		$object = Model::Named("Character")->objectForExternal($xid, $this->endpointTypeCode());
		if ( $object != false ) {
			return Model::Named("Character")->deleteObject( $object );
		}
		else {
			// object already purged
			return true;
		}

		return false;
	}

	public function purge_publication(array $metaRecord = array())
	{
		if ( isset($metaRecord[ContentMetadataImporter::META_IMPORT_XID]) == false ||
			strlen($metaRecord[ContentMetadataImporter::META_IMPORT_XID]) == 0) {
			throw new Exception("External ID '" . ContentMetadataImporter::META_IMPORT_XID . "'is required" . var_export($metaRecord, true));
		}

		$xid = array_valueForKeypath( ContentMetadataImporter::META_IMPORT_XID, $metaRecord );
		$object = Model::Named("Publication")->objectForExternal($xid, $this->endpointTypeCode());
		if ( $object != false ) {
			// bump the xupdated date so this object wont take an update slot for awhile
			return Model::Named("Publication")->updateObject( $object, array( "xupdated" => time()) );
		}

		return true;
	}

	public function purge_story_arc(array $metaRecord = array())
	{
		if ( isset($metaRecord[ContentMetadataImporter::META_IMPORT_XID]) == false ||
			strlen($metaRecord[ContentMetadataImporter::META_IMPORT_XID]) == 0) {
			throw new Exception("External ID '" . ContentMetadataImporter::META_IMPORT_XID . "'is required" . var_export($metaRecord, true));
		}

		$xid = array_valueForKeypath( ContentMetadataImporter::META_IMPORT_XID, $metaRecord );
		$object = Model::Named("Story_Arc")->objectForExternal($xid, $this->endpointTypeCode());
		if ( $object != false ) {
			// bump the xupdated date so this object wont take an update slot for awhile
			return Model::Named("Story_Arc")->updateObject( $object, array( "xupdated" => time()) );
		}

		return true;
	}


	public function preprocess($model = null, array $metaRecord = array())
	{
		if ( is_null($model) || ($model instanceof \Model) == false) {
			throw new Exception("Destination Model is required " . var_export($model, true));
		}

		// this should never happen since the xid is required
		if ( isset($metaRecord[ContentMetadataImporter::META_IMPORT_XID]) == false || strlen($metaRecord[ContentMetadataImporter::META_IMPORT_XID]) == 0) {
			throw new Exception("External ID '" . ContentMetadataImporter::META_IMPORT_XID . "'is required" . var_export($metaRecord, true));
		}

		$importKeys = $model->allColumnNames();
		if ( count($importKeys) == 0) {
			throw new Exception("Import Keys are required");
		}

		// check for existing
		$xid = array_valueForKeypath( ContentMetadataImporter::META_IMPORT_XID, $metaRecord );
		$object = $model->objectForExternal($xid, $this->endpointTypeCode());
		if ( $object == false ) {
			$createDict = array();
			foreach( $importKeys as $key ) {
				$value = array_valueForKeypath($key, $metaRecord);
				if ( $value != null ) {
					$createDict[$key] = $value;
				}
			}

			list( $object, $errorList ) = $model->createObject($createDict);
			if ( is_array($errorList) ) {
				throw new Exception("Failed to create new object " . var_export($errorList, true));
			}
		}

		return ($object == false) ? null : $object;
	}

	public function finalize($model = null, array $metaRecord = array())
	{
		if ( is_null($model) || ($model instanceof \Model) == false) {
			throw new Exception("Destination Model is required " . var_export($model, true));
		}

		// this should never happen since the xid is required
		if ( isset($metaRecord[ContentMetadataImporter::META_IMPORT_XID]) == false || strlen($metaRecord[ContentMetadataImporter::META_IMPORT_XID]) == 0) {
			throw new Exception("External ID '" . ContentMetadataImporter::META_IMPORT_XID . "'is required" . var_export($metaRecord, true));
		}

		$importKeys = $model->allColumnNames();
		if ( count($importKeys) == 0) {
			throw new Exception("Import Keys are required");
		}

		// check for existing
		$xid = array_valueForKeypath( ContentMetadataImporter::META_IMPORT_XID, $metaRecord );
		$object = $model->objectForExternal($xid, $this->endpointTypeCode());
		if ( $object == false ) {
			throw new Exception("All objects should be in finalize state " . var_export($metaRecord, true) );
		}

		// import images
		$this->importImages( $object, $metaRecord );

		$aliases = array_valueForKeypath("aliases", $metaRecord);
		if ( isset($aliases) && strlen($aliases) > 0 ) {
			$aliases = split_lines($aliases);
			if ( is_array($aliases) && method_exists($object, "addAlias") ) {
				foreach( $aliases as $alias ) {
					$object->addAlias( $alias );
				}
			}
		}

		$updates = array();
		foreach( $importKeys as $key ) {
			$value = array_valueForKeypath($key, $metaRecord);
			if ( $value != null && (isset($object->{$key}) == false || $value != $object->{$key})) {
				$updates[$key] = $value;
			}
		}

		if ( count($updates) > 0 ) {
			if ( $model->updateObject($object, $updates ) ) {
				return $model->refreshObject( $object );
			}
		}

		return $object;
	}

	public function enqueueBatch( $objectType = '', $size = 0 )
	{
		if ( is_numeric($size) ) {
			$this->setJobDescription( "Refreshing $size $objectType records" );

			$size = min(abs($size), 50);
			$methodName = 'enqueue_' . $objectType;
			if (method_exists($this, $methodName)) {
				$objectModel = Model::Named( $objectType );
				if (( $objectModel instanceof \Model) == false) {
					throw new \Exception( "Failed to load model named '" . $objectModel . "'");
				}
				$objects = $objectModel->allObjectsNeedingExternalUpdate($size);

				foreach( $objects as $idx => $object ) {
					call_user_func_array(array($this, $methodName), array( array( "xid" => $object->xid), true, true) );
				}
				$this->strictErrors = false;
			}
			else {
				throw new \Exception( "No method named '" . $methodName . "( array, bool, bool )'");
			}
		}
		else {
			throw new \Exception( "Batch parameter is not a number '" . $size . "'");
		}
	}

	public function processData()
	{
		$enqueued = $this->getMeta( ComicVineImporter::META_ENQUEUE_ROOT );
		while ( is_array($enqueued) && count($enqueued) > 0) {
			// move to imported
			foreach( $enqueued as $path => $data_keypath ) {
				$old = appendPath( ComicVineImporter::META_ENQUEUE_ROOT, $path);
				$new = appendPath( ComicVineImporter::META_IMPORT_ROOT, $path);
				$meta = $this->getMeta( appendPath( ComicVineImporter::META_DATA_ROOT, $path ));

				$pre_process_method = 'preprocess_' . $meta[ContentMetadataImporter::META_IMPORT_TYPE];
				if (method_exists($this, $pre_process_method)) {
					try {
						$success_return = $this->$pre_process_method($meta);
						if ( is_null($success_return)) {
							throw new Exception("pre-processing error " . $pre_process_method );
						}
					}
					catch ( Exception $e ) {
						Logger::logError( "Preprocessing error for " . var_export($meta, true), __method__, $this->guid);
						switch( $e->getCode() ) {
							case 101:
								Logger::logError( $meta[ContentMetadataImporter::META_IMPORT_TYPE] . " not found for xid " . $path,
									$this->type, $this->guid);
								// move object to the purge list
								$this->setMeta( $new, null );
								$purge = appendPath( ComicVineImporter::META_PURGE_ROOT, $path);
								$this->setMeta( $purge, $meta );
								break;

							default:
								Logger::LogException( $e );
								if ( $this->strictErrors == true ) {
									throw $e;
								}
								break;
						}
					}
					$this->setMeta( $old, null );
					$this->setMeta( $new, $data_keypath );
				}
				else {
					throw new Exception("failed to find processing function " . $pre_process_method );
				}
			}

			// see if anything has been enqueued
			$enqueued = $this->getMeta( ComicVineImporter::META_ENQUEUE_ROOT );
		}

		$imported = $this->getMeta( ComicVineImporter::META_IMPORT_ROOT );
		if ( is_array($imported) && count($imported) > 0 ) {
			foreach( $imported as $path => $data_keypath ) {
				$meta = $this->getMeta( appendPath( ComicVineImporter::META_DATA_ROOT, $path ));

				$finalize_method = 'finalize_' . $meta[ContentMetadataImporter::META_IMPORT_TYPE];
				if (method_exists($this, $finalize_method)) {
					try {
						$success_return = $this->$finalize_method($meta);
						if ( is_null($success_return) ) {
							throw new Exception("finalize error " . $finalize_method . '(' . $data_keypath . ')' );
						}
					}
					catch ( Exception $e ) {
						Logger::LogException( $e );
						if ( $this->strictErrors == true ) {
							throw $e;
						}
					}
				}
				else {
					throw new Exception("failed to find processing function " . $finalize_method );
				}
			}
		}

		$toBePurged = $this->getMeta( ComicVineImporter::META_PURGE_ROOT );
		if ( is_array($toBePurged) && count($toBePurged) > 0 ) {
			foreach( $toBePurged as $path => $data_keypath ) {
				$meta = $this->getMeta( appendPath( ComicVineImporter::META_DATA_ROOT, $path ));
				$purge_method = 'purge_' . $meta[ContentMetadataImporter::META_IMPORT_TYPE];
				if (method_exists($this, $purge_method)) {
					try {
						$success_return = $this->$purge_method($meta);
						if ( is_null($success_return) ) {
							throw new Exception("purge error " . $purge_method );
						}
					}
					catch ( Exception $e ) {
						Logger::LogException( $e );
						if ( $this->strictErrors == true ) {
							throw $e;
						}
					}
				}
				else {
					throw new Exception("failed to find purge function " . $finalize_method );
				}
			}
		}

		$order = array( "unknown", Publication::TABLE, Story_Arc::TABLE, Series::TABLE );
		$fullMetal = $this->getMeta( ContentMetadataImporter::META_FULL_ROOT );
		if ( is_array($fullMetal) && count($fullMetal) > 0 ) {
			$alchemist = array_keys($fullMetal);
			usort($alchemist, function ($a, $b) use ($order) {
				$pos_a = intval(array_search($a, $order));
				$pos_b = intval(array_search($b, $order));
				return $pos_a - $pos_b;
			});

			foreach( $alchemist as $tableName ) {
				$list = $this->getMeta( appendPath(ContentMetadataImporter::META_FULL_ROOT, $tableName) );
				$model = Model::Named($tableName);
				foreach( $list as $path => $xid ) {
					$model->updateStatistics( $xid, $this->endpointTypeCode());
				}
			}
		}

		$this->setPurgeOnExit(true);
		return true;
	}
}
