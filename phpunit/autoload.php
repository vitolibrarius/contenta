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

define('TEST_RESOURCE_PATH', SYSTEM_PATH . DIRECTORY_SEPARATOR . 'phpunit' . DIRECTORY_SEPARATOR . '_resources_' . DIRECTORY_SEPARATOR);
define('TEST_ROOT_PATH', "/tmp/ContentaTest" );

function reset_echo($string ="") {
	echo $string . PHP_EOL;
}

function SetConfigRoot($path = null, $purgeFirst = true)
{
	if ( is_null($path) ) {
		$path = TEST_ROOT_PATH . "/unknown";
	}

	$config = Config::instance();

	// override the logger type
	$config->setValue("Repository/path", "$path" );

	$config->setValue("Database/type", "sqlite" );
	$config->setValue("Database/path", "db" );

	$config->setValue("Logging/type", "Print") || die("Failed to change the configured Logging");
	$config->setValue("Logging/path", "logs") || die("Failed to change the configured Logging");

	$config->setValue("Repository/cache", "cache" );
	$config->setValue("Repository/processing", "processing" );

	reset_echo( "** Configuration" );
	reset_echo( "Repository " . $config->repositoryDirectory() );
	reset_echo( "media " . $config->mediaDirectory() );
	reset_echo( "cache " . $config->cacheDirectory() );
	reset_echo( "processing " . $config->processingDirectory() );
	reset_echo( "logs " . $config->loggingDirectory() );

	if ( $purgeFirst == true ) {
		destroy_dir( $path ) || die( "Failed to remove last test run $path");
	}
	makeRequiredDirectory($path, "Test directory $path");
}

function test_resourcePath($name = null)
{
	is_null($name) == false || die( "no test resource file specified");

	$path = appendPath( TEST_RESOURCE_PATH, $name );
	return $path;
}

function test_resourcePathExists($name = null)
{
	$path = test_resourcePath( $name );
	return is_file($path);
}

function test_jsonResource($name = null)
{
	$path = test_resourcePath( $name );
	return Metadata::forDirectoryAndFile( dirname($path), basename($path) );
}

SetConfigRoot( TEST_ROOT_PATH . "/phpunit" );

// my_echo( );
// my_echo( "Creating Database" );
// Migrator::Upgrade( Config::GetLog() );

// echo SYSTEM_PATH . PHP_EOL;
