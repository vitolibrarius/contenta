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

	use connectors\ComicVineConnector as ComicVineConnector;

	use model\Endpoint as Endpoint;
	use model\Endpoint_Type as Endpoint_Type;

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

		loadData( $ep_model, array($metadata->readMetadata()) );
	}

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
