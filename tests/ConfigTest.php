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

		echo "Internet/appname = " . $config->get("Internet/appname") . PHP_EOL;

		echo "Repository/path = " . $config->get("Repository/path") . PHP_EOL;
		echo "Repository/dir_permission = " . $config->get("Repository/dir_permission", '0755') . PHP_EOL;
		echo "Repository/file_permission = " . $config->get("Repository/file_permission", '0644') . PHP_EOL;

		echo "Repository/NoSuchValue = " . $config->get("Repository/NoSuchValue", 'Default') . PHP_EOL;
	}
	catch (Exception $e) {
		echo "Error :" . $e . PHP_EOL;
	}
