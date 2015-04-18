<?php

	$system_path = dirname(dirname(__FILE__));
	if (realpath($system_path) !== FALSE)
	{
		$system_path = realpath($system_path). DIRECTORY_SEPARATOR;
	}

	define('SYSTEM_PATH', str_replace("\\", DIRECTORY_SEPARATOR, $system_path));
	define('APPLICATION_PATH', SYSTEM_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR);

	require SYSTEM_PATH .'application/config/bootstrap.php';
	require SYSTEM_PATH .'application/config/autoload.php';
	require SYSTEM_PATH .'application/config/common.php';
	require SYSTEM_PATH .'application/config/errors.php';
	require SYSTEM_PATH .'application/libs/Config.php';
	require SYSTEM_PATH .'application/libs/Cache.php';

	require SYSTEM_PATH .'tests/_ResetConfig.php';
	require SYSTEM_PATH .'tests/_Data.php';

use processor\ComicVineImporter as ComicVineImporter;

use model\Character as Character;
use model\Character_Alias as Character_Alias;
use model\Endpoint as Endpoint;
use model\Endpoint_Type as Endpoint_Type;
use model\Log as Log;
use model\Log_Level as Log_Level;
use model\Network as Network;
use model\Patch as Patch;
use model\Publication as Publication;
use model\Publication_Character as Publication_Character;
use model\Publisher as Publisher;
use model\Series as Series;
use model\Series_Alias as Series_Alias;
use model\Series_Character as Series_Character;
use model\Story_Arc as Story_Arc;
use model\Story_Arc_Character as Story_Arc_Character;
use model\Story_Arc_Series as Story_Arc_Series;
use model\User_Network as User_Network;
use model\User_Series as User_Series;
use model\Users as Users;
use model\Version as Version;
use model\Job_Type as Job_Type;
use model\Job_Running as Job_Running;
use model\Job as Job;

$root = "/tmp/test/" . basename(__FILE__, ".php");
SetConfigRoot( $root );

my_echo( );
my_echo( "Creating Database" );
Migrator::Upgrade( Config::GetLog() );

my_echo( "---------- Endpoint ");
$cv_endpoint_type = Model::Named('Endpoint_Type')->endpointTypeForCode(model\Endpoint_Type::ComicVine);
($cv_endpoint_type != false && $cv_endpoint_type->code == 'ComicVine') || die("Could not find Endpoint_Type::ComicVine");

$ep_model = Model::Named('Endpoint');
$points = $ep_model->allForTypeCode(Endpoint_Type::ComicVine);
if ( is_array($points) == false || count($points) == 0) {
	$metadata = metadataFor(Endpoint_Type::ComicVine . ".json");
	if ( $metadata->isMeta( model\Endpoint::api_key ) == false )
	{
		$metadata->setMeta( model\Endpoint::name, "My ComicVine" );
		$metadata->setMeta( model\Endpoint::type_id, $cv_endpoint_type->id );
		$metadata->setMeta( model\Endpoint::base_url, $cv_endpoint_type->api_url );
		$metadata->setMeta( model\Endpoint::api_key, "YOUR API KEY HERE" );
		$metadata->setMeta( model\Endpoint::username, 'vito' );
		$metadata->setMeta( model\Endpoint::enabled, Model::TERTIARY_TRUE );
		$metadata->setMeta( model\Endpoint::compressed, Model::TERTIARY_FALSE );

		die( "Please configure the ComicVine.json config file with your API key" );
	}

	loadData( $ep_model, array($metadata->readMetadata()), array( "name", "type", "base_url", "api_key") );
}

$points = $ep_model->allForTypeCode(Endpoint_Type::ComicVine);
($points != false && count($points) > 0) || die('No endpoint defined');

$epoint = $points[0];

$metadata = metadataFor( "ComicVineImporter.json", true);

/***********************************************************************************************/
my_echo( );
my_echo( "---------- Publisher ");
$importer = new ComicVineImporter( Publisher::TABLE );
$importer->setEndpoint($points[0]);

$publishers = $metadata->getMeta( Publisher::TABLE );
if ( is_array($publishers) == false || count($publishers) == 0 ) {
	$sample = array(
		array( "xid" => 101, "name" => "Archie Comics" ),
		array( "xid" => 464, "name" => "Disney" ),
		array( "xid" => 513, "name" => "Image" )
	);
	$metadata->setMeta( Publisher::TABLE, $sample );
	$publishers = $metadata->getMeta( Publisher::TABLE );
}

foreach( $publishers as $pub ) {
	$importer->enqueue_publisher( $pub, true, true);
}
$importer->processData();

$publisher_model = Model::Named("Publisher");
foreach( $publishers as $pub ) {
	$object = $publisher_model->objectForExternal( $pub["xid"], Endpoint_Type::ComicVine);
	if ( isset($object) == false || is_a($object, '\model\PublisherDBO') == false || $object->name != $pub["name"]) {
		my_echo( "Error with " . $pub["xid"] . " - " . $pub["name"] . " found " . var_export( $object, true ));
	}
}
my_echo( );

/***********************************************************************************************/
my_echo( );
my_echo( "---------- Series ");
$importer = new ComicVineImporter( Series::TABLE );
$importer->setEndpoint($points[0]);

$series = $metadata->getMeta( Series::TABLE );
if ( is_array($series) == false || count($series) == 0 ) {
	$sample = array(
		array( "xid" => 42599, "name" => "Swamp Thing" ),
		array( "xid" => 68136, "name" => "Afterlife With Archie" ),
		array( "xid" => 26151, "name" => "All-New Savage She-Hulk" )
	);
	$metadata->setMeta( Series::TABLE, $sample );
	$series = $metadata->getMeta( Series::TABLE );
}

