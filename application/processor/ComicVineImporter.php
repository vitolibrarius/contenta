<?php

namespace processor;

use \Processor as Processor;
use \Migrator as Migrator;
use \Config as Config;
use \Logger as Logger;
use \Exception as Exception;
use \Model as Model;

use processor\EndpointImporter as EndpointImporter;
use endpoints\ComicVineConnector as ComicVineConnector;

use model\Users as Users;
use model\Publisher as Publisher;
use model\Endpoint as Endpoint;
use model\Endpoint_Type as Endpoint_Type;
use model\EndpointDBO as EndpointDBO;

class ComicVineImporter extends EndpointImporter
{
	const META_IMPORT_PUB_ROOT = 	'import/publisher/';

	const META_IMPORT_SMALL_ICON =	'image/small';
	const META_IMPORT_LARGE_ICON =	'image/large';

	const META_IMPORT_NAME =	'name';
	const META_IMPORT_XID =		'xid';
	const META_IMPORT_XSRC =	'xsource';
	const META_IMPORT_XURL =	'xurl';


	function __construct($guid)
	{
		parent::__construct($guid);
	}

	public function setEndpoint($point) {
		if ( is_a($point, '\model\EndpointDBO')) {
			$type = $point->type();
			if ( $type == false || $type->code != Endpoint_Type::ComicVine ) {
				throw new Exception("Endpoint " . $point->displayName() . " is is not for " . Endpoint_Type::ComicVine);
			}
		}
		parent::setEndpoint($point);
	}

	public function importImage( $mediaObject, $imagename = "small_icon_name", $sourceurl )
	{
		if ( isset($sourceurl) ) {
			$filename = downloadImage($sourceurl, $this->workingDirectory(), $imagename );
			if ( empty($filename) == false ) {
				$oldfile = $mediaObject->fullpath($mediaObject->{$imagename});
				if ( is_file($oldfile) ) {
					unlink($oldfile) || die("failed to remove old file? " . $oldfile);
				}

				$newfile = $mediaObject->fullpath($filename);
				rename(appendPath($this->workingDirectory(), $filename), $newfile) || die("failed install new file? " . $newfile);

				if ( $mediaObject->model()->updateObject( $mediaObject, array( $imagename => $filename )) ) {
					return $mediaObject->model()->refreshObject($mediaObject);
				}
			}
		}
		return false;
	}

	/**
	 * Set meta data for async processing
	 */
	public function importPublisherValues($name = null, $xid = null, $xurl = null)
	{
		$this->setMeta( ComicVineImporter::META_IMPORT_PUB_ROOT . ComicVineImporter::META_IMPORT_NAME, $name);
		$this->setMeta( ComicVineImporter::META_IMPORT_PUB_ROOT . ComicVineImporter::META_IMPORT_XID, $xid);
		$this->setMeta( ComicVineImporter::META_IMPORT_PUB_ROOT . ComicVineImporter::META_IMPORT_XSRC, Endpoint_Type::ComicVine);
		$this->setMeta( ComicVineImporter::META_IMPORT_PUB_ROOT . ComicVineImporter::META_IMPORT_XURL, $xurl);
	}

	/**
	 * Perform actual processing actions including fetching from ComicVine.  Can be called sync or async
	 */
	private function processPublisher()
	{
		if ( $this->isMeta(ComicVineImporter::META_IMPORT_PUB_ROOT . ComicVineImporter::META_IMPORT_XID) ) {
			$publisherId = $this->getMeta(ComicVineImporter::META_IMPORT_PUB_ROOT . ComicVineImporter::META_IMPORT_XID);
			if ( $this->isMeta(ComicVineImporter::META_IMPORT_PUB_ROOT . ComicVineImporter::META_IMPORT_NAME) == false) {
				// load the details from ComicVine
				$comicVine = $this->endpoint();
				if ( $comicVine == false ) {
					throw new Exception("Endpoint was not found");
				}
				else {
					$connection = new ComicVineConnector($comicVine);
					$record = $connection->publisherDetails( $publisherId );
					if (isset($record, $record['name'], $record['id'], $record['api_detail_url'])) {
						$this->importPublisherValues( $record['name'], $record['id'], $record['api_detail_url']);
					}

					if ( isset($record['image']['tiny_url']) ) {
						$this->setMeta(
							ComicVineImporter::META_IMPORT_PUB_ROOT . ComicVineImporter::META_IMPORT_SMALL_ICON,
							$record['image']['tiny_url']
						);
					}

					if ( isset($record['image']['icon_url']) ) {
						$this->setMeta(
							ComicVineImporter::META_IMPORT_PUB_ROOT . ComicVineImporter::META_IMPORT_LARGE_ICON,
							$record['image']['icon_url']
						);
					}
				}
			}

			$publish_model = Model::Named('Publisher');
			$publishObj = false;
			$publisherName = $this->getMeta(ComicVineImporter::META_IMPORT_PUB_ROOT . ComicVineImporter::META_IMPORT_NAME);
			$publisherSrc = $this->getMeta(ComicVineImporter::META_IMPORT_PUB_ROOT . ComicVineImporter::META_IMPORT_XSRC);
			$publisherUrl = $this->getMeta(ComicVineImporter::META_IMPORT_PUB_ROOT . ComicVineImporter::META_IMPORT_XURL);

			$publishObj = $publish_model->findExternalOrCreate( $publisherName, $publisherId, $publisherSrc, $publisherUrl);
			if ( $publishObj != false ) {
				if ( $this->isMeta(ComicVineImporter::META_IMPORT_PUB_ROOT . ComicVineImporter::META_IMPORT_SMALL_ICON) ) {
					$this->importImage(
						$publishObj,
						Publisher::small_icon_name,
						$this->getMeta(ComicVineImporter::META_IMPORT_PUB_ROOT . ComicVineImporter::META_IMPORT_SMALL_ICON)
					);
				}

				if ( $this->isMeta(ComicVineImporter::META_IMPORT_PUB_ROOT . ComicVineImporter::META_IMPORT_LARGE_ICON) ) {
					$this->importImage(
						$publishObj,
						Publisher::large_icon_name,
						$this->getMeta(ComicVineImporter::META_IMPORT_PUB_ROOT . ComicVineImporter::META_IMPORT_LARGE_ICON)
					);
				}
			}
		}

		return true;
	}

	public function processData()
	{
		if ( $this->processPublisher() ) {
			return true;
		}
		return $false;
	}

}
