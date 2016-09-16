<?php

namespace db;

use \Database as Database;
use \Localized as Localized;
use \Logger as Logger;
use \Model as Model;
use \Config as Config;
use \Metadata as Metadata;
use \DataObject as DataObject;

use \Exception as Exception;

use \model\media\Character as Character;
use \model\media\Character_Alias as Character_Alias;
use \model\network\Endpoint as Endpoint;
use \model\network\Endpoint_Type as Endpoint_Type;
use \model\network\Flux as Flux;
use \model\network\Rss as Rss;
use \model\jobs\Job as Job;
use \model\jobs\Job_Running as Job_Running;
use \model\jobs\Job_Type as Job_Type;
use \model\media\Log as Log;
use \model\media\Log_Level as Log_Level;
use \model\media\Media as Media;
use \model\media\Media_Type as Media_Type;
use \model\media\Network as Network;
use \model\media\Patch as Patch;
use \model\media\Publication as Publication;
use \model\media\Publication_Character as Publication_Character;
use \model\media\Publisher as Publisher;
use \model\media\Series as Series;
use \model\media\Series_Alias as Series_Alias;
use \model\media\Series_Character as Series_Character;
use \model\media\Story_Arc as Story_Arc;
use \model\media\Story_Arc_Character as Story_Arc_Character;
use \model\media\Story_Arc_Publication as Story_Arc_Publication;
use \model\media\Story_Arc_Series as Story_Arc_Series;
use \model\media\User_Network as User_Network;
use \model\user\Users as Users;
use \model\version as Version;

class ImportData
{
	public $importDir;
	public $modelDirectoryMap;
	public $primaryKeyMap;
	public $importOrder;

	public function __construct($importDir, array $importOrder = null)
	{
		isset($importDir) || die('Import directory is required.');
		is_dir($importDir) || die('Import directory does not exists.');

		$this->importDir = $importDir;
		if ( is_array($importOrder) && count($importOrder) > 0 ) {
			$this->importOrder = $importOrder;
		}
	}

	public function importOrder()
	{
		if ( isset($this->importOrder) ) {
			return $this->importOrder;
		}

		return array(
			"Version",
			"Patch",
			"Endpoint_Type",
			"Job_Type",
			"Log_Level",
			"Media_Type",

			"Endpoint",
			"Rss",
			"Flux",
			"Job",

			"Users",
			"Network",
			"User_Network",

			"Publisher",
			"Series",
			"Series_Alias",

			"Publication",

			"Character",
			"Character_Alias",
			"Publication_Character",
			"Series_Character",

			"Story_Arc",
			"Story_Arc_Character",
			"Story_Arc_Publication",
			"Story_Arc_Series",

			"Media",
		);
	}

	public function directoryFor( $tablename )
	{
		isset($tablename) || die('directory for tablename requires a table name.');
		$retval = appendPath( $this->importDir, $tablename );
		return $retval;
	}

	public function mapPrimaryKey( Model $model, $id, $newId )
	{
		isset($model) || die('mapPrimaryKey requires a model.');
		isset($id) || die('mapPrimaryKey requires a source pk.');
		if (isset($newId) == false) throw new \Exception('mapPrimaryKey requires a destination pk.');

		$oldKey = $model->tableName() . "-" . $id;
		$this->primaryKeyMap[ $oldKey ] = $newId;
	}

	public function mappedPrimaryKey( Model $model, $id )
	{
		isset($model) || die('mappedPrimaryKey requires a model.');
		isset($id) || die('mappedPrimaryKey requires a source pk.');

		// endpoint-endpoint-id_4
		$oldKey = $model->tableName() . "-" . $id;

		$newKey = (isset($this->primaryKeyMap[ $oldKey ]) ? $this->primaryKeyMap[ $oldKey ] : null);
		if ( is_null($newKey)) {
			file_put_contents( "/tmp/map.json", json_encode($this->primaryKeyMap, JSON_PRETTY_PRINT));

			throw new Exception( "Failed to find mapped $oldKey" );
		}
		return $newKey;
	}

