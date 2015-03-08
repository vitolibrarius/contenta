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
use model\Character as Character;
use model\Endpoint as Endpoint;
use model\Endpoint_Type as Endpoint_Type;
use model\EndpointDBO as EndpointDBO;

class ComicVineImporter extends EndpointImporter
{
	const META_IMPORT_ROOT = 	'import/';
// 	const META_IMPORT_CHARACTER_ROOT = 	'import/character/';

	const META_IMPORT_FORCE_ICON =	'image/force';
	const META_IMPORT_SMALL_ICON =	'image/small';
	const META_IMPORT_LARGE_ICON =	'image/large';

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
				$oldfile = $mediaObject->mediaPath($mediaObject->{$imagename});
				if ( is_file($oldfile) ) {
					unlink($oldfile) || die("failed to remove old file? " . $oldfile);
				}

				$newfile = $mediaObject->mediaPath($filename);
				rename(appendPath($this->workingDirectory(), $filename), $newfile) || die("failed install new file? " . $newfile);

				if ( $mediaObject->model()->updateObject( $mediaObject, array( $imagename => $filename )) ) {
					return $mediaObject->model()->refreshObject($mediaObject);
				}
			}
		}
		return false;
	}

	/** Import maps between ComicVine keypaths and model attributes */
	public function genderMap($code = "3")
	{
		$cv_gender = array(
			1 => "Male",
			2 => "Female",
			3 => "Other"
		);

		if ( isset($code) && is_numeric($code)) {
			$code = intval($code);
			if ( array_key_exists($code, $cv_gender) ) {
				return $cv_gender[$code];
			}
		}
		return $code;
	}

	public function importPublisherMap()
	{
		// comicvine => model attribute
		return array( //,
			"id" => Publisher::xid,
			"api_detail_url" => Publisher::xurl,
			"deck" => "desc",
			"name" => Publisher::name,
			"story_arcs" => "story_arcs",
			"image/tiny_url" => ComicVineImporter::META_IMPORT_SMALL_ICON,
			"image/icon_url" => ComicVineImporter::META_IMPORT_LARGE_ICON
		);
	}

	public function importCharacterMap()
	{
		// comicvine => model attribute
		return array( //,
			"aliases" => "aliases",
			"id" => Character::xid,
			"api_detail_url" => Character::xurl,
			"deck" => Character::desc,
			"gender" => Character::gender,
			"name" => Character::name,
			"real_name" => Character::realname,
			"story_arc_credits" => "story_arc_credits",
			"image/tiny_url" => ComicVineImporter::META_IMPORT_SMALL_ICON,
			"image/icon_url" => ComicVineImporter::META_IMPORT_LARGE_ICON,
			"publisher/id" => Publisher::TABLE . '_' . Publisher::xid
		);
	}




	/**
	 * Set meta data for async processing
	 */
	public function importStoryArcValues($name = null, $xid, $xurl = null, $forceImages = false)
	{
		if ( isset($xid) == false || strlen($xid) == 0) {
			throw new Exception("External ID is required");
		}

		$root = appendPath( ComicVineImporter::META_IMPORT_ROOT, "story_arc", $xid);
		$cvDetails = $this->getMeta( $root );
		if ( isset($cvDetails, $cvDetails[Publisher::name]) == false) {
			$this->setMeta( appendPath( $root, Publisher::name), $name);
			$this->setMeta( appendPath( $root, Publisher::xid), $xid);
			$this->setMeta( appendPath( $root, Publisher::xsource), Endpoint_Type::ComicVine);
			$this->setMeta( appendPath( $root, Publisher::xurl), $xurl);
			$this->setMeta( appendPath( $root, ComicVineImporter::META_IMPORT_FORCE_ICON), $forceImages );
		}

		return $root;
	}

	public function importPublisherValues($name = null, $xid, $xurl = null, $forceImages = false)
	{
		if ( isset($xid) == false || strlen($xid) == 0) {
			throw new Exception("External ID is required");
		}

		$root = appendPath( ComicVineImporter::META_IMPORT_ROOT, Publisher::TABLE, $xid);
		$cvDetails = $this->getMeta( $root );
		if ( isset($cvDetails, $cvDetails[Publisher::name]) == false) {
			$this->setMeta( appendPath( $root, Publisher::name), $name);
			$this->setMeta( appendPath( $root, Publisher::xid), $xid);
			$this->setMeta( appendPath( $root, Publisher::xsource), Endpoint_Type::ComicVine);
			$this->setMeta( appendPath( $root, Publisher::xurl), $xurl);
			$this->setMeta( appendPath( $root, ComicVineImporter::META_IMPORT_FORCE_ICON), $forceImages );
		}

		return $root;
	}

	public function importCharacterValues($name = null, $xid = null, $xurl = null, $forceImages = false)
	{
		if ( isset($xid) == false || strlen($xid) == 0) {
			throw new Exception("External ID is required");
		}

		$root = appendPath( ComicVineImporter::META_IMPORT_ROOT, Character::TABLE, $xid);
		$cvDetails = $this->getMeta( $root );
		if ( isset($cvDetails, $cvDetails[Character::name]) == false) {
			$this->setMeta( appendPath( $root, Character::name), $name);
			$this->setMeta( appendPath( $root, Character::xid), $xid);
			$this->setMeta( appendPath( $root, Character::xsource), Endpoint_Type::ComicVine);
			$this->setMeta( appendPath( $root, Character::xurl), $xurl);
			$this->setMeta( appendPath( $root, ComicVineImporter::META_IMPORT_FORCE_ICON), $forceImages );
		}
		return $root;
	}

	/**
	 * Perform ComicVine processing actions.  Can be called sync or async
	 */
	private function preprocess_story_arc($path, array $cvDetails = array())
	{
		// this should never happen since the xid is required as part of the path
		if ( isset($cvDetails["xid"]) ) {
			// with no name means we probably need to fetch from ComicVine
			if ( isset($cvDetails["name"]) == false) {
				$story_arcId = $cvDetails["xid"];
				// load the details from ComicVine
				$comicVine = $this->endpoint();
				if ( $comicVine == false ) {
					throw new Exception("Endpoint was not found");
				}
				else {
					$connection = new ComicVineConnector($comicVine);
					$record = $connection->storyArcDetails( $story_arcId );
					if ( $record == false ) {
						return $record;
					}

					$this->setMeta( "cv/story_arc/" . $story_arcId, $record );
// 					$map = $this->importPublisherMap();
// 					$this->setMeta( appendPath( $path, Publisher::xsource), Endpoint_Type::ComicVine);
// 					foreach( $map as $cvKey => $modelKey ) {
// 						$value = valueForKeypath($cvKey, $record);
// 						$this->setMeta( appendPath( $path, $modelKey), $value );
// 					}
				}
			}
		}
		return true;
	}

	private function preprocess_publisher($path, array $cvDetails = array())
	{
		// this should never happen since the xid is required as part of the path
		if ( isset($cvDetails[Publisher::xid]) ) {
			// with no name means we probably need to fetch from ComicVine
			if ( isset($cvDetails[Publisher::name]) == false) {
				$publisherId = $cvDetails[Publisher::xid];
				// load the details from ComicVine
				$comicVine = $this->endpoint();
				if ( $comicVine == false ) {
					throw new Exception("Endpoint was not found");
				}
				else {
					$connection = new ComicVineConnector($comicVine);
					$record = $connection->publisherDetails( $publisherId );
					if ( $record == false ) {
						return $record;
					}

					$this->setMeta( "cv/publisher/" . $publisherId, $record );
					$map = $this->importPublisherMap();
					$this->setMeta( appendPath( $path, Publisher::xsource), Endpoint_Type::ComicVine);
					foreach( $map as $cvKey => $modelKey ) {
						$value = valueForKeypath($cvKey, $record);
						$this->setMeta( appendPath( $path, $modelKey), $value );
					}

					$storyArcs = valueForKeypath( "story_arcs", $record );
					if ( is_array($storyArcs) && count($storyArcs) > 0) {
						foreach( $storyArcs as $arc ) {
							if ( isset($arc['id']) ) {
								// story arcs do not automatically get all details
								$this->importStoryArcValues($arc['name'], $arc['id'], $arc['api_detail_url']);
							}
						}
					}
				}
			}
		}
		return true;
	}

	private function preprocess_character($path, array $cvDetails = array())
	{
		// this should never happen since the xid is required as part of the path
		if ( isset($cvDetails[Character::xid]) ) {
			// with no name means we probably need to fetch from ComicVine
			if ( isset($cvDetails[Character::name]) == false) {
				$characterId = $cvDetails[Character::xid];
				// load the details from ComicVine
				$comicVine = $this->endpoint();
				if ( $comicVine == false ) {
					throw new Exception("Endpoint was not found");
				}
				else {
					$connection = new ComicVineConnector($comicVine);
					$record = $connection->characterDetails( $characterId );
					if ( $record == false ) {
						return $record;
					}

					$this->setMeta( "cv/character/" . $characterId, $record );
					$map = $this->importCharacterMap();
					$this->setMeta( appendPath( $path, Character::xsource), Endpoint_Type::ComicVine);
					foreach( $map as $cvKey => $modelKey ) {
						$value = valueForKeypath($cvKey, $record);
						$this->setMeta( appendPath( $path, $modelKey), $value );
					}

					// check for Publisher
					$publisher_xid = valueForKeypath( "publisher/id", $record );
					if ( is_null($publisher_xid) == false ) {
						$pubObj = Model::Named("Publisher")->objectForExternal( $publisher_xid, Endpoint_Type::ComicVine );
						if ( $pubObj == false ) {
							$pubroot = $this->importPublisherValues( null, $publisher_xid);
						}
						else {
							Logger::logWarning( "Publisher found for " . $publisher_xid . " => " . $pubObj );
						}
					}
					else {
						Logger::logWarning( "No publisher xid found" );
					}

					$storyArcs = valueForKeypath( "story_arc_credits", $record );
					if ( is_array($storyArcs) && count($storyArcs) > 0) {
						foreach( $storyArcs as $arc ) {
							if ( isset($arc['id']) ) {
								// story arcs do not automatically get all details
								$this->importStoryArcValues($arc['name'], $arc['id'], $arc['api_detail_url']);
							}
						}
					}
				}
			}
			else {
				Logger::logError( "Name is set details for " . $path . var_export($cvDetails, true), get_class($this), $this->guid );
			}
		}
		else {
			Logger::logError( "No details for " . $path, get_class($this), $this->guid );
		}
		return true;
	}

	/**
	 * Perform actual processing actions loading the comicVine data into the database.
	 * should return the created/updated object, false on error, or null if no action takes place
	 */
	private function process_story_arc($path, array $cvDetails = array())
	{
		return null;
	}

	private function process_publisher($path, array $cvDetails = array())
	{
		$object = null;
		if ( isset($cvDetails[Publisher::xid], $cvDetails[Publisher::name]) ) {
			$publish_model = Model::Named('Publisher');
			$publisherName = valueForKeypath(Publisher::name, $cvDetails);
			$publisherId = valueForKeypath(Publisher::xid, $cvDetails);
			$publisherSrc = valueForKeypath(Publisher::xsource, $cvDetails);
			$publisherUrl = valueForKeypath(Publisher::xurl, $cvDetails);

			$object = $publish_model->findExternalOrCreate( $publisherName, $publisherId, $publisherSrc, $publisherUrl);
			if ( $object != false ) {
				$this->addImportsProcessed($object);

				$forceImageUpdate = valueForKeypath(ComicVineImporter::META_IMPORT_FORCE_ICON, $cvDetails);
				if ( $forceImageUpdate == true || $object->hasIcons() == false ) {
					$imageURL = valueForKeypath(ComicVineImporter::META_IMPORT_SMALL_ICON, $cvDetails);
					if ( is_null($imageURL) == false ) {
						$this->importImage( $object, Publisher::small_icon_name, $imageURL );
					}

					$imageURL = valueForKeypath(ComicVineImporter::META_IMPORT_LARGE_ICON, $cvDetails);
					if ( is_null($imageURL) == false ) {
						$this->importImage( $object, Publisher::large_icon_name, $imageURL );
					}
				}
			}
		}
		else {
			Logger::logError( "Unable to import publisher.  incomplete details " . var_export( $cvDetails, true), get_class($this), $this->guid );
		}

		return $object;
	}

	private function process_character($path, array $cvDetails = array())
	{
		$object = null;
		if ( isset($cvDetails[Character::xid], $cvDetails[Character::name]) ) {
			$character_model = Model::Named('Character');

			$name = valueForKeypath(Character::name, $cvDetails);
			$realname = valueForKeypath(Character::realname, $cvDetails);
			$desc = valueForKeypath(Character::desc, $cvDetails);
			$xid = valueForKeypath(Character::xid, $cvDetails);
			$xsrc = valueForKeypath(Character::xsource, $cvDetails);
			$xurl = valueForKeypath(Character::xurl, $cvDetails);

			$publisher = null;
			$publisherId = valueForKeypath( Publisher::TABLE . '_' . Publisher::xid, $cvDetails);
			if ( is_null( $publisherId ) == false ) {
				$publisher = Model::Named("Publisher")->objectForExternal( $publisherId, Endpoint_Type::ComicVine );
			}

			$aliases = valueForKeypath("aliases", $cvDetails);
			if ( isset($aliases) && strlen($aliases) > 0 ) {
				$aliases = split_lines($aliases);
			}

			$gender = $this->genderMap(valueForKeypath(Character::gender, $cvDetails));
			$object = $character_model->findExternalOrCreate( $publisher, $name, $realname, $gender, $desc, $aliases, $xid, $xsrc, $xurl);
			if ( $object != false ) {
				$this->addImportsProcessed($object);

				$forceImageUpdate = valueForKeypath(ComicVineImporter::META_IMPORT_FORCE_ICON, $cvDetails);
				if ( $forceImageUpdate == true || $object->hasIcons() == false ) {
					$imageURL = valueForKeypath(ComicVineImporter::META_IMPORT_SMALL_ICON, $cvDetails);
					if ( is_null($imageURL) == false ) {
						$this->importImage( $object, Character::small_icon_name, $imageURL );
					}

					$imageURL = valueForKeypath(ComicVineImporter::META_IMPORT_LARGE_ICON, $cvDetails);
					if ( is_null($imageURL) == false ) {
						$this->importImage( $object, Character::large_icon_name, $imageURL );
					}
				}
			}
		}

		return $object;
	}

	public function processData()
	{
		$preorder = array(
			Character::TABLE,
			Publisher::TABLE,
			"story_arc"
		);

		$success = true;
		$preorder_success = true;
		foreach( $preorder as $prekey ) {
			$root = appendPath( ComicVineImporter::META_IMPORT_ROOT, $prekey);
			$preprocessMethod = 'preprocess_' . $prekey;
			if (method_exists($this, $preprocessMethod)) {
				$cvImports = $this->getMeta($root);
				// each key in the imports is an externalId to be imported
				if ( is_null($cvImports) == false ) {
					foreach( $cvImports as $cvKey => $cvDetails ) {
						$itemPath = appendPath( $root, $cvKey );
						if ( $this->$preprocessMethod($itemPath, $cvDetails) == false ) {
							// we allow the preprocessing to continue since it won't harm anything
							$preorder_success = false;
						}
					}
				}
			}
			else {
				Logger::logError( "failed to find pre-processing function " . $preprocessMethod, get_class($this), $this->guid );
			}
		}

		if ( $preorder_success == true ) {
			$reverse = array_reverse($preorder);
			foreach( $reverse as $prekey ) {
				$root = appendPath( ComicVineImporter::META_IMPORT_ROOT, $prekey);
				$processMethod = 'process_' . $prekey;
				if (method_exists($this, $processMethod)) {
					$cvImports = $this->getMeta($root);
					// each key in the imports is an externalId to be imported
					if ( is_null($cvImports) == false ) {
						foreach( $cvImports as $cvKey => $cvDetails ) {
							$itemPath = appendPath( $root, $cvKey );
							if ( $this->$processMethod($itemPath, $cvDetails) == false ) {
								// we allow the preprocessing to continue since it won't harm anything
								$success = false;
							}
						}
					}
				}
				else {
					Logger::logError( "failed to find processing function " . $processMethod, get_class($this), $this->guid );
				}
			}
		}
		return $success;
	}

}
