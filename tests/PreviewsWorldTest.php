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

use processor\PreviewsWorldImporter as PreviewsWorldImporter;

use model\Character as Character;
use model\Character_Alias as Character_Alias;
use model\Endpoint as Endpoint;
use model\Endpoint_Type as Endpoint_Type;
use model\logs\Log as Log;
use model\logs\Log_Level as Log_Level;
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
SetConfigRoot( $root );

my_echo( );
my_echo( "Creating Database" );
Migrator::Upgrade( Config::GetLog() );

my_echo( "---------- Endpoint ");
$pw_endpoint_type = Model::Named('Endpoint_Type')->endpointTypeForCode(model\Endpoint_Type::PreviewsWorld);
($pw_endpoint_type != false && $pw_endpoint_type->code == Endpoint_Type::PreviewsWorld) || die("Could not find Endpoint_Type::PreviewsWorld");

$ep_model = Model::Named('Endpoint');
$points = $ep_model->allForTypeCode(Endpoint_Type::PreviewsWorld);
if ( is_array($points) == false || count($points) == 0) {
	$ep_model->create($pw_endpoint_type, "PreviewsWorld", $pw_endpoint_type->api_url);
}

$points = $ep_model->allForTypeCode(Endpoint_Type::PreviewsWorld);
($points != false && count($points) > 0) || die('No endpoint defined');

$epoint = $points[0];

$importer = new PreviewsWorldImporter( basename(__file__) );
$importer->setEndpoint($epoint);
$importer->processData();
