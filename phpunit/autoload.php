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

	$config->setValue("Repository/cache", TEST_ROOT_PATH . "/cache" );
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

function test_filePath($name = null, $purge = false)
{
	is_null($name) == false || die( "no test file specified");

	$path = appendPath( SYSTEM_PATH, "tests", $name );

	if ($purge == true && is_file($path) ) {
		unlink( $path );
	}
	return $path;
}

function test_filePathExists($name = null)
{
	$path = test_filePath( $name );
	return is_file($path);
}

function test_metadataFor($name = null, $purge = false)
{
	if ( is_null($name) ) {
		$name = "test.json";
	}
	$path = test_filePath( $name, $purge );
	return Metadata::forDirectoryAndFile( dirname($path), basename($path) );
}

$root = TEST_ROOT_PATH . "/" . basename(__FILE__, ".php");
SetConfigRoot( $root );

// my_echo( );
// my_echo( "Creating Database" );
// Migrator::Upgrade( Config::GetLog() );

echo SYSTEM_PATH . PHP_EOL;
