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

use \Metadata as Metadata;

$root = TEST_ROOT_PATH . "/" . basename(__FILE__, ".php");
SetConfigRoot( $root );

$metadata_sql = new Metadata_sqlite( appendPath($root, "Test.sqlite"));

$endpoint_name = $metadata_sql->getMeta( "endpoint_name" );
my_echo( "endpoint_name  = " . $endpoint_name );

$enqueued_publisher_1986_xid = $metadata_sql->getMeta( "enqueued/publisher_1986/xid" );
my_echo( "enqueued/publisher_1986/xid  = " . $enqueued_publisher_1986_xid );

$enqueued_publisher_1986 = $metadata_sql->getMeta( "enqueued/publisher_1986" );
my_echo( "enqueued/publisher_1986  = " . var_export($enqueued_publisher_1986, true) );

my_echo();
my_echo("-=-=-=-=-=-=-=-");
my_echo();

$metadata_sql->setMeta( "model/Endpoint/name", "Newznab Source" );
$metadata_sql->setMeta( "model/Endpoint/type_code", 12345.678 );
$metadata_sql->setMeta( "model/Endpoint/base_url", "YOUR Newznab site base url here" );
$metadata_sql->setMeta( "model/Endpoint/api_key", "YOUR API KEY HERE" );
$metadata_sql->setMeta( "model/Endpoint/username", 'vito' );
$metadata_sql->setMeta( "model/Endpoint/enabled", true );
$metadata_sql->setMeta( "model/Endpoint/compressed", false );
$metadata_sql->setMeta( "model/Endpoint/image", array("name"=>"Back Cover", "url"=>"http://not.real.com/image?1234") );
$metadata_sql->setMeta( "model/Endpoint/myname", array("david", "aspinall", "coool") );

$model = $metadata_sql->getMeta( "model/Endpoint" );
my_echo( "model/Endpoint  = " . var_export($model, true) );

$metadata_sql->setMeta( "model/Endpoint/image", "replace Cover");
$model = $metadata_sql->getMeta( "model/Endpoint" );
my_echo( "model/Endpoint  = " . var_export($model, true) );
