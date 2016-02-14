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

	use model\Endpoint as Endpoint;
	use model\Endpoint_Type as Endpoint_Type;

$root = TEST_ROOT_PATH . "/" . basename(__FILE__, ".php");
SetConfigRoot( $root, false );

my_echo( );
my_echo( "Creating Database" );
Migrator::Upgrade( Config::GetLog() );

my_echo( "---------- Endpoint ");
$cv_endpoint_type = Model::Named('Endpoint_Type')->endpointTypeForCode(model\Endpoint_Type::SABnzbd);
($cv_endpoint_type != false && $cv_endpoint_type->code == 'SABnzbd') || die("Could not find Endpoint_Type::SABnzbd");

$ep_model = Model::Named('Endpoint');
$points = $ep_model->allForTypeCode(Endpoint_Type::SABnzbd);
if ( is_array($points) == false || count($points) == 0) {
	$metadata = metadataFor(Endpoint_Type::SABnzbd . ".json");
	if ( $metadata->isMeta( model\Endpoint::api_key ) == false )
	{
		$metadata->setMeta( model\Endpoint::name, "My SABnzbd" );
		$metadata->setMeta( model\Endpoint::type_id, $cv_endpoint_type->id );
		$metadata->setMeta( model\Endpoint::base_url, "http://localhost:8080/api" );
		$metadata->setMeta( model\Endpoint::api_key, "YOUR API KEY HERE" );
		$metadata->setMeta( model\Endpoint::username, 'vito' );
		$metadata->setMeta( model\Endpoint::enabled, Model::TERTIARY_TRUE );
		$metadata->setMeta( model\Endpoint::compressed, Model::TERTIARY_FALSE );

		die( "Please configure the SABnzbd.json config file with your API key" );
	}

	loadData( $ep_model, array($metadata->readMetadata()) );
}

$points = $ep_model->allForTypeCode(Endpoint_Type::SABnzbd);
($points != false && count($points) > 0) || die('No endpoint defined');

$epoint = $points[0];
my_echo( "Connecting using " . $epoint );
$connection = new SABnzbdConnector($epoint);

$version = $connection->sabnzbdVersion();
my_echo ("** SABnzbd version " . $version['version']);
// $categories = $connection->categories();
// var_dump($categories);
// $scripts = $connection->scripts();
// var_dump($scripts);
$queue = $connection->queue();
my_echo( "---------------------------" );
echo json_encode($queue, JSON_PRETTY_PRINT);
my_echo( "---------------------------" );
$queue = $connection->history();
echo json_encode($queue, JSON_PRETTY_PRINT);
my_echo( "---------------------------" );

$history = $connection->historySlots();
foreach( $history as $slot ) {
	$sab_id = $slot['nzo_id'];
	$sab_fail_message = $slot['fail_message'];
	$sab_status = $slot['status'];
	my_echo( "$sab_id => " . $slot['name'] . " $sab_status ($sab_fail_message)" );
}

my_echo( "---------------------------" );
$del_status = $connection->delete('SABnzbd_nzo_54pQUz');
die();
/* Arguments:
	output=json,
	mode=addfile,
	apikey=<API_KEY>
	nzbname=<FILENAME>,
	nzbfile(form file): filename=<FILENAME>.nzb (application/x-nzb)=<NZB_CONTENT>,
*/
// $postfields = array(
// 	'output' => 'json',
// 	'mode' => 'addfile',
// 	'apikey' => 'da27a8e6736636525f29e9053eec5537',
// 	'nzbname' => basename( $request_file)
// );
//
// $length = filesize($request_file) + strlen(http_build_query($postfields));
// $postfields['nzbfile'] = new CurlFile($request_file,'application/x-nzb', basename( $request_file));
//
// //array("Content-Length: $length")
// 				$headers[] = 'Content-Type:multipart/form-data';
// 				var_dump($postfields);
//
// list( $data, $headers ) = $connection->performPOST( 'http://localhost:8080/api', $postfields, $headers);
// my_echo( "---------------------------" );
// var_dump($headers);
// my_echo( "---------------------------" );
// var_dump($data);
// my_echo( "---------------------------" );
$file = "/Users/daspinall/Downloads/Bucky Barnes - The Winter Soldier 007 (2015) (Digital) (Zone-Empire).cbr.nzb";
$data = $connection->addNZB( $file );
my_echo( "---------------------------" );
var_dump($data);
my_echo( "---------------------------" );

die();
$status  = $connection->statusCheck();
$queue = $connection->queue();
