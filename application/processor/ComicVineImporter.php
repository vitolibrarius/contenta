<?php

namespace processor;

use \Processor as Processor;
use \Migrator as Migrator;
use \Config as Config;
use \Logger as Logger;
use \Exception as Exception;
use \Model as Model;

use processor\EndpointImporter as EndpointImporter;
use connectors\ComicVineConnector as ComicVineConnector;

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

class ComicVineImporter extends ContentMetadataImporter
{
	public function setEndpoint(EndpointDBO $point = null)
	{
		if ( is_null($point) == false ) {
			$type = $point->type();
			if ( $type == false || $type->code != Endpoint_Type::ComicVine ) {
				throw new Exception("Endpoint " . $point->displayName() . " is is not for " . Endpoint_Type::ComicVine);
			}
		}
		parent::setEndpoint($point);
	}

	public function connection()
	{
		$comicVine = $this->endpoint();
		if ( $comicVine == false ) {
			throw new Exception("Endpoint was not found");
		}

		return new ComicVineConnector($comicVine);
	}

	/** Import maps between ComicVine keypaths and model attributes */
	public function descriptionForRecord( Array $cvRecord = array() )
	{
		$desc = $this->convert_desc(array_valueForKeypath("deck", $cvRecord));
		$fullDesc = array_valueForKeypath("description", $cvRecord);

		if ( is_null($desc) || strlen($desc) == 0) {
			$desc = $this->convert_desc( $fullDesc );
		}

		return $desc;
	}

	public function convert_gender($code = "3")
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

	public function convert_desc($desc = null)
	{
		if (isset($desc) && strlen($desc) > 0) {
			return strip_tags ( $desc );
		}

		return $desc;
	}

	public function convert_pub_date($coverDate = null)
	{
		return (is_string($coverDate) ? strtotime($coverDate) : $coverDate);
	}

	public function convert_issue_num($issueNum = null)
	{
		if (isset($issueNum) && strlen($issueNum) > 0) {
			return intval($issueNum);
		}

		return $issueNum;
	}

	public function convert_pub_count($pub_count = null)
	{
		if (isset($pub_count) && strlen($pub_count) > 0 && intval($pub_count) > 0) {
			return intval($pub_count);
		}

		return null;
	}


	public function convert_start_year($start_year = null)
	{
		if (isset($start_year) && strlen($start_year) == 4) {
			return intval($start_year);
		}

		return $start_year;
	}

	public function importMap_publisher()
	{
		// comicvine => model attribute
		return array( //,
			"id" 				=> Publisher::xid,
			"site_detail_url" 	=> Publisher::xurl,
			"name" 				=> Publisher::name,
			"deck" 				=> "desc", // not currently used
			"image/tiny_url" 	=> ComicVineImporter::META_IMPORT_SMALL_ICON,
			"image/icon_url" 	=> ComicVineImporter::META_IMPORT_LARGE_ICON
		);
	}

	public function importMap_character()
	{
		// comicvine => model attribute
		return array( //,
			"aliases"			=> "aliases",
			"id" 				=> Character::xid,
			"site_detail_url" 	=> Character::xurl,
			"deck" 				=> Character::desc,
			"description" 		=> "description",
			"gender" 			=> Character::gender,
			"name" 				=> Character::name,
			"real_name" 		=> Character::realname,
			"image/tiny_url" 	=> ComicVineImporter::META_IMPORT_SMALL_ICON,
			"image/icon_url" 	=> ComicVineImporter::META_IMPORT_LARGE_ICON
		);
	}

	public function importMap_series()
	{
		// comicvine => model attribute
		return array( //,
			"aliases"			=> "aliases",
			"id" 				=> Series::xid,
			"site_detail_url" 	=> Series::xurl,
			"deck" 				=> Series::desc,
			"description" 		=> "description",
			"name" 				=> Series::name,
			"start_year" 		=> Series::start_year,
			"image/tiny_url" 	=> ComicVineImporter::META_IMPORT_SMALL_ICON,
			"image/thumb_url" 	=> ComicVineImporter::META_IMPORT_LARGE_ICON
		);
	}

