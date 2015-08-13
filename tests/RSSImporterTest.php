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

use processor\RSSImporter as RSSImporter;

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

$root = TEST_ROOT_PATH . "/" . basename(__FILE__, ".php");
SetConfigRoot( $root );

my_echo( );
my_echo( "Creating Database" );
Migrator::Upgrade( Config::GetLog() );

my_echo( "---------- Endpoint ");
$rss_endpoint_type = Model::Named('Endpoint_Type')->endpointTypeForCode(model\Endpoint_Type::RSS);
($rss_endpoint_type != false && $rss_endpoint_type->code == Endpoint_Type::RSS) || die("Could not find Endpoint_Type::RSS");

$ep_model = Model::Named('Endpoint');
$points = $ep_model->allForTypeCode(Endpoint_Type::RSS);
if ( is_array($points) == false || count($points) == 0) {
	$metadata = metadataFor(Endpoint_Type::RSS . ".json");
	if ( $metadata->isMeta( model\Endpoint::api_key ) == false )
	{
		$metadata->setMeta( model\Endpoint::name, "RSS Source" );
		$metadata->setMeta( model\Endpoint::type_id, $rss_endpoint_type->id );
		$metadata->setMeta( model\Endpoint::base_url, $rss_endpoint_type->api_url );
		$metadata->setMeta( model\Endpoint::api_key, "YOUR API KEY HERE" );
		$metadata->setMeta( model\Endpoint::username, 'vito' );
		$metadata->setMeta( model\Endpoint::enabled, Model::TERTIARY_TRUE );
		$metadata->setMeta( model\Endpoint::compressed, Model::TERTIARY_FALSE );

		die( "Please configure the RSS.json config file with correct test data" . PHP_EOL);
	}

	loadData( $ep_model, array($metadata->readMetadata()), array( "name", "type", "base_url", "api_key") );
}

$points = $ep_model->allForTypeCode(Endpoint_Type::RSS);
($points != false && count($points) > 0) || die('No endpoint defined');

$epoint = $points[0];

$importer = new RSSImporter( basename(__file__) );
$importer->setEndpoint($epoint);
$importer->processData();

my_echo();

$rss = Model::Named("Rss")->allObjects();
reportData($rss,  array(
	"clean_year",
	"clean_issue",
	"clean_name",
	"title",
	"endpoint/name"
	)
);

