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
require SYSTEM_PATH .'application/libs/db/ExportData.php';

define('TEST_RESOURCE_PATH', SYSTEM_PATH . DIRECTORY_SEPARATOR . 'phpunit' . DIRECTORY_SEPARATOR . '_resources_' . DIRECTORY_SEPARATOR);
define('TEST_ROOT_PATH', "/tmp/ContentaTest" );

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


	echo "** Configuration" . PHP_EOL;
	echo "Repository " . $config->repositoryDirectory() . PHP_EOL;
	echo "media " . $config->mediaDirectory() . PHP_EOL;
	echo "cache " . $config->cacheDirectory() . PHP_EOL;
	echo "processing " . $config->processingDirectory() . PHP_EOL;
	echo "logs " . $config->loggingDirectory() . PHP_EOL;

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

function test_initializeDatabase($reset = false)
{
	if ( $reset == true ) {
		SetConfigRoot( TEST_ROOT_PATH . "/phpunit", $reset );
	}
	echo "Creating Database"  . PHP_EOL;
	Migrator::Upgrade( Config::GetLog() );
}

function test_importTestDataDirectory( )
{
	$retval = appendPath( TEST_RESOURCE_PATH, "data");
	is_dir($retval) ||  die('test_importTestDataDirectory could not find ' . $retval . PHP_EOL);
	return $retval;
}

function test_importTestData( array $models = array() )
{
	$importer = new \db\ImportData( test_importTestDataDirectory(), $models );
	$importer->importAll();
}

function test_exportTestData( array $models = array() )
{
	$exporter = new \db\ExportData_sqlite( test_importTestDataDirectory() . "1", \Database::instance() );
	$exporter->exportAll();
}


SetConfigRoot( TEST_ROOT_PATH . "/phpunit" );
