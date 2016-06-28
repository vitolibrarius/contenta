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
	$config->setValue("Debug/localized", true );

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

function test_generateRandomString($length = 10, $characters = null)
{
	if ( is_null($characters) ) {
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	}
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
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

function test_initializeDatabase($reset = true)
{
	if ( $reset == true ) {
		$db_path = Config::GetPath("Database/path", null);
		destroy_dir( $db_path ) || die( "Failed to remove last test db $db_path");
		Database::ResetConnection();
	}
	$config = Config::instance();

	$config->setValue("Logging/type", "File") || die("Failed to change the configured Logging");
	$config->setValue("Logging/prefix", "Migration") || die("Failed to change the configured Logging");
	Logger::resetInstance();
	Migrator::Upgrade( Config::GetLog() );

	$caller = callerClassAndMethod("test_initializeDatabase");
	$config->setValue("Logging/prefix", sanitize_filename($caller['class'], 256, true, true)) || die("Failed to change the configured Logging");
	Logger::resetInstance();
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

function test_exportTestData( $testName, array $models = array() )
{
	$path = appendPath( TEST_ROOT_PATH, (is_string($testName) ? $testName : uuid()) );
		echo PHP_EOL . $path . PHP_EOL;
	(is_dir($path) == false) || destroy_dir($path) || die( "Failed to delete $path" );
	$exporter = new \db\ExportData_sqlite( $path, \Database::instance() );
	$exporter->exportAll();
}

function test_mediaSamplesDirectory()
{
	$retval = appendPath( TEST_RESOURCE_PATH, "media");
	is_dir($retval) ||  die('test_mediaSamplesDirectory could not find ' . $retval . PHP_EOL);
	return $retval;
}

function test_mediaSamplesFile( $filename = '')
{
	return appendPath( TEST_RESOURCE_PATH, "media", $filename );
}

function test_copyMediaSamplesFile( $filename = '')
{
	$source = appendPath( TEST_RESOURCE_PATH, "media", $filename );
	if ( file_exists($source) ) {
		$destFilename = uuid() . "-" . $filename;
		$destination = appendPath(Config::GetRepository(), $destFilename);
		if ( copy($source, $destination) == false ) {
			return false;
		}
		return $destination;
	}
	return false;
}

function test_RandomWords($number_words = 5, $length_min = 5, $length_max = 30)
{
	$words = array();
	for($idx = 0; $idx < $number_words; $idx++ ) {
		$length = rand($length_min, $length_max);
		$words[] = test_RandomString( $length );
	}
	return implode(" ", $words);
}

function test_RandomNumber($min = 0, $max = 100)
{
	return rand($min, $max);
}

function test_RandomString( $length = 5, $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
{
	$randstring = '';
	for ($i = 0; $i < $length; $i++) {
		$randstring .= $characters[rand(0, strlen($characters) -1)];
	}
	return $randstring;
}

SetConfigRoot( TEST_ROOT_PATH . "/phpunit" );