	public function dataForRow( $pkDir )
	{
		isset($pkDir) || die('dataForRow requires a source pk.');

		$path = appendPath($pkDir, "data.json");
		is_file($path) || dir( "Row $pkDir does not have 'data.json' file." );

		$jsonData = json_decode(file_get_contents($path), true);
		if ( json_last_error() != 0 ) {
			throw new Exception( jsonErrorString(json_last_error()) );
		}
		return $jsonData;
	}

	public function importAll()
	{
		$modelNames = $this->importOrder();
		foreach( $modelNames as $modelName ) {
			$model = Model::Named( $modelName );
			$modelSubDir = $this->directoryFor( $model->tableName() );
			if ( is_dir($modelSubDir) ) {
// 				print PHP_EOL . str_pad($modelName, 15);
				$count = 0;

				$table_method = 'importRow_' . $modelName;
				if (method_exists($this, $table_method) == false) {
					$table_method = 'importRow';
				}

				foreach (scandir($modelSubDir) as $rowName)
				{
					if ($rowName == '.' || $rowName == '..' || $rowName == '.DS_Store') continue;
					$success_return = $this->$table_method($model, appendPath($modelSubDir, $rowName) );
					if ( is_null($success_return) || $success_return == false ) {
						throw new Exception("import error " . $table_method );
					}

					$count++;
// 					if ( ($count % 500) == 0 ) {
// 						print " " . $count;
// 					}
				}
// 				print " " . $count;
			}
		}
// 		print PHP_EOL;
	}

	public function importMediaPath( $rowDir, $mediaName, $searchExtensions = true)
	{
		$base = appendPath($rowDir, $mediaName);
		if ( $searchExtensions ) {
			foreach( imageExtensions() as $ext) {
				if ( file_exists( $base . '.' . $ext) ) {
					return $base . '.' . $ext;
				}
			}
			return null;
		}
		return ( file_exists($base) ? $base : null );
	}

	public function importRowIcons( Model $model, DataObject $dbo, $rowDir )
	{
		$icon = $this->importMediaPath( $rowDir, Model::IconName, true );
		if ( is_null($icon) == false ) {
			$dest = hashedPath($model->tableName(), $dbo->id, basename($icon));
			if ( file_exists($dest) ) {
				throw new Exception("Object $dbo already has " . $dest );
			}

			if (copy($icon, $dest ) == false) {
				throw new \Exception( "Failed to copy $icon to $dest" );
			}
		}

		$thumb = $this->importMediaPath( $rowDir, Model::ThumbnailName, true );
		if ( is_null($thumb) == false ) {
			$dest = hashedPath($model->tableName(), $dbo->id, basename($thumb));
			if ( file_exists($dest) ) {
				throw new Exception("Object $dbo already has " . $dest );
			}

			if (copy($thumb, $dest ) == false) {
				throw new \Exception( "Failed to copy $thumb to $dest" );
			}
		}

		if ( $model->tableName() == "media" ) {
			$filename = $dbo->filename;
			$media = $this->importMediaPath( $rowDir, $filename, false );
			if ( is_null($media) == false ) {
				$dest = hashedPath($model->tableName(), $dbo->id, $filename);
				if (copy($media, $dest ) == false) {
					throw new \Exception( "Failed to copy $media to $dest" );
				}
			}
			else {
				throw new Exception("Media $dbo does not have " . $filename );
			}
		}
	}

	public function importRowData( Model $model, $rowDir, array $data )
	{
		$pkColumn = $model->tablePK();
		$dbo = \SQL::InsertRecord( $model, array(), $data )->commitTransaction();
		if ( $dbo == false ) {
			throw new Exception("import error " . $rowDir );
		}
		$this->mapPrimaryKey( $model, basename($rowDir), $dbo->{$pkColumn} );
		return $dbo;
	}

	public function importRow( Model $model, $rowDir )
	{
		$data = $this->dataForRow($rowDir);
		$pkColumn = $model->tablePK();
		$oldPk = $data[$pkColumn];
		unset( $data[$pkColumn] );
		return $this->importRowData( $model, $rowDir, $data );
	}

