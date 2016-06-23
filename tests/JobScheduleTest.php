<?php

	$system_path = dirname(dirname(__FILE__));
	if (realpath($system_path) !== FALSE)
	{
		$system_path = realpath($system_path).DIRECTORY_SEPARATOR;
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
	use processor\JobEvaluator as JobEvaluator;

use \model\media\Character as Character;
use \model\media\Character_Alias as Character_Alias;
use \model\network\Endpoint as Endpoint;
use \model\network\Endpoint_Type as Endpoint_Type;
use \model\media\logs\Log as Log;
use \model\media\logs\Log_Level as Log_Level;
use \model\media\Network as Network;
use \model\media\Publication as Publication;
use \model\media\Publication_Character as Publication_Character;
use \model\media\Publisher as Publisher;
use \model\media\Series as Series;
use \model\media\Series_Alias as Series_Alias;
use \model\media\Series_Character as Series_Character;
use \model\media\Story_Arc as Story_Arc;
use \model\media\Story_Arc_Character as Story_Arc_Character;
use \model\media\Story_Arc_Series as Story_Arc_Series;
use \model\media\User_Network as User_Network;
use \model\media\User_Series as User_Series;
use \model\user\Users as Users;
use \model\jobs\Job_Type as Job_Type;
use \model\jobs\Job_Running as Job_Running;
use \model\jobs\Job as Job;

$root = TEST_ROOT_PATH . "/" . basename(__FILE__, ".php");
SetConfigRoot( $root, false );

my_echo( );
my_echo( "Creating Database" );
Migrator::Upgrade( Config::GetLog() );

$configPaths = array(
	"Repository/path",
	"Database/type",
	"Database/path",
	"Logging/type",
	"Logging/path",
	"Repository/cache",
	"Repository/processing"
);

$configOverride = array();
$config = Config::instance();
foreach( $configPaths as $keypath ) {
	$configValue = $config->getValue( $keypath );
	array_setValueForKeypath( $keypath, $configValue, $configOverride );
}

// Models
$ep_model = Model::Named('Endpoint');
$character_model = Model::Named('Character');

my_echo( "---------- Endpoint ");
$cv_endpoint_type = Model::Named('Endpoint_Type')->objectForCode(\model\network\Endpoint_Type::ComicVine);
($cv_endpoint_type != false && $cv_endpoint_type->code == 'ComicVine') || die("Could not find Endpoint_Type::ComicVine");

$points = $ep_model->allForTypeCode(Endpoint_Type::ComicVine);
if ( is_array($points) == false || count($points) == 0) {
	$metadata = metadataFor(Endpoint_Type::ComicVine . ".json");
	if ( $metadata->isMeta( \model\network\Endpoint::api_key ) == false )
	{
		$metadata->setMeta( \model\network\Endpoint::name, "My ComicVine" );
		$metadata->setMeta( \model\network\Endpoint::type_id, $cv_endpoint_type->id );
		$metadata->setMeta( \model\network\Endpoint::base_url, $cv_endpoint_type->api_url );
		$metadata->setMeta( \model\network\Endpoint::api_key, "YOUR API KEY HERE" );
		$metadata->setMeta( \model\network\Endpoint::username, 'vito' );
		$metadata->setMeta( \model\network\Endpoint::enabled, Model::TERTIARY_TRUE );
		$metadata->setMeta( \model\network\Endpoint::compressed, Model::TERTIARY_FALSE );

		die( "Please configure the ComicVine.json config file with your API key" );
	}

	loadData( $ep_model, array($metadata->readMetadata()), array( "name", "type", "base_url", "api_key") );
}

$points = $ep_model->allForTypeCode(Endpoint_Type::ComicVine);
($points != false && count($points) > 0) || die('No endpoint defined');

$comicvine_epoint = $points[0];
$importer = new ComicVineImporter( Publisher::TABLE );
$importer->setEndpoint($comicvine_epoint);

my_echo( "---------- Preloading characters ");
$importer->enqueue_character( array( "xid" => 1686, "name" => "Superboy" ) );
$importer->enqueue_character( array( "xid" => 1699, "name" => "Batman" ) );
$importer->enqueue_character( array( "xid" => 1698, "name" => "Catwoman" ) );

$importer->processData();

reportData($character_model->allObjects(),  array("name", "realname", "desc", "gender", "publisher", "series") );


my_echo( "---------- Jobs ");
$job_types = Model::Named('Job_Type')->allObjects();
reportData($job_types,  Model::Named('Job_Type')->allColumnNames());

$character_job_type = Model::Named('Job_Type')->jobTypeForCode('character');

$test_job_type = Model::Named('Job_Type')->jobTypeForCode('test');
if ( ($test_job_type instanceof \model\media\Job_TypeDBO) == false ) {
	$params = array(
		Job_Type::name => "Test",
		Job_Type::code => "test",
		Job_Type::desc => "Test use only",
		Job_Type::processor => "TestProcessor",
		Job_Type::scheduled => 1
	);
	list( $obj, $errorList ) = Model::Named('Job_Type')->createObject($params);
	if ( is_array($errorList) ) {
		die( "failed to create test job_type" );
	}

	$test_job_type = Model::Named('Job_Type')->jobTypeForCode('test');
	($test_job_type instanceof \model\media\Job_TypeDBO) || die( "failed to fetch new test job_type $newObjId" );
}

$job_model = Model::Named("Job");
clearAllData( Model::Named("Job_Running") );
clearAllData( $job_model );

$job_data = array(
	array(
		model\Job::type_id => $test_job_type->id,
		model\Job::minute => "*/1",
		model\Job::hour => "*",
		model\Job::dayOfWeek => "*",
		model\Job::parameter => '{"type": "random", "batch": "50"}',
		model\Job::next => null,
		model\Job::one_shot => Model::TERTIARY_FALSE,
		model\Job::enabled => Model::TERTIARY_TRUE
	),
	array(
		model\Job::type_id => $test_job_type->id,
		model\Job::minute => "*/2",
		model\Job::hour => "*",
		model\Job::dayOfWeek => "*",
		model\Job::parameter => '{"items": "random", "batch": "50"}',
		model\Job::next => null,
		model\Job::one_shot => Model::TERTIARY_FALSE,
		model\Job::enabled => Model::TERTIARY_TRUE
	),
	array(
		model\Job::type_id => $test_job_type->id,
		model\Job::minute => "*/3",
		model\Job::hour => "*",
		model\Job::dayOfWeek => "*",
		model\Job::parameter => '{"items": "random", "batch": "50"}',
		model\Job::next => null,
		model\Job::one_shot => Model::TERTIARY_FALSE,
		model\Job::enabled => Model::TERTIARY_TRUE
	),
	array(
		model\Job::type_id => $character_job_type->id,
		model\Job::minute => "*",
		model\Job::hour => "*",
		model\Job::dayOfWeek => "*",
		model\Job::parameter => '{ "setEndpointId": ' . $comicvine_epoint->id . ', "enqueueBatch": ["character", 50 ] }',
		model\Job::next => null,
		model\Job::one_shot => Model::TERTIARY_FALSE,
		model\Job::enabled => Model::TERTIARY_TRUE
	)

);
$jobs = loadData( $job_model, $job_data, array("jobType/code", "endpoint", "minute", "hour", "dayOfWeek", "parameter", "nextDate", "one_shot", "enabled") );
$character_job = $jobs[0];

$jobs_to_run = $job_model->jobsToRun();
reportData($jobs_to_run,  array("jobType/code", "endpoint", "minute", "hour", "dayOfWeek", "parameter", "nextDate", "one_shot", "enabled") );

my_echo( "-------------------------------------------------");
my_echo( "");

foreach( $jobs_to_run as $aJob ) {
	echo DaemonizeJob( $aJob, null, array( "debug" => true, "ConfigOverride" => $configOverride )  );
}

sleep(5);

$running = Model::Named("Job_Running")->allObjects();
while ( is_array($running) && count( $running ) > 0) {
	echo "waiting .. " . PHP_EOL;
	sleep(10);
	$running = Model::Named("Job_Running")->allObjects();
}

$export = new db\ExportData( appendPath( Config::GetRepository(), "export.json" ));
$export->exportAll();

