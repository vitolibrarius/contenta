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

use \SimpleXMLElement as SimpleXMLElement;

use connectors\NewznabConnector as NewznabConnector;

use model\Character as Character;
use model\Character_Alias as Character_Alias;
use model\Endpoint as Endpoint;
use model\Endpoint_Type as Endpoint_Type;
use model\logs\Log as Log;
use model\logs\Log_Level as Log_Level;
use model\Network as Network;
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
use \model\user\Users as Users;
use \model\jobs\Job_Type as Job_Type;
use \model\jobs\Job_Running as Job_Running;
use \model\jobs\Job as Job;

$root = TEST_ROOT_PATH . "/" . basename(__FILE__, ".php");
SetConfigRoot( $root, false );

my_echo( );
my_echo( "Creating Database" );
Migrator::Upgrade( Config::GetLog() );

my_echo( "---------- Endpoint ");
$Newznab_endpoint_type = Model::Named('Endpoint_Type')->endpointTypeForCode(model\Endpoint_Type::Newznab);
($Newznab_endpoint_type != false && $Newznab_endpoint_type->code == Endpoint_Type::Newznab) || die("Could not find Endpoint_Type::Newznab");

$ep_model = Model::Named('Endpoint');
$points = $ep_model->allForTypeCode(Endpoint_Type::Newznab);
if ( is_array($points) == false || count($points) == 0) {
	$metadata = metadataFor(Endpoint_Type::Newznab . ".json");
	if ( $metadata->isMeta( model\Endpoint::api_key ) == false )
	{
		$metadata->setMeta( model\Endpoint::name, "Newznab Source" );
		$metadata->setMeta( model\Endpoint::type_id, $Newznab_endpoint_type->id );
		$metadata->setMeta( model\Endpoint::base_url, "YOUR Newznab site base url here" );
		$metadata->setMeta( model\Endpoint::api_key, "YOUR API KEY HERE" );
		$metadata->setMeta( model\Endpoint::username, 'vito' );
		$metadata->setMeta( model\Endpoint::enabled, Model::TERTIARY_TRUE );
		$metadata->setMeta( model\Endpoint::compressed, Model::TERTIARY_FALSE );

		die( "Please configure the Newznab.json config file with correct test data" . PHP_EOL );
	}

	loadData( $ep_model, array($metadata->readMetadata()), array( "name", "type", "base_url", "api_key") );
}

function reportXML( $xml, $root, $name )
{
	if ( $xml instanceof SimpleXMLElement ) {
		$xml->asXML( appendPath($root, $name) );
		foreach ($xml->channel->item as $key => $item) {
			$name = $item->title . ".nzb";
			$url = $item->link;
			if (isset($item->enclosure, $item->enclosure['url'])) {
				$url = $item->enclosure['url'];
			}
			$nzb = file_get_contents($url);
			if ( $nzb != null ) {
				my_echo( "	" . $name );
				file_put_contents(appendPath($root, $name), $nzb);
			}
		}
		my_echo( "---------------------------------------" );
	}
	else if ( is_array( $xml ) ) {
		foreach ($xml as $key => $item) {
			my_echo( var_export($item, true));
		}
		my_echo( "---------------------------------------" );
	}
	else {
		my_echo( $name . " = " . var_export($xml, true));
		my_echo( "---------------------------------------" );
	}
}

$points = $ep_model->allForTypeCode(Endpoint_Type::Newznab);
($points != false && count($points) > 0) || die('No endpoint defined');

$epoint = $points[0];

my_echo( "Search capabilities" );
$connection = new NewznabConnector( $epoint );
$xml = $connection->capabilities();
reportXML($xml, $root, 'capabilities.xml');

my_echo( "Search superman" );
$xml = $connection->search("superman", null, null);
reportXML($xml, $root, 'search_1.xml');

my_echo( "Search superman (comics)" );
$xml = $connection->searchComics("superman");
reportXML($xml, $root, 'search_2.xml');

my_echo( "Search Stephen King (books)" );
$xml = $connection->searchBooks("*", "Stephen King");
reportXML($xml, $root, 'search_3.xml');

