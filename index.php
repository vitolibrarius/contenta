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
 		if ( $config->absolutePathValue( "Logging/path", null) != null ) {
 			$ini_error_log = ini_get("error_log");
 			$proposed_error_log =  Config::GetLog("script.log");
 			if ( $ini_error_log != $proposed_error_log ) {
 				if (is_file($proposed_error_log) || touch( $proposed_error_log )) {
	 				ini_set("error_log", $proposed_error_log );
 				}
 			}
 		}

		$database = Database::instance();
		if ( $database->verifyDatabase() == true ) {
			$app = new Application();
		}
		else {
			$controller = "Upgrade";
			$action = "index";

			if (isset($_GET['url'])) {
				$url = rtrim($_GET['url'], '/');
				$url = filter_var($url, FILTER_SANITIZE_URL);
				$url = explode('/', $url);
				if ( isset($url[0], $url[1]) && $url[0] === $controller ) {
					$action = $url[1];
				}
			}

			$upgrader = Controller::Named($controller);
			$upgrader->{$action}();
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
