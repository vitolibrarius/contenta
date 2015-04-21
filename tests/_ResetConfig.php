<?php

// guard to ensure basic configuration is loaded
defined('SYSTEM_PATH') || exit("SYSTEM_PATH not found.");

function SetConfigRoot($path = null, $purgeFirst = true)
{
	if ( is_null($path) ) {
		$path = "/tmp/test/unknown";
	}

	$config = Config::instance();

	// override the logger type
	$config->setValue("Repository/path", "$path" );

	$config->setValue("Database/type", "sqlite" );
	$config->setValue("Database/path", "db" );

	$config->setValue("Logging/type", "Print") || die("Failed to change the configured Logging");
	$config->setValue("Logging/path", "logs") || die("Failed to change the configured Logging");

	$config->setValue("Repository/cache", "/tmp/test/cache" );
	$config->setValue("Repository/processing", "processing" );

	my_echo( "** Configuration" );
	my_echo( "Repository " . $config->repositoryDirectory() );
	my_echo( "media " . $config->mediaDirectory() );
	my_echo( "cache " . $config->cacheDirectory() );
	my_echo( "processing " . $config->processingDirectory() );
	my_echo( "logs " . $config->loggingDirectory() );

	if ( $purgeFirst == true ) {
		destroy_dir( $path ) || die( "Failed to remove last test run $path");
	}
	makeRequiredDirectory($path, "Test directory $path");
}