	public function importMap_publication()
	{
		// comicvine => model attribute
		return array( //,
			"id" 				=> Publication::xid,
			"name" 				=> Publication::name,
			"deck" 				=> Publication::desc,
			"description" 		=> "description",
			"cover_date" 		=> Publication::pub_date,
			"issue_number" 		=> Publication::issue_num,
			"site_detail_url" 	=> Publication::xurl,
			"image/icon_url" 	=> ComicVineImporter::META_IMPORT_SMALL_ICON,
			"image/thumb_url"	=> ComicVineImporter::META_IMPORT_LARGE_ICON
		);
	}

	public function importMap_story_arc()
	{
		// comicvine => model attribute
		return array( //,
			"id" 				=> Story_Arc::xid,
			"name"				=> Story_Arc::name,
			"deck" 				=> Story_Arc::desc,
			"description" 		=> "description",
			"count_of_issue_appearances" => Story_Arc::pub_count,
			"site_detail_url"	=> Story_Arc::xurl,
			"image/tiny_url" 	=> ComicVineImporter::META_IMPORT_SMALL_ICON,
			"image/icon_url" 	=> ComicVineImporter::META_IMPORT_LARGE_ICON
		);
	}

	/** PRE-PROCESSING
	 * imports a minimal record that can later be fleshed out
	 */
	public function preprocessRelationship( $model = null, $path = "error", array $cvData = array(), array $map = array(), $forceMeta = false, $forceImages = false )
	{
		if ( is_null($model) || ($model instanceof \Model) == false) {
			throw new Exception("Destination Model is required " . var_export($model, true));
		}

		// this is a little different because the xid from CV is the id
		if ( isset($cvData['id']) == false || strlen($cvData['id']) == 0) {
			throw new Exception("External ID 'id' is required " . var_export($cvData, true));
		}

		// relationship reference
		$relations_key = $model->tableName() . "_" . $cvData['id'];
		$relation_path = appendPath( $path, ContentMetadataImporter::META_IMPORT_RELATIONSHIP, $relations_key );
		if ( $this->isMeta($relation_path) == false ) {
			$this->setMeta( $relation_path, $cvData['id'] );
		}

		// ensure the related record is enqueued for import
		$importValues = array();
		foreach( $map as $cvKey => $modelKey ) {
			$value = array_valueForKeypath($cvKey, $cvData);
			$convert_method = 'convert_' . $modelKey;
			if (method_exists($this, $convert_method)) {
				$value = $this->$convert_method( $value );
			}

			// set the enqueued values for pre-processing
			if ( is_null( $value ) == false ) {
				$importValues[$modelKey] = $value;
			}
		}

		return $this->enqueue( $model, $importValues, $forceMeta, $forceImages );

	}

	public function preprocess_publisher(array $metaRecord = array())
	{
		$object = parent::preprocess(Model::Named("Publisher"), $metaRecord);

		if ( $object != null ) {
			$forceMeta = array_valueForKeypath( ContentMetadataImporter::META_IMPORT_FORCE, $metaRecord );
			$forceImages = array_valueForKeypath( ContentMetadataImporter::META_IMPORT_FORCE_ICON, $metaRecord );
			$xid = array_valueForKeypath( ContentMetadataImporter::META_IMPORT_XID, $metaRecord );

			$path = appendPath( ContentMetadataImporter::META_DATA_ROOT, Publisher::TABLE . '_' . $xid );
			if ( $forceMeta === true ) {
				$connection = $this->connection();
				$record = $connection->publisherDetails( $xid );
				if ( $record == false ) {
					throw new Exception("Connection failed to find publisher for " . $xid);
				}

				$map = $this->importMap_publisher();
				foreach( $map as $cvKey => $modelKey ) {
					$value = array_valueForKeypath($cvKey, $record);
					$convert_method = 'convert_' . $modelKey;
					if (method_exists($this, $convert_method)) {
						$value = $this->$convert_method( $value );
					}

					$this->setMeta( appendPath( $path, $modelKey), $value );
				}
				$this->setMeta( appendPath($path, Publisher::xupdated), time() );

				$descPath = appendPath( $path, "desc");
				$this->setMeta( $descPath, $this->descriptionForRecord( $record) );
			}
		}

		return $object;
	}

