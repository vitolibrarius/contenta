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

$options = getopt( "f:");
echo "Options " . var_export($options, true) . PHP_EOL. PHP_EOL;

if (is_file($options['f']) ) {

	my_echo( );
	my_echo( "---------- Hashes ");
	print_r(hash_algos());

	my_echo( );
	my_echo( "---------- Hashes ");
	foreach ( hash_algos() as $algo ) {
		$start = microtime(true);;
		$hash = hash_file( $algo , $options['f'] );
		my_echo( str_pad ( $algo, 12) . " " . (microtime(true) - $start) . "\t $hash \t\t " );
	}
}
else {
	my_echo( );
	my_echo( "---------- file not found for " . $options['f']);
}
