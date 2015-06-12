<?php

$system_path = dirname(dirname(__FILE__));
if (realpath($system_path) !== FALSE)
{
	$system_path = realpath($system_path).DIRECTORY_SEPARATOR;
}

define('SYSTEM_PATH', str_replace("\\", DIRECTORY_SEPARATOR, $system_path));
define('APPLICATION_PATH', SYSTEM_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR);

require SYSTEM_PATH .'application/config/bootstrap.php';
require SYSTEM_PATH .'application/config/autoload.php';
require SYSTEM_PATH .'application/config/common.php';
require SYSTEM_PATH .'application/config/errors.php';
require SYSTEM_PATH .'application/libs/Config.php';
require SYSTEM_PATH .'application/libs/Cache.php';

require SYSTEM_PATH .'tests/_ResetConfig.php';
require SYSTEM_PATH .'tests/_Data.php';

use \Config as Config;
use \Logger as Logger;
use \Processor as Processor;
use utilities\Metadata as Metadata;


$root = TEST_ROOT_PATH . "/" . basename(__FILE__, ".php");
SetConfigRoot( $root, false);

my_echo( );
my_echo( "Creating Database" );
Migrator::Upgrade( Config::GetLog() );

$name = db\Qualifier::Equals( 'a', "name", "David" );
my_echo( "select * from table where " . $name->sqlStatement() . " | " . var_export($name->sqlParameters(), true));

$age = db\Qualifier::Equals( 'a', "age", "47" );
my_echo( "select * from table where " . $age->sqlStatement() . " | " . var_export($age->sqlParameters(), true));

$notname = db\Qualifier::NotQualifier( $name );
my_echo( "select * from table where " . $notname->sqlStatement() . " | " . var_export($notname->sqlParameters(), true));

$andname = db\Qualifier::AndQualifier( $name, $age, $notname );
my_echo( "select * from table where " . $andname->sqlStatement() . " | " . var_export($andname->sqlParameters(), true));

