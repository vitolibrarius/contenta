<?php
	$system_path = dirname(dirname(__FILE__));
	if (realpath($system_path) !== FALSE)
	{
		$system_path = realpath($system_path).'/';
	}

	define('SYSTEM_PATH', str_replace("\\", "/", $system_path));

	require SYSTEM_PATH .'application/config/bootstrap.php';
	require SYSTEM_PATH .'application/config/autoload.php';
	require SYSTEM_PATH .'application/config/common.php';
	require SYSTEM_PATH .'application/config/errors.php';
	require SYSTEM_PATH .'application/libs/Config.php';

	try {
		$config = Config::instance();

		// override the logger type
		$config->setValue("Logger/type", "Print") || die("Failed to change the configured logger");

		Logger::logWarning( "Test log", basename(__FILE__), "cool" );

	}
	catch (Exception $e) {
		echo "Error :" . $e . PHP_EOL;
	}
