<?php

	$system_path = dirname(__FILE__);
	if (realpath($system_path) !== FALSE)
	{
		$system_path = realpath($system_path).'/';
	}

	define('SYSTEM_PATH', str_replace("\\", "/", $system_path));
	define('APPLICATION_PATH', SYSTEM_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR);

	require APPLICATION_PATH .'config/bootstrap.php';
	require APPLICATION_PATH .'config/autoload.php';
	require APPLICATION_PATH .'config/common.php';
	require APPLICATION_PATH .'config/errors.php';

	try {
 		$config = Config::instance();
		$database = Database::instance();
		if ( $database->verifyDatabase() == true ) {
			$app = new Application();
		}
		else {

			echo "<!doctype html><html><head></head><body>";
			echo "<h1>Error</h1>";
			echo "<div class='error'><p>Database not ready</p>";
			echo "<pre>There are missing tables, please check your configuration and re-run the migration</pre></div>";

			$config->setValue("Logging/type", "File") || die("Failed to change the configured logger");
			$config->setValue("Logging/path", "logs/migrations") || die("Failed to change the configured logging path");
			Logger::resetInstance();
			echo "<pre>Logging\n" . var_export(Config::Get("Logging"), true) . "\n";

			$user_model = Model::Named("Users");
			var_dump($user_model);

			$processor = new processor\Migration(currentVersionNumber());
			$processor->processData();

			echo "\nDone</pre>";

			echo "</body></html>";
		}
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