	public function preprocess_character(array $metaRecord = array())
	{
		$object = parent::preprocess(Model::Named("Character"), $metaRecord);

		if ( $object != null ) {
			$forceMeta = array_valueForKeypath( ContentMetadataImporter::META_IMPORT_FORCE, $metaRecord );
			$forceImages = array_valueForKeypath( ContentMetadataImporter::META_IMPORT_FORCE_ICON, $metaRecord );
			$xid = array_valueForKeypath( ContentMetadataImporter::META_IMPORT_XID, $metaRecord );

			$path = appendPath( ContentMetadataImporter::META_DATA_ROOT, Character::TABLE . '_' . $xid );
			if ( $forceMeta === true ) {
				$connection = $this->connection();
				$record = $connection->characterDetails( $xid );
				if ( $record == false ) {
					// try minimal fields
					$record = $connection->characterDetails( $xid, true );
					if ( $record == false ) {
						throw new Exception("Connection failed to find character for " . $xid);
					}
				}

				$map = $this->importMap_character();
				foreach( $map as $cvKey => $modelKey ) {
					$value = array_valueForKeypath($cvKey, $record);
					$convert_method = 'convert_' . $modelKey;
					if (method_exists($this, $convert_method)) {
						$value = $this->$convert_method( $value );
					}
					$this->setMeta( appendPath( $path, $modelKey), $value );
				}
				$this->setMeta( appendPath($path, Character::xupdated), time() );

				$descPath = appendPath( $path, "desc");
				$this->setMeta( $descPath, $this->descriptionForRecord( $record) );

				$pubReference = array_valueForKeypath("publisher", $record);
				if ( is_array($pubReference) ) {
					$pubEnqueued = $this->preprocessRelationship( Model::Named('Publisher'), $path, $pubReference, $this->importMap_publisher() );
				}

				$story_arc_array = array_valueForKeypath("story_arc_credits", $record);
				if ( is_array($story_arc_array) ) {
					foreach ( $story_arc_array as $story_arc ) {
						$enqueued = $this->preprocessRelationship( Model::Named('Story_Arc'), $path, $story_arc, $this->importMap_story_arc() );
					}
				}
			}
		}

		return $object;
	}

	public function preprocess_series(array $metaRecord = array())
	{
		$object = parent::preprocess(Model::Named("Series"), $metaRecord);

		if ( $object != null ) {
			$forceMeta = array_valueForKeypath( ContentMetadataImporter::META_IMPORT_FORCE, $metaRecord );
			$forceImages = array_valueForKeypath( ContentMetadataImporter::META_IMPORT_FORCE_ICON, $metaRecord );
			$xid = array_valueForKeypath( ContentMetadataImporter::META_IMPORT_XID, $metaRecord );

			$path = appendPath( ContentMetadataImporter::META_DATA_ROOT, Series::TABLE . '_' . $xid );
			if ( $forceMeta === true ) {
				$connection = $this->connection();
				$record = $connection->seriesDetails( $xid );
				if ( $record == false ) {
					$record = $connection->seriesDetails( $xid, true );
					if ( $record == false ) {
						throw new Exception("Connection failed to find series for " . $xid);
					}
				}

				$map = $this->importMap_series();
				foreach( $map as $cvKey => $modelKey ) {
					$value = array_valueForKeypath($cvKey, $record);
					$convert_method = 'convert_' . $modelKey;
					if (method_exists($this, $convert_method)) {
						$value = $this->$convert_method( $value );
					}

					$this->setMeta( appendPath( $path, $modelKey), $value );
				}
				$this->setMeta( appendPath($path, Series::xupdated), time() );

				$descPath = appendPath( $path, "desc");
				$this->setMeta( $descPath, $this->descriptionForRecord( $record) );

				$pubReference = array_valueForKeypath("publisher", $record);
				if ( is_array($pubReference) ) {
					$pubEnqueued = $this->preprocessRelationship( Model::Named('Publisher'), $path, $pubReference, $this->importMap_publisher() );
				}

				$character_array = array_valueForKeypath("characters", $record);
				if ( is_array($character_array) ) {
					foreach ( $character_array as $character ) {
						$enqueued = $this->preprocessRelationship( Model::Named('Character'), $path, $character, $this->importMap_character() );
					}
				}

				$issues_array = array_valueForKeypath("issues", $record);
				if ( is_array($issues_array) ) {
					$pubModel = Model::Named('Publication');
					foreach ( $issues_array as $issue ) {
						$destinationNeedsUpdate = $object->isWanted();
						if ( $object->isWanted() ) {
							$pub_obj = $pubModel->objectForExternal($issue['id'], $this->endpointTypeCode());
							if ( $pub_obj instanceof DataObject ) {
								$destinationNeedsUpdate = $pub_obj->needsEndpointUpdate();
							}
						}

						$enqueued = $this->preprocessRelationship(
							$pubModel,
							$path,
							$issue,
							$this->importMap_publication(),
							$destinationNeedsUpdate,
							$destinationNeedsUpdate
						);
					}
				}
			}
		}

		return $object;
	}

