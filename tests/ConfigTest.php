<?php
	$system_path = dirname(dirname(__FILE__));
	if (realpath($system_path) !== FALSE)
	{
		$system_path = realpath($system_path).'/';
	}

	define('SYSTEM_PATH', str_replace("\\", "/", $system_path));
	define('APPLICATION_PATH', SYSTEM_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR);

	require SYSTEM_PATH .'application/config/bootstrap.php';
	require SYSTEM_PATH .'application/config/autoload.php';
	require SYSTEM_PATH .'application/config/common.php';
	require SYSTEM_PATH .'application/config/errors.php';
	require SYSTEM_PATH .'application/libs/Config.php';

	require SYSTEM_PATH .'tests/_ResetConfig.php';
	require SYSTEM_PATH .'tests/_Data.php';

	try {

		$config = Config::instance();

		echo "Internet/appname = " . $config->get("Internet/appname") . PHP_EOL;

		echo "Repository/path = " . Config::Get("Repository/path") . PHP_EOL;
		echo "Repository/NoSuchValue = " . $config->get("Repository/NoSuchValue", 'Default') . PHP_EOL;
	}
	catch (Exception $e) {
		echo "Error :" . $e . PHP_EOL;
	}
