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
require SYSTEM_PATH .'application/libs/Logger.php';

require SYSTEM_PATH .'tests/_ResetConfig.php';
require SYSTEM_PATH .'tests/_Data.php';

$root = "/tmp/test/" . basename(__FILE__, ".php");
SetConfigRoot( $root );

echo "Random " . randomPath() . PHP_EOL;

echo PHP_EOL;

$array = array(
	"A Red Mass for Mars" => array(
		"2008" => array(
			"1" => "Eternity",
			"2" => "Liberty"
		),
		"2012" => array(
			"1" => "Equality",
			"2" => "Fraternity"
		)
	)
);

foreach( $array as $series => $years ) {
	foreach( $years as $year => $issues ) {
		foreach( $issues as $issue => $name ) {
			echo appendPath( $series, $year, $issue, $name) . " " . hash(HASH_DEFAULT_ALGO, sanitizedPath($series, $year, $issue, $name)) . PHP_EOL;
		}
	}
}

echo PHP_EOL;


echo "Random " . randomPath() . PHP_EOL;

echo PHP_EOL;

$tables = array( 'character', 'publisher', 'series', 'publication', 'issue' );
for ($x = 1; $x <= 10000; $x++) {
	$table = array_rand($tables);

//  	echo $x . "\t" . Config::GetMedia( $tables[$table], substr("00".dechex($x % 256),-2), $x ) . PHP_EOL;

	if ( substr("00".dechex($x % 256),-2) === 'ca' ) {

		echo '|' . $x . "|" . dechex($x) . "|" . $x%256 . "|". substr("00".dechex($x % 256),-2) . "|`"
			. '/' . substr("00".dechex($x % 256),-2) . '/' . $x . '/` |`'
			. 'publication'. '/' . substr("00".dechex($x % 256),-2) . '/' . $x . '/` |' . PHP_EOL;
	}
}

$x=6397;