	public function preprocess_story_arc(array $metaRecord = array())
	{
		$object = parent::preprocess(Model::Named("Story_Arc"), $metaRecord);

		if ( $object != null ) {
			$forceMeta = array_valueForKeypath( ContentMetadataImporter::META_IMPORT_FORCE, $metaRecord );
			$forceImages = array_valueForKeypath( ContentMetadataImporter::META_IMPORT_FORCE_ICON, $metaRecord );
			$xid = array_valueForKeypath( ContentMetadataImporter::META_IMPORT_XID, $metaRecord );

			$path = appendPath( ContentMetadataImporter::META_DATA_ROOT, Story_Arc::TABLE . '_' . $xid );
			if ( $forceMeta === true ) {
				$connection = $this->connection();
				$record = $connection->story_arcDetails( $xid );
				if ( $record == false ) {
					throw new Exception("Connection failed to find story_arc for " . $xid);
				}

				$map = $this->importMap_story_arc();
				foreach( $map as $cvKey => $modelKey ) {
					$value = array_valueForKeypath($cvKey, $record);
					$convert_method = 'convert_' . $modelKey;
					if (method_exists($this, $convert_method)) {
						$value = $this->$convert_method( $value );
					}

					$this->setMeta( appendPath( $path, $modelKey), $value );
				}
				$this->setMeta( appendPath($path, Story_Arc::xupdated), time() );

				$descPath = appendPath( $path, "desc");
				$this->setMeta( $descPath, $this->descriptionForRecord( $record) );

				$pubReference = array_valueForKeypath("publisher", $record);
				if ( is_array($pubReference) ) {
					$pubEnqueued = $this->preprocessRelationship( Model::Named('Publisher'), $path, $pubReference, $this->importMap_publisher() );
				}

				// only preload the publications if the story_arc is wanted for download
				$issuesReference = array_valueForKeypath("issues", $record);
				if ( is_array($issuesReference) && $object->isWanted() ) {
					foreach ( $issuesReference as $issue ) {
						$enqueued = $this->preprocessRelationship(
							Model::Named('Publication'),
							$path,
							$issue,
							$this->importMap_publication(),
							true,
							true
						);
					}
				}
			}
		}

		return $object;
	}

