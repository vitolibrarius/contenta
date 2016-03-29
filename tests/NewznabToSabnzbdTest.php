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

	use connectors\SABnzbdConnector as SABnzbdConnector;
	use connectors\NewznabConnector as NewznabConnector;

	use processor\FluxImporter as FluxImporter;
	use processor\FluxStatusUpdater as FluxStatusUpdater;

	use model\Endpoint as Endpoint;
	use model\Endpoint_Type as Endpoint_Type;
	use model\Flux as Flux;
	use model\FluxDBO as FluxDBO;


$root = TEST_ROOT_PATH . "/" . basename(__FILE__, ".php");
SetConfigRoot( $root, false );

my_echo( );
my_echo( "Creating Database" );
Migrator::Upgrade( Config::GetLog() );

my_echo( "---------- Newznab Endpoint ");
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

my_echo( "---------- SABnzbd endpoint ");
$sab_endpoint_type = Model::Named('Endpoint_Type')->endpointTypeForCode(model\Endpoint_Type::SABnzbd);
($sab_endpoint_type != false && $sab_endpoint_type->code == 'SABnzbd') || die("Could not find Endpoint_Type::SABnzbd");

$ep_model = Model::Named('Endpoint');
$FluxModel = Model::Named('Flux');

$points = $ep_model->allForTypeCode(Endpoint_Type::SABnzbd);
if ( is_array($points) == false || count($points) == 0) {
	$metadata = metadataFor(Endpoint_Type::SABnzbd . ".json");
	if ( $metadata->isMeta( model\Endpoint::api_key ) == false )
	{
		$metadata->setMeta( model\Endpoint::name, "My SABnzbd" );
		$metadata->setMeta( model\Endpoint::type_id, $sab_endpoint_type->id );
		$metadata->setMeta( model\Endpoint::base_url, "http://localhost:8080/api" );
		$metadata->setMeta( model\Endpoint::api_key, "YOUR API KEY HERE" );
		$metadata->setMeta( model\Endpoint::username, 'vito' );
		$metadata->setMeta( model\Endpoint::enabled, Model::TERTIARY_TRUE );
		$metadata->setMeta( model\Endpoint::compressed, Model::TERTIARY_FALSE );

		die( "Please configure the SABnzbd.json config file with your API key" );
	}

	loadData( $ep_model, array($metadata->readMetadata()) );
}

$points = $ep_model->allForTypeCode(Endpoint_Type::Newznab);
($points != false && count($points) > 0) || die('No Newznab endpoint defined');
$newznab_point = $points[0];
$connection = new NewznabConnector( $newznab_point );

$points = $ep_model->allForTypeCode(Endpoint_Type::SABnzbd);
($points != false && count($points) > 0) || die('No SABnzbd endpoint defined');
$sabnzbd_point = $points[0];
$sab_connection = new SABnzbdConnector($sabnzbd_point);

$fluxImporter = new FluxImporter( 'abc' );
$fluxImporter->setEndpoint( $sabnzbd_point );

$incomplete = $FluxModel->destinationIncomplete();
if ( is_array($incomplete) && count($incomplete) > 0 ) {
	$fluxStatus = new FluxStatusUpdater();
	$fluxStatus->setEndpoint( $sabnzbd_point );
	$fluxStatus->processData();

// 	$queue = $sab_connection->queueSlots();
// 	$history = $sab_connection->historySlots();
// 	foreach( $queue as $slot ) {
// 		$sab_id = $slot['nzo_id'];
// 		$sab_percent = $slot['percentage'];
// 		$sab_status = $slot['status'];
// 		$flux = $FluxModel->objectForDestinationEndpointGUID($sab_connection->endpoint(), $sab_id);
// 		if ( $flux != false && $flux->isComplete() == false) {
// 		// 			Flux::dest_endpoint, Flux::dest_guid, Flux::dest_submission, Flux::dest_status
//
// 			$FluxModel->updateObject( $flux, array( Flux::dest_status => $sab_status . ' ' . $sab_percent . '%'	));
// 		}
// 	}
//
// 	foreach( $history as $slot ) {
// 		$sab_id = $slot['nzo_id'];
// 		$sab_fail_message = $slot['fail_message'];
// 		$sab_status = $slot['status'];
// 		$flux = $FluxModel->objectForDestinationEndpointGUID($sab_connection->endpoint(), $sab_id);
// 		if ( $flux != false && $flux->isComplete() == false) {
// 			if ( $sab_status == 'Failed' ) {
// 				$FluxModel->updateObject( $flux, array(
// 						Flux::flux_error => Model::TERTIARY_TRUE,
// 						Flux::dest_status => $sab_status . " ($sab_fail_message)",
// 					)
// 				);
// 			}
// 			else {
// 				$FluxModel->updateObject( $flux, array(Flux::dest_status => $sab_status));
// 			}
// 		}
// 	}
}
else {
	my_echo( "Search Newznab comics for 'Steampunk Battlestar Galactica 04 2014'" );
	$xml = $connection->searchComics("Steampunk Battlestar Galactica 04 2014");
	$count = 0;
	if ( is_array($xml) ) {
		foreach ($xml as $key => $item) {
			$name = $item['title'] . ".nzb";
			$guid = $item['guid'];
			$publishedDate = $item['publishedDate'];
			$url = $item['url'];
				my_echo( "looking for $name" );

			$flux = $FluxModel->objectForSourceGUID($guid);
			if ( $flux == false ) {
				my_echo( "Found $name" );
				$flux = $FluxModel->create( null, $name, $newznab_point, $guid, $publishedDate, $url );
				$fluxImporter->importFlux( $flux );
				$count++;

				if ( $count > 2 ) {
					break;
				}
			}
		}
	}

	$fluxImporter->processData();
}
die();

if ( is_array($xml) ) {
	foreach ($xml as $key => $item) {
		$name = $item['title'] . ".nzb";
		$guid = $item['guid'];
		$publishedDate = $item['publishedDate'];
		$url = $item['url'];

		$flux = $FluxModel->objectForSourceGUID($guid);
		if ( $flux == false ) {
			my_echo( "Found $name" );
			$flux = $FluxModel->create( null, $name, $newznab_point, $guid, $publishedDate, $url );
			$nzb = file_get_contents($url);
			if ( $nzb != null ) {
				$localFile = appendPath( $root, sanitize($name) );
				my_echo( "	" . $name );
				if (file_put_contents($localFile, $nzb)) {
					$FluxModel->updateObject( $flux,
						array(
							Flux::flux_hash => hash_file(HASH_DEFAULT_ALGO, $localFile),
							Flux::src_status => 'Downloaded'
						)
					);

					$upload = $sab_connection->addNZB( $localFile );
						var_dump($upload);

					if ( is_array($upload) && isset($upload['status'], $upload['nzo_ids']) && $upload['status'] == true ) {
						$FluxModel->updateObject( $flux,
							array(
								Flux::dest_endpoint => $sabnzbd_point->id,
								Flux::dest_status => 'Active',
								Flux::dest_guid => $upload['nzo_ids'][0],
								Flux::dest_submission => time()
							)
						);
						die();
					}
					else {
						$FluxModel->updateObject( $flux,
							array(
								Flux::dest_endpoint => $sabnzbd_point,
								Flux::dest_status => 'Failed ' . var_export($upload, true)
							)
						);
					}
				}
			}
			else {
				$FluxModel->updateObject( $flux,
					array(
						Flux::flux_error => Model::TERTIARY_TRUE,
						Flux::src_status => 'Download failed'
					)
				);
			}

		}
		else {
			my_echo( "Item '$name' for $guid is already loaded" );
		}
	}
}
