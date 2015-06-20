<?php

namespace processor;

use \Processor as Processor;
use \Migrator as Migrator;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \Exception as Exception;

use model\Users as Users;
use model\Publisher as Publisher;
use model\Character as Character;
use model\Series as Series;
use model\Publication as Publication;
use model\Endpoint as Endpoint;
use model\Endpoint_Type as Endpoint_Type;
use model\EndpointDBO as EndpointDBO;
use model\Story_Arc as Story_Arc;
use model\Story_Arc_Character as Story_Arc_Character;
use model\Story_Arc_Series as Story_Arc_Series;

abstract class EndpointImporter extends Processor
{
	const META_ENDPOINT_DISPLAY = "endpoint_name";
	const META_ENDPOINT_ID 		= "endpoint_id";
	const META_ENDPOINT_TYPE 	= "endpoint_type_code";

	const META_ENQUEUE_ROOT = 	'enqueued/';
	const META_IMPORT_ROOT = 	'import/';

	const META_IMPORT_TYPE = 		'meta_type';
	const META_IMPORT_XID = 		'xid';
	const META_IMPORT_FORCE =		'meta_force';
	const META_IMPORT_FORCE_ICON =	'image/overwrite';
	const META_IMPORT_SMALL_ICON =	'image/small';
	const META_IMPORT_LARGE_ICON =	'image/large';
	const META_IMPORT_RELATIONSHIP = 'relationships';

	function __construct($guid)
	{
		parent::__construct($guid);
	}

	public function endpointConnector()
	{
		$endpoint = $this->endpoint();
		if ( $endpoint == false ) {
			throw new Exception("No Endpoint set for Importer " . get_class() );
		}

		$connectorName = 'connectors\\' . $endpoint->type()->code . 'Connector';
		$connection = new $connectorName($comicVine);
		return $connection;
	}

	public function endpointTypeCode() {
		if ( $this->isMeta(EndpointImporter::META_ENDPOINT_TYPE) ) {
			return $this->getMeta(EndpointImporter::META_ENDPOINT_TYPE);
		}

		$endpoint = $this->endpoint();
		if ( $endpoint == false ) {
			throw new Exception("No Endpoint set for Importer " . get_class() );
		}
		$this->setMeta(EndpointImporter::META_ENDPOINT_TYPE, $endpoint->type()->code );
		return $endpoint->type()->code;
	}

	public function endpoint()
	{
		if ( isset($this->endpoint) == false ) {
			if ( $this->isMeta(EndpointImporter::META_ENDPOINT_ID) ) {
				$endid = $this->getMeta(EndpointImporter::META_ENDPOINT_ID);
				$model = Model::Named('Endpoint');
				$this->endpoint = $model->objectForId($endid);
				return $this->endpoint;
			}
		}
		else {
			return $this->endpoint;
		}

		return false;
	}

	public function setEndpoint($point) {
		if ( is_a($point, '\model\EndpointDBO')) {
			if ($point->isEnabled() ) {
				$this->endpoint = $point;
				$this->setMeta(EndpointImporter::META_ENDPOINT_DISPLAY, $point->displayName() );
				$this->setMeta(EndpointImporter::META_ENDPOINT_ID, $point->id);
				$this->setMeta(EndpointImporter::META_ENDPOINT_TYPE, $point->type()->code );
			}
			else {
				throw new Exception("Endpoint " . $point->displayName() . " is disabled");
			}
		}
		else {
			throw new Exception("Cannot be initialized with "
				. (empty($point) ? '-null-' : get_class($point))
				. ", requires a configuration of type 'endpointDBO'");
		}
	}