$importer->enqueue_publisher( array( "xid" => 10, "name" => "DC Comics" ), true, true);
$importer->enqueue_character( array( "xid" => 1686, "name" => "Superboy" ), true, true);
foreach( $series as $sample ) {
	$importer->enqueue_series( $sample, true, true );
}
$importer->processData();

$series_model = Model::Named("Series");
foreach( $series as $sample ) {
	$object = $series_model->objectForExternal( $sample["xid"], Endpoint_Type::ComicVine);
	if ( isset($object) == false || is_a($object, '\model\SeriesDBO') == false || $object->name != $sample["name"]) {
		my_echo( "Error with " . $sample["xid"] . " - " . $sample["name"] . " found " . var_export( $object, true ));
	}
}

my_echo( );

/***********************************************************************************************/
my_echo( );
my_echo( "---------- Publication ");
$importer = new ComicVineImporter( Publication::TABLE );
$importer->setEndpoint($points[0]);

$pubs = $metadata->getMeta( Publication::TABLE );
if ( is_array($pubs) == false || count($pubs) == 0 ) {
	$sample = array(
		array(   "xid" => 319038, "name" => "Swamp Thing", "issue" => 7,
			"series_xid" => 42599, "series_name" => "Swamp Thing" ),
		array(   "xid" => 447038, "name" => "Escape From Riverdale Chapter Four: Archibald Rex", "issue" => 4,
			"series_xid" => 68136, "series_name" => "Afterlife With Archie" ),
		array(   "xid" => 293263, "name" => "...And Most Of The Costumes Stay On...", "issue" => 1,
			"series_xid" => 42722, "series_name" => "Catwoman" ),
		array(   "xid" => 155152, "name" => "W.M.D. Woman of Mass Destruction", "issue" => 1,
			"series_xid" => 26151, "series_name" => "All-New Savage She-Hulk" )
	);
	$metadata->setMeta( Publication::TABLE, $sample );
	$pubs = $metadata->getMeta( Publication::TABLE );
}

foreach( $pubs as $sample ) {
	$importer->enqueue_publication( $sample, true, true );
}
$importer->processData();

$pubs_model = Model::Named("Publication");
foreach( $pubs as $sample ) {
	$object = $pubs_model->objectForExternal( $sample["xid"], Endpoint_Type::ComicVine);
	if ( isset($object) == false || is_a($object, '\model\PublicationDBO') == false) {
		my_echo( "Error with " . $sample["xid"] . " - record not found .. not imported");
	}
	else {
		if ( $object->name != $sample["name"] ) {
			my_echo( "Error with " . $sample["xid"] . " - " . $object->name . " is not " . $sample["name"]);
		}

		if ( $object->issue_num != $sample["issue"] ) {
			my_echo( "Error with " . $sample["xid"] . " - " . $object->issue_num . " is not " . $sample["issue"]);
		}

		if ( $object->series() == false ) {
			my_echo( "Error with " . $sample["xid"] . " - no series");
		}
		else {
			if ( $object->series()->name != $sample["series_name"] ) {
				my_echo( "Error with " . $sample["xid"] . " - series name " . $object->series()->name
					. " is not " . $sample["series_name"]);
			}

			$seriesObj = $series_model->objectForExternal( $sample["series_xid"], Endpoint_Type::ComicVine);
			if ( $seriesObj == false || ($seriesObj instanceof model\SeriesDBO) == false ) {
				my_echo( "Error with " . $sample["xid"] . " - expected series not found " .  $sample["series_xid"]
					. " got " . var_export($seriesObj, true));
			}
			else {
				if ( $seriesObj->id != $object->series()->id) {
					my_echo( "Error with " . $sample["xid"] . " - wrong series id " . $seriesObj->id . " found " . var_export( $object, true ));
				}

				if ( $seriesObj->xid != $object->series()->xid) {
					my_echo( "Error with " . $sample["xid"] . " - wrong series xid " . $seriesObj->xid . " found " . var_export( $object, true ));
				}
			}
		}
	}
}

/***********************************************************************************************/
my_echo( );
my_echo( "---------- Media ");

$cbzType = Model::Named( "Media_Type" )->cbz();
$media_model = Model::Named("Media");

$samples = array();
foreach (glob(SYSTEM_PATH . "/tests/samples/*.cbz") as $file) {
	$samples[] = $file;
}

foreach( $pubs as $idx => $sample ) {
	$publication = $pubs_model->objectForExternal( $sample["xid"], Endpoint_Type::ComicVine);
	if ( $publication instanceof model\PublicationDBO ) {
		$filename = "NoSampleFound";
		$hash = uuid();
		$size = rand();
		$fullpath = null;
		if ( count( $samples ) >= $idx ) {
			$fullpath = $samples[$idx];
			$filename = basename($fullpath);
			$hash = hash_file(HASH_DEFAULT_ALGO, $fullpath);
			$size = filesize($fullpath);
		}

		my_echo( "$idx => $filename for $publication" );
		$media = $media_model->create( $publication, $cbzType, $filename, $hash, $size );
		if ( $media instanceof model\MediaDBO ) {
			$newfile = $media->contentaPath();
			if ( is_null($fullpath) ) {
				touch($newfile);
			}
			else {
				copy($fullpath, $newfile);
			}
		}
	}
}

$allMedia = $media_model->allObjects();
reportData($allMedia,  array("filename", "original_filename", "publication", "checksum") );