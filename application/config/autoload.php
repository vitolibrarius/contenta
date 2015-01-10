<?php

/**
 * the auto-loading function, which will be called every time a file "is missing"
 * NOTE: don't get confused, this is not "__autoload", the now deprecated function
 * The PHP Framework Interoperability Group (@see https://github.com/php-fig/fig-standards) recommends using a
 * standardized auto-loader https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md, so we do:
 */
function autoload($class) {
	$parts = explode('\\', $class);
	$namesp_class =  implode(DIRECTORY_SEPARATOR, $parts);
	// if file does not exist in LIBS_PATH folder [set it in config/config.php]
	if (file_exists(LIBS_PATH . $namesp_class . ".php")) {
		require LIBS_PATH . $namesp_class . ".php";
	} 
}

// spl_autoload_register defines the function that is called every time a file is missing. as we created this
// function above, every time a file is needed, autoload(THENEEDEDCLASS) is called
spl_autoload_register("autoload");


function loadModel($name)
{
	$path = MODELS_PATH . strtolower($name) . '_model.php';

	if (file_exists($path)) {
		require_once MODELS_PATH . strtolower($name) . '_model.php';
		// The "Model" has a capital letter as this is the second part of the model class name,
		// all models have names like "LoginModel"
		$modelName = ucwords($name) . 'Model';
		// return the new model object while passing the database connection to the model
		return new $modelName(Database::instance());
	}
}

/**
 * loads the Processor with the given name.
 * @param $name string name of the Processor
 */
function loadProcessor($name, $file)
{
	isset($file) || die('Unable to load processor ' . $name . ', missing processing directory');
	$path = PROCESSOR_PATH . strtolower($name) . '_processor.php';
	if (file_exists($path)) {
		$classNames = classNames($path);
		$processorName = strtolower($name . 'Processor');
		$match = $classNames[$processorName];

		require_once $path;
		return new $match($file);
	}
}

function loadConnector($name, $endpoint)
{
	$path = CONNECTOR_PATH . strtolower($name) . '_connector.php';

	if (file_exists($path)) {
		require_once $path;
		$connectorName = ucwords($name) . 'EndpointConnector';
		return new $connectorName( $endpoint );
	}
}