	public function importRowForCodeType( Model $model, $rowDir )
	{
		$data = $this->dataForRow($rowDir);
		$existing = $model->singleObjectForKeyValue( "code", $data['code'] );
		if ( $existing == false ) {
			return $this->importRow( $model, $rowDir );
		}
		$pkColumn = $model->tablePK();
		$this->mapPrimaryKey( $model, basename($rowDir), $existing->{$pkColumn} );
		return $existing;
	}

	public function importRow_media_type( Model $model, $rowDir )	{ return $this->importRowForCodeType( $model, $rowDir ); }
	public function importRow_job_type( Model $model, $rowDir )		{ return $this->importRowForCodeType( $model, $rowDir ); }
	public function importRow_endpoint_type(Model $model, $rowDir ) { return $this->importRowForCodeType( $model, $rowDir ); }

	public function importRow_log_level( Model $model, $rowDir )
	{
		$data = $this->dataForRow($rowDir);
		unset( $data["id"] );
		$existing = $model->singleObjectForKeyValue( "code", $data['code'] );
		if ( $existing == false ) {
			$existing = $model->singleObjectForKeyValue( "name", $data['name'] );
			if ( $existing == false ) {
				$existing = \SQL::InsertRecord( $model, array(), $data )->commitTransaction();
				if ( $existing == false ) {
					throw new Exception("import error " . $rowDir );
				}
			}
			else {
				throw new Exception( "No LogLevel for " . $data['code'] . " but found name " . $data['name']);
			}
		}
		$this->mapPrimaryKey( $model, basename($rowDir), $existing->code );
		return $existing;
	}

	public function importRow_version( Model $model, $rowDir )
	{
		$data = $this->dataForRow($rowDir);
		unset( $data["id"] );
		$existing = $model->singleObjectForKeyValue( "code", $data['code'] );
		if ( $existing == false ) {
			$existing = \SQL::InsertRecord( $model, array(), $data )->commitTransaction();
			if ( $existing == false ) {
				throw new Exception("import error " . $rowDir );
			}
		}
		$this->mapPrimaryKey( $model, basename($rowDir), $existing->code );
		return $existing;
	}

	public function importRow_patch( Model $model, $rowDir )
	{
		$data = $this->dataForRow($rowDir);
		$existing = $model->singleObjectForKeyValue( "name", $data['name'] );
		if ( $existing == false ) {
			$existing = \SQL::InsertRecord( $model, array(), $data )->commitTransaction();
			if ( $existing == false ) {
				throw new Exception("import error " . $rowDir );
			}
		}
		$this->mapPrimaryKey( $model, basename($rowDir), $existing->id );
		return $existing;
	}

	public function importRow_rss( Model $model, $rowDir )
	{
		$data = $this->dataForRow($rowDir);
		$endpointModel = Model::Named( "Endpoint" );
		if ( isset($data['endpoint_id']) && is_null($data['endpoint_id']) == false) {
			$oldEndpoint = 'id_' . $data['endpoint_id'];
			$data['endpoint_id'] = $this->mappedPrimaryKey( $endpointModel, $oldEndpoint );
		}

		return $this->importRowData( $model, $rowDir, $data );
	}

	public function importRow_users( Model $model, $rowDir )
	{
		$data = $this->dataForRow($rowDir);
// 		if ( $data['name'] == 'vito' ) {
// 			$success = \SQL::Update( $model, Qualifier::Equals( "name", $data['name']), $data)->commitTransaction();
// 			if ( $success == false ) {
// 				throw new Exception("import error updating " . $rowDir );
// 			}
//
// 			$existing = $model->singleObjectForKeyValue( "name", $data['name'] );
// 			if ( $existing == false ) {
// 				throw new Exception("failed to find row for 'name' =" . $data['name'] );
// 			}
// 			$pkColumn = $model->tablePK();
// 			$this->mapPrimaryKey( $model, basename($rowDir), $existing->{$pkColumn} );
// 			return $existing;
// 		}

		return $this->importRow( $model, $rowDir );
	}

