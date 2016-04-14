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

require SYSTEM_PATH .'tests/_ResetConfig.php';
require SYSTEM_PATH .'tests/_Data.php';

$root = TEST_ROOT_PATH . "/" . basename(__FILE__, ".php");
SetConfigRoot( $root );

my_echo( );
my_echo( "Creating Database" );
Migrator::Upgrade( Config::GetLog(), true );

$options = getopt( "f:");
if ( isset( $options['f']) == false ) {
	echo "Import -f <path/to/Contenta.export>" . PHP_EOL;
	die( "Source directory is required" . PHP_EOL );
}

$import = new db\ImportData( $options['f'] );
$import->importAll();



?>
