<?php

	$system_path = dirname(dirname(__FILE__));
	if (realpath($system_path) !== FALSE)
	{
		$system_path = realpath($system_path).'/';
	}

	define('SYSTEM_PATH', str_replace("\\", "/", $system_path));
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

		die( "Please configure the comicvine.json config file with your API key" );
	}

	loadData( $ep_model, array($metadata->readMetadata()), array( "name", "type", "base_url", "api_key") );
}

$points = $ep_model->allForTypeCode(Endpoint_Type::ComicVine);
($points != false && count($points) > 0) || die('No endpoint defined');

$epoint = $points[0];

$metadata = metadataFor( "ComicVineImporter.json");

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
	$importer->importPublisherValues( null, $pub["xid"], null);
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

foreach( $series as $sample ) {
	$importer->importSeriesValues( null, $sample["xid"], null);
}
$importer->processData();

$series_model = Model::Named("Series");
foreach( $series as $sample ) {
	$object = $series_model->objectForExternal( $sample["xid"], Endpoint_Type::ComicVine);
	if ( isset($object) == false || is_a($object, '\model\SeriesDBO') == false || $object->name != $sample["name"]) {
		my_echo( "Error with " . $sample["xid"] . " - " . $sample["name"] . " found " . var_export( $object, true ));
	}
}