	public function importRow_series( Model $model, $rowDir )
	{
		$data = $this->dataForRow($rowDir);
		$publisherModel = Model::Named( "Publisher" );
		if ( isset($data['publisher_id']) && is_null($data['publisher_id']) == false) {
			$oldpublisher = 'id_' . $data['publisher_id'];
			$data['publisher_id'] = $this->mappedPrimaryKey( $publisherModel, $oldpublisher );
		}

		$dbo = $this->importRowData( $model, $rowDir, $data );
		if ( $dbo != false ) {
			$this->importRowIcons( $model, $dbo, $rowDir );
		}
		return $dbo;
	}

	public function importRow_series_alias( Model $model, $rowDir )
	{
		$data = $this->dataForRow($rowDir);
		$seriesModel = Model::Named( "Series" );
		if ( isset($data['series_id']) && is_null($data['series_id']) == false) {
			$oldseries = 'id_' . $data['series_id'];
			$data['series_id'] = $this->mappedPrimaryKey( $seriesModel, $oldseries );
		}

		return $this->importRowData( $model, $rowDir, $data );
	}

	public function importRow_publication( Model $model, $rowDir )
	{
		$data = $this->dataForRow($rowDir);
		$seriesModel = Model::Named( "Series" );
		if ( isset($data['series_id']) && is_null($data['series_id']) == false) {
			$oldseries = 'id_' . $data['series_id'];
			$data['series_id'] = $this->mappedPrimaryKey( $seriesModel, $oldseries );
		}

		$dbo = $this->importRowData( $model, $rowDir, $data );
		if ( $dbo != false ) {
			$this->importRowIcons( $model, $dbo, $rowDir );
		}
		return $dbo;
	}

	public function importRow_character( Model $model, $rowDir )
	{
		$data = $this->dataForRow($rowDir);
		$publisherModel = Model::Named( "Publisher" );
		if ( isset($data['publisher_id']) && is_null($data['publisher_id']) == false) {
			$oldpublisher = 'id_' . $data['publisher_id'];
			$data['publisher_id'] = $this->mappedPrimaryKey( $publisherModel, $oldpublisher );
		}

		$dbo = $this->importRowData( $model, $rowDir, $data );
		if ( $dbo != false ) {
			$this->importRowIcons( $model, $dbo, $rowDir );
		}
		return $dbo;
	}

	public function importRow_character_alias( Model $model, $rowDir )
	{
		$data = $this->dataForRow($rowDir);
		$characterModel = Model::Named( "Character" );
		if ( isset($data['character_id']) && is_null($data['character_id']) == false) {
			$oldcharacter = 'id_' . $data['character_id'];
			$data['character_id'] = $this->mappedPrimaryKey( $characterModel, $oldcharacter );
		}

		return $this->importRowData( $model, $rowDir, $data );
	}

	public function importRow_publication_character( Model $model, $rowDir )
	{
		$data = $this->dataForRow($rowDir);
		$characterModel = Model::Named( "Character" );
		if ( isset($data['character_id']) && is_null($data['character_id']) == false) {
			$oldcharacter = 'id_' . $data['character_id'];
			$data['character_id'] = $this->mappedPrimaryKey( $characterModel, $oldcharacter );
		}

		$publicationModel = Model::Named( "Publication" );
		if ( isset($data['publication_id']) && is_null($data['publication_id']) == false) {
			$oldpublication = 'id_' . $data['publication_id'];
			$data['publication_id'] = $this->mappedPrimaryKey( $publicationModel, $oldpublication );
		}

		return $this->importRowData( $model, $rowDir, $data );
	}

	public function importRow_series_character( Model $model, $rowDir )
	{
		$data = $this->dataForRow($rowDir);
		$characterModel = Model::Named( "Character" );
		if ( isset($data['character_id']) && is_null($data['character_id']) == false) {
			$oldcharacter = 'id_' . $data['character_id'];
			$data['character_id'] = $this->mappedPrimaryKey( $characterModel, $oldcharacter );
		}

		$seriesModel = Model::Named( "Series" );
		if ( isset($data['series_id']) && is_null($data['series_id']) == false) {
			$oldseries = 'id_' . $data['series_id'];
			$data['series_id'] = $this->mappedPrimaryKey( $seriesModel, $oldseries );
		}

		return $this->importRowData( $model, $rowDir, $data );
	}

