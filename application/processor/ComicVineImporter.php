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
use model\Series as Series;
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

	public function importImage( $mediaObject, $imagename = Model::IconName, $sourceurl )
	{
		if ( isset($sourceurl) ) {
			$filename = downloadImage($sourceurl, $this->workingDirectory(), $imagename );
			if ( empty($filename) == false ) {
				$imageFile = $mediaObject->imagePath($imagename);
				if ( is_file($imageFile) ) {
					unlink($imageFile) || die("failed to remove old file? " . $imageFile);
				}

				$newfile = $mediaObject->mediaPath($filename);
				rename(appendPath($this->workingDirectory(), $filename), $newfile) || die("failed install new file? " . $newfile);
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

	public function importSeriesMap()
	{
		// comicvine => model attribute
		return array( //,
			"aliases" => "aliases",
			"id" => Series::xid,
			"api_detail_url" => Series::xurl,
			"deck" => Series::desc,
			"name" => Series::name,
			"start_year" => Series::start_year,
			"image/tiny_url" => ComicVineImporter::META_IMPORT_SMALL_ICON,
			"image/thumb_url" => ComicVineImporter::META_IMPORT_LARGE_ICON,
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

	public function importSeriesValues($name = null, $xid = null, $xurl = null, $forceImages = false)
	{
		if ( isset($xid) == false || strlen($xid) == 0) {
			throw new Exception("External ID is required");
		}

		$root = appendPath( ComicVineImporter::META_IMPORT_ROOT, Series::TABLE, $xid);
		$cvDetails = $this->getMeta( $root );
		if ( isset($cvDetails, $cvDetails[Series::name]) == false) {
			$this->setMeta( appendPath( $root, Series::name), $name);
			$this->setMeta( appendPath( $root, Series::xid), $xid);
			$this->setMeta( appendPath( $root, Series::xsource), Endpoint_Type::ComicVine);
			$this->setMeta( appendPath( $root, Series::xurl), $xurl);
			$this->setMeta( appendPath( $root, ComicVineImporter::META_IMPORT_FORCE_ICON), $forceImages );
		}
		return $root;
	}

	public function importPublicationValues($name = null, $num = null, $xid = null, $xurl = null, $forceImages = false)
	{
		if ( isset($xid) == false || strlen($xid) == 0) {
			throw new Exception("External ID is required");
		}

		$root = appendPath( ComicVineImporter::META_IMPORT_ROOT, "publication", $xid);
		$cvDetails = $this->getMeta( $root );
		if ( isset($cvDetails, $cvDetails["name"]) == false) {
			$this->setMeta( appendPath( $root, "name"), $name);
			$this->setMeta( appendPath( $root, "issue_number"), $num);
			$this->setMeta( appendPath( $root, "xid"), $xid);
			$this->setMeta( appendPath( $root, "xsource"), Endpoint_Type::ComicVine);
			$this->setMeta( appendPath( $root, "xurl"), $xurl);
			$this->setMeta( appendPath( $root, ComicVineImporter::META_IMPORT_FORCE_ICON), $forceImages );
		}

// 		$root = appendPath( ComicVineImporter::META_IMPORT_ROOT, Publication::TABLE, $xid);
// 		$cvDetails = $this->getMeta( $root );
// 		if ( isset($cvDetails, $cvDetails[Publication::name]) == false) {
// 			$this->setMeta( appendPath( $root, Publication::name), $name);
// 			$this->setMeta( appendPath( $root, Publication::issue_number), $num);
// 			$this->setMeta( appendPath( $root, Publication::xid), $xid);
// 			$this->setMeta( appendPath( $root, Publication::xsource), Endpoint_Type::ComicVine);
// 			$this->setMeta( appendPath( $root, Publication::xurl), $xurl);
// 			$this->setMeta( appendPath( $root, ComicVineImporter::META_IMPORT_FORCE_ICON), $forceImages );
// 		}
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
		}
		else {
			Logger::logError( "No details for " . $path, get_class($this), $this->guid );
		}
		return true;
	}

	private function preprocess_series($path, array $cvDetails = array())
	{
		// this should never happen since the xid is required as part of the path
		if ( isset($cvDetails[Series::xid]) ) {
			// with no name means we probably need to fetch from ComicVine
			if ( isset($cvDetails[Series::name]) == false) {
				$xId = $cvDetails[Series::xid];
				// load the details from ComicVine
				$comicVine = $this->endpoint();
				if ( $comicVine == false ) {
					throw new Exception("Endpoint was not found");
				}
				else {
					$connection = new ComicVineConnector($comicVine);
					$record = $connection->seriesDetails( $xId );
					if ( $record == false ) {
						return $record;
					}

					$this->setMeta( "cv/series/" . $xId, $record );
					$map = $this->importSeriesMap();
					$this->setMeta( appendPath( $path, Series::xsource), Endpoint_Type::ComicVine);
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

					$characters = valueForKeypath( "characters", $record );
					if ( is_array($characters) && count($characters) > 0) {
						foreach( $characters as $character ) {
							if ( isset($character['id']) ) {
								// story arcs do not automatically get all details
								$characterName = (isset($character['name']) ? $character['name'] : "Unknown");
								$characterURL = (isset($character['api_detail_url']) ? $character['api_detail_url'] : null);
								$this->importCharacterValues($characterName, $character['id'], $characterURL);
							}
						}
					}

					$issues = valueForKeypath( "issues", $record );
					if ( is_array($issues) && count($issues) > 0) {
						foreach( $issues as $issue ) {
							if ( isset($issue['id']) ) {
								// story arcs do not automatically get all details
								$issueName = (isset($issue['name']) ? $issue['name'] : "Unknown");
								$issueNumber = (isset($issue['issue_number']) ? $issue['issue_number'] : "0");
								$issueURL = (isset($issue['api_detail_url']) ? $issue['api_detail_url'] : null);
								$this->importPublicationValues($issueName, $issueNumber, $issue['id'], $issueURL);
							}
						}
					}
				}
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
			if ( $object != false && is_array($object) == false) {
				$this->addImportsProcessed($object);

				$forceImageUpdate = valueForKeypath(ComicVineImporter::META_IMPORT_FORCE_ICON, $cvDetails);
				if ( $forceImageUpdate == true || $object->hasIcons() == false ) {
					$imageURL = valueForKeypath(ComicVineImporter::META_IMPORT_SMALL_ICON, $cvDetails);
					if ( is_null($imageURL) == false ) {
						$this->importImage( $object, Model::IconName, $imageURL );
					}

					$imageURL = valueForKeypath(ComicVineImporter::META_IMPORT_LARGE_ICON, $cvDetails);
					if ( is_null($imageURL) == false ) {
						$this->importImage( $object, Model::ThumbnailName, $imageURL );
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
			if ( $object != false && is_array($object) == false) {
				$this->addImportsProcessed($object);

				$forceImageUpdate = valueForKeypath(ComicVineImporter::META_IMPORT_FORCE_ICON, $cvDetails);
				if ( $forceImageUpdate == true || $object->hasIcons() == false ) {
					$imageURL = valueForKeypath(ComicVineImporter::META_IMPORT_SMALL_ICON, $cvDetails);
					if ( is_null($imageURL) == false ) {
						$this->importImage( $object, Model::IconName, $imageURL );
					}

					$imageURL = valueForKeypath(ComicVineImporter::META_IMPORT_LARGE_ICON, $cvDetails);
					if ( is_null($imageURL) == false ) {
						$this->importImage( $object, Model::ThumbnailName, $imageURL );
					}
				}
			}
		}

		return $object;
	}

	private function process_series($path, array $cvDetails = array())
	{
		$object = null;
		if ( isset($cvDetails[Series::xid], $cvDetails[Series::name]) ) {
			$series_model = Model::Named('Series');

			$name = valueForKeypath(Series::name, $cvDetails);
			$start_year = valueForKeypath(Series::start_year, $cvDetails);
			$desc = valueForKeypath(Series::desc, $cvDetails);
			$xid = valueForKeypath(Series::xid, $cvDetails);
			$xsrc = valueForKeypath(Series::xsource, $cvDetails);
			$xurl = valueForKeypath(Series::xurl, $cvDetails);

			$publisher = null;
			$publisherId = valueForKeypath( Publisher::TABLE . '_' . Publisher::xid, $cvDetails);
			if ( is_null( $publisherId ) == false ) {
				$publisher = Model::Named("Publisher")->objectForExternal( $publisherId, Endpoint_Type::ComicVine );
			}

			$aliases = valueForKeypath("aliases", $cvDetails);
			if ( isset($aliases) && strlen($aliases) > 0 ) {
				$aliases = split_lines($aliases);
			}

			$object = $series_model->findExternalOrCreate( $publisher, $name, $start_year, null, $xid, $xsrc, $xurl, $desc, $aliases);
			if ( $object != false && is_array($object) == false) {
				$this->addImportsProcessed($object);

				$forceImageUpdate = valueForKeypath(ComicVineImporter::META_IMPORT_FORCE_ICON, $cvDetails);
				if ( $forceImageUpdate == true || $object->hasIcons() == false ) {
					$imageURL = valueForKeypath(ComicVineImporter::META_IMPORT_SMALL_ICON, $cvDetails);
					if ( is_null($imageURL) == false ) {
						$this->importImage( $object, Model::IconName, $imageURL );
					}

					$imageURL = valueForKeypath(ComicVineImporter::META_IMPORT_LARGE_ICON, $cvDetails);
					if ( is_null($imageURL) == false ) {
						$this->importImage( $object, Model::ThumbnailName, $imageURL );
					}
				}
			}
			else {
				Logger::logError( "series findExternalOrCreate " . var_export($object, true), get_class($this), $this->guid );
			}
		}

		return $object;
	}


	public function processData()
	{
		$preorder = array(
			Series::TABLE,
			Character::TABLE,
			Publisher::TABLE,
			"story_arc"
		);

		$success = true;
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
							$success = false;
						}
					}
				}
			}
			else {
				Logger::logError( "failed to find pre-processing function " . $preprocessMethod, get_class($this), $this->guid );
				$success = false;
			}
		}

		if ( $success == true ) {
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
								Logger::logError( "Failed to process $processMethod($itemPath)", get_class($this), $this->guid );
								$success = false;
								break;
							}
						}
					}
				}
				else {
					Logger::logError( "failed to find processing function " . $processMethod, get_class($this), $this->guid );
					$success = false;
				}
			}
		}

		$this->setPurgeOnExit( $success );
		return $success;
	}

}