	public function preprocess_publication(array $metaRecord = array())
	{
		$object = parent::preprocess(Model::Named("Publication"), $metaRecord);

		if ( $object != null ) {
			$forceMeta = array_valueForKeypath( ContentMetadataImporter::META_IMPORT_FORCE, $metaRecord );
			$forceImages = array_valueForKeypath( ContentMetadataImporter::META_IMPORT_FORCE_ICON, $metaRecord );
			$xid = array_valueForKeypath( ContentMetadataImporter::META_IMPORT_XID, $metaRecord );

			$path = appendPath( ContentMetadataImporter::META_DATA_ROOT, Publication::TABLE . '_' . $xid );
			if ( $forceMeta === true ) {
				$connection = $this->connection();
				$record = $connection->issueDetails( $xid );
				if ( $record == false ) {
					throw new Exception("Connection failed to find publication for " . $xid);
				}

				$map = $this->importMap_publication();
				foreach( $map as $cvKey => $modelKey ) {
					$value = array_valueForKeypath($cvKey, $record);
					$convert_method = 'convert_' . $modelKey;
					if (method_exists($this, $convert_method)) {
						$value = $this->$convert_method( $value );
					}

					$this->setMeta( appendPath( $path, $modelKey), $value );
				}
				$this->setMeta( appendPath($path, Publication::xupdated), time() );

				$descPath = appendPath( $path, "desc");
				$this->setMeta( $descPath, $this->descriptionForRecord( $record) );

				$pubReference = array_valueForKeypath("publisher", $record);
				if ( is_array($pubReference) ) {
					$pubEnqueued = $this->preprocessRelationship( Model::Named('Publisher'), $path, $pubReference, $this->importMap_publisher() );
				}

				$seriesReference = array_valueForKeypath("volume", $record);
				if ( is_array($seriesReference) ) {
					$seriesEnqueued = $this->preprocessRelationship( Model::Named('Series'), $path, $seriesReference, $this->importMap_series() );
				}

				$story_arc_array = array_valueForKeypath("story_arc_credits", $record);
				if ( is_array($story_arc_array) ) {
					foreach ( $story_arc_array as $story_arc ) {
						$enqueued = $this->preprocessRelationship( Model::Named('Story_Arc'), $path, $story_arc, $this->importMap_story_arc() );
					}
				}

				$character_array = array_valueForKeypath("character_credits", $record);
				if ( is_array($character_array) ) {
					foreach ( $character_array as $character ) {
						$enqueued = $this->preprocessRelationship( Model::Named('Character'), $path, $character, $this->importMap_character() );
					}
				}
			}
		}

		return $object;
	}


	/** FINALIZE
	 * complete the import for each object
	 */
	public function finalize_publisher(array $metaRecord = array())
	{
		$object = parent::finalize(Model::Named("Publisher"), $metaRecord);

		return $object;
	}

	public function finalize_character(array $metaRecord = array())
	{
		$object = parent::finalize(Model::Named("Character"), $metaRecord);
		if ( $object instanceof model\CharacterDBO ) {
			$relationships = array_valueForKeypath(ContentMetadataImporter::META_IMPORT_RELATIONSHIP, $metaRecord);
			if ( is_array($relationships) ) {
				foreach( $relationships as $path => $relatedId ) {
					$table = substr($path, 0, strrpos($path, '_'));
					$related_model = Model::Named( $table );
					if ( $related_model == null ) {
						throw new Exception( "failed to find model for " . var_export( $relation, true ));
					}

					$relatedObj = $related_model->objectForExternal($relatedId, $this->endpointTypeCode());
					if ( $relatedObj == false ) {
						throw new Exception( "failed to find object for " . var_export( $relation, true ));
					}

					switch( $table ) {
						case "publisher":
							$object->setPublisher( $relatedObj );
							break;
						case "story_arc":
							if ( $object->isWanted() ) {
								$object->joinToStory_Arc( $relatedObj );
							}
							break;
						default:
							Logger::logError( "$object Unknown relationship $table", $this->type, $this->guid );
							break;
					}
				}
			}
		}

		return $object;
	}

	public function finalize_series(array $metaRecord = array())
	{
		$object = parent::finalize(Model::Named("Series"), $metaRecord);
		if ( $object instanceof model\SeriesDBO ) {
			$relationships = array_valueForKeypath(ContentMetadataImporter::META_IMPORT_RELATIONSHIP, $metaRecord);
			if ( is_array($relationships) ) {
				foreach( $relationships as $path => $relatedId ) {
					$table = substr($path, 0, strrpos($path, '_'));
					$related_model = Model::Named( $table );
					if ( $related_model == null ) {
						throw new Exception( "failed to find model for " . var_export( $relation, true ));
					}

					$relatedObj = $related_model->objectForExternal($relatedId, $this->endpointTypeCode());
					if ( $relatedObj == false ) {
						throw new Exception( "failed to find related object for $table->xid = " . var_export( $relatedId, true ));
					}

					switch( $table ) {
						case "publisher":
							$object->setPublisher( $relatedObj );
							break;
						case "publication":
							$relatedObj->setSeries( $object );
							break;
						case "character":
							$object->joinToCharacter( $relatedObj );
							break;
						case "story_arc":
							$object->joinToStory_Arc( $relatedObj );
							break;
						default:
							Logger::logError( "$object Unknown relationship $table", $this->type, $this->guid );
							break;
					}
				}
			}
		}

		return $object;
	}

