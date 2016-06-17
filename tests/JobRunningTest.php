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

	use connectors\ComicVineConnector as ComicVineConnector;

	use \model\network\Endpoint as Endpoint;
	use \model\network\Endpoint_Type as Endpoint_Type;

$root = TEST_ROOT_PATH . "/" . basename(__FILE__, ".php");
SetConfigRoot( $root, false );

my_echo( );
my_echo( "Creating Database" );
Migrator::Upgrade( Config::GetLog() );

$job_run_model = Model::Named('Job_Running');
$job_run_data = array(
	array(
		"job_type_code" => "initial upload",
		"processor" => 'UploadImport',
		"guid" => rand(),
		"pid" => 3456
	),
	array(
		"job_type_code" => "character",
		"processor" => 'ComicVineConnector',
		"guid" => "character-1234",
		"pid" => 3456
	)
);
// $jobs_running = loadData( $job_run_model, $job_run_data, array("job", "jobType", "trace", "trace_id", "context", "context_id", "pid") );
foreach ( $job_run_data as $sample ) {
	$jobRunning = $job_run_model->createForCode(
			$sample['job_type_code'],
			$sample['processor'],
			$sample['guid'],
			$sample['pid']
		);
}
$allJobs = $job_run_model->allObjects();
reportData($allJobs,  array( "jobType", "processor", "guid" ));