	public function importRow_story_arc( Model $model, $rowDir )
	{
		$data = $this->dataForRow($rowDir);
		$publisherModel = Model::Named( "Publisher" );
		if ( isset($data['publisher_id']) && is_null($data['publisher_id']) == false) {
			$oldpublisher = 'id_' . $data['publisher_id'];
			$data['publisher_id'] = $this->mappedPrimaryKey( $publisherModel, $oldpublisher );
		}

		$dbo = $this->importRowData( $model, $rowDir, $data );
		if ( $dbo != false ) {
			$this->importRowIcons( $model, $dbo, $rowDir );
		}
		return $dbo;
	}

	public function importRow_story_arc_character( Model $model, $rowDir )
	{
		$data = $this->dataForRow($rowDir);
		$characterModel = Model::Named( "Character" );
		if ( isset($data['character_id']) && is_null($data['character_id']) == false) {
			$oldcharacter = 'id_' . $data['character_id'];
			$data['character_id'] = $this->mappedPrimaryKey( $characterModel, $oldcharacter );
		}

		$story_arcModel = Model::Named( "Story_Arc" );
		if ( isset($data['story_arc_id']) && is_null($data['story_arc_id']) == false) {
			$oldstory_arc = 'id_' . $data['story_arc_id'];
			$data['story_arc_id'] = $this->mappedPrimaryKey( $story_arcModel, $oldstory_arc );
		}

		return $this->importRowData( $model, $rowDir, $data );
	}

	public function importRow_story_arc_publication( Model $model, $rowDir )
	{
		$data = $this->dataForRow($rowDir);
		$publicationModel = Model::Named( "Publication" );
		if ( isset($data['publication_id']) && is_null($data['publication_id']) == false) {
			$oldpublication = 'id_' . $data['publication_id'];
			$data['publication_id'] = $this->mappedPrimaryKey( $publicationModel, $oldpublication );
		}

		$story_arcModel = Model::Named( "Story_Arc" );
		if ( isset($data['story_arc_id']) && is_null($data['story_arc_id']) == false) {
			$oldstory_arc = 'id_' . $data['story_arc_id'];
			$data['story_arc_id'] = $this->mappedPrimaryKey( $story_arcModel, $oldstory_arc );
		}

		return $this->importRowData( $model, $rowDir, $data );
	}

	public function importRow_story_arc_series( Model $model, $rowDir )
	{
		$data = $this->dataForRow($rowDir);
		$seriesModel = Model::Named( "Series" );
		if ( isset($data['series_id']) && is_null($data['series_id']) == false) {
			$oldseries = 'id_' . $data['series_id'];
			$data['series_id'] = $this->mappedPrimaryKey( $seriesModel, $oldseries );
		}

		$story_arcModel = Model::Named( "Story_Arc" );
		if ( isset($data['story_arc_id']) && is_null($data['story_arc_id']) == false) {
			$oldstory_arc = 'id_' . $data['story_arc_id'];
			$data['story_arc_id'] = $this->mappedPrimaryKey( $story_arcModel, $oldstory_arc );
		}

		return $this->importRowData( $model, $rowDir, $data );
	}

	public function importRow_media( Model $model, $rowDir )
	{
		$data = $this->dataForRow($rowDir);
		$publicationModel = Model::Named( "Publication" );
		if ( isset($data['publication_id']) && is_null($data['publication_id']) == false) {
			$oldpublication = 'id_' . $data['publication_id'];
			$data['publication_id'] = $this->mappedPrimaryKey( $publicationModel, $oldpublication );
		}

		$dbo = $this->importRowData( $model, $rowDir, $data );
		if ( $dbo != false ) {
			$this->importRowIcons( $model, $dbo, $rowDir );
		}
		return $dbo;
	}
}