	public function importImage( $mediaObject, $imagename = Model::IconName, $sourceurl )
	{
		if ( isset($sourceurl) && $mediaObject->hasAdditionalMedia() ) {
			$filename = downloadImage($sourceurl, $this->workingDirectory(), $imagename );
			if ( empty($filename) == false ) {
				$imageFile = $mediaObject->imagePath($imagename);
				if ( is_file($imageFile) ) {
					unlink($imageFile) || die("failed to remove old file? " . $imageFile);
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
		if ( isset($importValues[EndpointImporter::META_IMPORT_XID]) == false || strlen($importValues[EndpointImporter::META_IMPORT_XID]) == 0) {
			throw new Exception("External ID '" . EndpointImporter::META_IMPORT_XID . "'is required" . var_export($importValues, true));
		}

		$importKeys = $model->allColumnNames();
		if ( count($importKeys) == 0) {
			throw new Exception("Import Keys are required");
		}

		$dir = $model->tableName() . '_' . $importValues[EndpointImporter::META_IMPORT_XID];
		// check to see if the record has been pre-processed
		$root = appendPath( EndpointImporter::META_IMPORT_ROOT, $dir);
		if ( $this->isMeta( $root ) == false ) {
			// not pre-processed, it may or may not be enqueued
			$root = appendPath( EndpointImporter::META_ENQUEUE_ROOT, $dir);
		}

		foreach( $importKeys as $key ) {
			$value = array_valueForKeypath($key, $importValues);
			if ( $value != null ) {
				$this->setMeta( appendPath( $root, $key), $value);
			}
		}

		$this->setMeta( appendPath( $root, "xsource"), $this->endpointTypeCode());
		$this->setMetaBoolean( appendPath( $root, EndpointImporter::META_IMPORT_FORCE), $forceMeta );
		$this->setMetaBoolean( appendPath( $root, EndpointImporter::META_IMPORT_FORCE_ICON), $forceImages );
		$this->setMeta( appendPath( $root, EndpointImporter::META_IMPORT_TYPE), $model->tableName() );

		return $this->getMeta( $root );
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

	public function preprocess($model = null, array $metaRecord = array())
	{
		if ( is_null($model) || ($model instanceof \Model) == false) {
			throw new Exception("Destination Model is required " . var_export($model, true));
		}

		// this should never happen since the xid is required
		if ( isset($metaRecord[EndpointImporter::META_IMPORT_XID]) == false || strlen($metaRecord[EndpointImporter::META_IMPORT_XID]) == 0) {
			throw new Exception("External ID '" . EndpointImporter::META_IMPORT_XID . "'is required" . var_export($metaRecord, true));
		}

		$importKeys = $model->allColumnNames();
		if ( count($importKeys) == 0) {
			throw new Exception("Import Keys are required");
		}

		// check for existing
		$xid = array_valueForKeypath( EndpointImporter::META_IMPORT_XID, $metaRecord );
		$object = $model->objectForExternal($xid, $this->endpointTypeCode());
		if ( $object == false ) {
			$createDict = array();
			foreach( $importKeys as $key ) {
				$value = array_valueForKeypath($key, $metaRecord);
				if ( $value != null ) {
					$createDict[$key] = $value;
				}
			}

			$objectOrErrors = $model->createObject($createDict);
			if ( is_array($objectOrErrors) ) {
				throw new Exception("Failed to create new object " . var_export($objectOrErrors, true));
			}

			if ($objectOrErrors != false) {
				$object = $objectOrErrors;
				Logger::logWarning( "Importing stub record for " . $object, $this->type, $this->guid );
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
		if ( isset($metaRecord[EndpointImporter::META_IMPORT_XID]) == false || strlen($metaRecord[EndpointImporter::META_IMPORT_XID]) == 0) {
			throw new Exception("External ID '" . EndpointImporter::META_IMPORT_XID . "'is required" . var_export($metaRecord, true));
		}

		$importKeys = $model->allColumnNames();
		if ( count($importKeys) == 0) {
			throw new Exception("Import Keys are required");
		}

		// check for existing
		$xid = array_valueForKeypath( EndpointImporter::META_IMPORT_XID, $metaRecord );
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
			else {
				Logger::LogWarning( "$object does not support aliases '" . array_valueForKeypath("aliases", $metaRecord) . "'", $this->type, $this->guid );
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

	public function setEndpointId( $endpoint_id = 0)
	{
		$endp = Model::Named( "Endpoint" )->objectForId( $endpoint_id );
		$this->setEndpoint( $endp );
	}

	public function enqueueBatch( $objectType = '', $size = 0 )
	{
		if ( is_numeric($size) ) {
			$size = max(abs($size), 50);
			$methodName = 'enqueue_' . $objectType;
			if (method_exists($this, $methodName)) {
				$objectModel = Model::Named( $objectType );
				if (( $objectModel instanceof \Model) == false) {
					throw new \Exception( "Failed to load model named '" . $objectModel . "'");
				}
				$objects = $objectModel->allObjectsNeedingExternalUpdate($size);

				foreach( $objects as $idx => $object ) {
					Logger::logInfo( "Enqueuing $idx: " . $object, $this->type, $this->guid );
					call_user_func_array(array($this, $methodName), array( array( "xid" => $object->xid), true, true) );
				}
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
			foreach( $enqueued as $path => $meta ) {
				$old = appendPath( ComicVineImporter::META_ENQUEUE_ROOT, $path);
				$new = appendPath( ComicVineImporter::META_IMPORT_ROOT, $path);
				$this->setMeta( $old, null );
				$this->setMeta( $new, $meta );

				$pre_process_method = 'preprocess_' . $meta[EndpointImporter::META_IMPORT_TYPE];
				if (method_exists($this, $pre_process_method)) {
					if ( $this->$pre_process_method($meta) == null ) {
						throw new Exception("pre-processing error " . $pre_process_method );
					}
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
			foreach( $imported as $path => $meta ) {
				$finalize_method = 'finalize_' . $meta[EndpointImporter::META_IMPORT_TYPE];
				if (method_exists($this, $finalize_method)) {
					if ( $this->$finalize_method($meta) == null ) {
						throw new Exception("finalize error " . $finalize_method );
					}
				}
				else {
					throw new Exception("failed to find processing function " . $finalize_method );
				}
			}
		}

		$this->setPurgeOnExit(true);
		return true;
	}
}
