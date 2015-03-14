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

	use connectors\ComicVineConnector as ComicVineConnector;

	use model\Endpoint as Endpoint;
	use model\Endpoint_Type as Endpoint_Type;

	try {
		$config = Config::instance();

		// override the logger type
		$config->setValue("Logging/type", "Print") || die("Failed to change the configured Logging");
		$config->setValue("Logging/path", "/tmp/Tests/logs") || die("Failed to change the configured Logging");

		$config->setValue("Repository/cache", "/tmp/Tests/cache" );
		$config->setValue("Repository/processing", "/tmp/Tests/processing" );

		Logger::logWarning( "Test log", basename(__FILE__), "cool" );

	}
	catch (Exception $e) {
		echo "Error :" . $e . PHP_EOL;
	}

	$ep_model = Model::Named('Endpoint');
	$points = $ep_model->allForTypeCode(Endpoint_Type::ComicVine);
	($points != false && count($points) > 0) || die('No endpoint defined');

	$epoint = $points[0];
	echo "Connecting using " . $epoint . PHP_EOL;
	$connection = new ComicVineConnector($epoint);

// 	$results = $connection->queryForCharacterName( "Batman" );
	$results = $connection->storyArcDetails( "40764" );
// 	$results = $connection->seriesDetails( "38859" );
// 	$results = $connection->issueDetails( "260758" );
// 	$results = $connection->personDetails( "7082" );

	echo  json_encode($results, JSON_PRETTY_PRINT) . PHP_EOL;
	echo  PHP_EOL;

	$keys = array_keys($results);
	echo  json_encode($keys, JSON_PRETTY_PRINT) . PHP_EOL;
