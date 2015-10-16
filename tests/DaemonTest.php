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

use \Config as Config;
use \Logger as Logger;
use \Processor as Processor;
use \Metadata as Metadata;


$root = TEST_ROOT_PATH . "/" . basename(__FILE__, ".php");
SetConfigRoot( $root, false);

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

$other = array();
$config = Config::instance();
foreach( $configPaths as $keypath ) {
	$configValue = $config->getValue( $keypath );
	array_setValueForKeypath( $keypath, $configValue, $other );
}

my_echo();
my_echo( "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-==-=-" );
// echo Daemonize( $workingDir );
echo Daemonize( "TestProcessor", "user_api_code", uuid(), 123, array( "debug" => true, "ConfigOverride" => $other ) );
