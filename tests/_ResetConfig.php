<?php

// guard to ensure basic configuration is loaded
defined('SYSTEM_PATH') || exit("SYSTEM_PATH not found.");

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
