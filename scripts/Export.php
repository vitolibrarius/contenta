#! /usr/bin/env php
<?php

$system_path = dirname(dirname(__FILE__));
if (realpath($system_path) !== FALSE)
{
	$system_path = realpath($system_path). DIRECTORY_SEPARATOR;
}

define('SYSTEM_PATH', str_replace("\\", DIRECTORY_SEPARATOR, $system_path));
define('APPLICATION_PATH', SYSTEM_PATH . DIRECTORY_SEPARATOR . 'application');
define('MODELS_PATH', APPLICATION_PATH . DIRECTORY_SEPARATOR . 'model');

require SYSTEM_PATH .'application/config/bootstrap.php';
require SYSTEM_PATH .'application/config/autoload.php';
require SYSTEM_PATH .'application/config/common.php';
require SYSTEM_PATH .'application/config/errors.php';
require SYSTEM_PATH .'application/libs/db/ExportData.php';

$config = Config::instance();

// override the logger type
$config->setValue("Logging/type", "Print") || die("Failed to change the configured Logging");
$config->setValue("Logging/path", "logs") || die("Failed to change the configured Logging");

$options = getopt( "f:");
if ( isset( $options['f']) == false ) {
	echo "Export -f <source/root>" . PHP_EOL;
	die( "Source directory is required" );
}

if ( is_dir($options['f']) == false ) {
	mkdir($options['f'], DIR_PERMS, true) || die( "Failed to create " . $options['f']);
}

$db = new Database();
$export = new db\ExportData_sqlite( appendPath($options['f'], "Contenta.export"), $db );
$export->exportAll();


?>
