<?php

	$system_path = dirname(__FILE__);
	if (realpath($system_path) !== FALSE)
	{
		$system_path = realpath($system_path).'/';
	}

	define('SYSTEM_PATH', str_replace("\\", "/", $system_path));

	require SYSTEM_PATH .'application/config/bootstrap.php';
	require SYSTEM_PATH .'application/config/autoload.php';
	require SYSTEM_PATH .'application/config/common.php';
	require SYSTEM_PATH .'application/config/errors.php';


	try {
		$config = Config::instance();
		$app = new Application();
	}
	catch (Exception $exception) {
		echo "<!doctype html><html><head></head><body>";
		$ex_class = get_class($exception);

		echo "<h1>Error</h1>";
		echo "<div class='error'><p>" . $exception->getMessage() . "</p>";
		echo "<pre>" . $exception . "</pre></div>";
		echo "</body></html>";
	}


?>