	public function finalize_story_arc(array $metaRecord = array())
	{
		$object = parent::finalize(Model::Named("Story_Arc"), $metaRecord);
		if ( $object instanceof model\Story_ArcDBO ) {
			$relationships = array_valueForKeypath(ContentMetadataImporter::META_IMPORT_RELATIONSHIP, $metaRecord);
			if ( is_array($relationships) ) {
				foreach( $relationships as $path => $relatedId ) {
					$table = substr($path, 0, strrpos($path, '_'));
					$related_model = Model::Named( $table );
					if ( $related_model == null ) {
						throw new Exception( "failed to find model for " . var_export( $relation, true ));
					}

					$relatedObj = $related_model->objectForExternal($relatedId, $this->endpointTypeCode());
					if ( $relatedObj == false ) {
						throw new Exception( "failed to find object for " . var_export( $relation, true ));
					}

					switch( $table ) {
						case "publisher":
							$object->setPublisher( $relatedObj );
							break;
						case "publication":
							$object->joinToPublication( $relatedObj );
							break;
						default:
							Logger::logError( "$object Unknown relationship $table", $this->type, $this->guid );
							break;
					}
				}
			}
		}

		return $object;
	}

	public function finalize_publication(array $metaRecord = array())
	{
		$object = parent::finalize(Model::Named("Publication"), $metaRecord);
		if ( $object instanceof model\PublicationDBO ) {
			$relationships = array_valueForKeypath(ContentMetadataImporter::META_IMPORT_RELATIONSHIP, $metaRecord);
			if ( is_array($relationships) ) {
				foreach( $relationships as $path => $relatedId ) {
					$table = substr($path, 0, strrpos($path, '_'));
					$related_model = Model::Named( $table );
					if ( $related_model == null ) {
						throw new Exception( "failed to find model for " . var_export( $relation, true ));
					}

					$relatedObj = $related_model->objectForExternal($relatedId, $this->endpointTypeCode());
					if ( $relatedObj == false ) {
						throw new Exception( "failed to find object for " . var_export( $relation, true ));
					}

					switch( $table ) {
						case "publisher":
							$object->setPublisher( $relatedObj );
							break;
						case "series":
							$object->setSeries( $relatedObj );
							break;
						case "character":
							$object->joinToCharacter( $relatedObj );
							break;
						case "story_arc":
							$object->joinToStory_Arc( $relatedObj );
							break;
						default:
							Logger::logError( "$object Unknown relationship $table", $this->type, $this->guid );
							break;
					}
				}
			}
		}

		return $object;
	}

	function refreshPublicationsForObject( $object = null )
	{
		if ( is_null($object) == false) {
			$object = $object->model()->refreshObject($object);
			$objTable = $object->tableName();
			$needsUpdate = $object->needsEndpointUpdate();
			$isWanted = $object->isWanted();

			$this->setJobDescription( "Refreshing publications related to " . $object->displayDescription() );

			if ( $needsUpdate == true || $isWanted == true ) {
				$enqueue_method = 'enqueue_' . $objTable;
				if (method_exists($this, $enqueue_method)) {
					$this->$enqueue_method( array( "xid" => $object->xid), true, true );
				}
			}

			if (method_exists($object, "publications")) {
				$publications = $object->publications();
				if ( is_array($publications) ) {
					foreach( $publications as $publication ) {
						$pubNeedsUpdate = $publication->needsEndpointUpdate();
						$this->enqueue_publication( array( "xid" => $publication->xid), $pubNeedsUpdate, $pubNeedsUpdate );
					}
				}
			}
		}
	}
}
