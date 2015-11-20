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

$root = TEST_ROOT_PATH . "/" . basename(__FILE__, ".php");
SetConfigRoot( $root );

$metadata = metadataFor("Filenames.json");
$testdata = $metadata->getMeta( "/" );

$hasErrors = false;
foreach( $testdata as $filename => $expected ) {
	echo ".";
	$mediaFilename = new utilities\MediaFilename($filename);
	$meta = $mediaFilename->updateFileMetaData(null);

	$result_diff_a = array_diff($meta, $expected);
	$result_diff_b = array_diff($expected, $meta);
	$result_assoc = array_diff_assoc($meta, $expected);
	if ( count($result_diff_a) > 0 || count($result_assoc) > 0 || count($result_diff_b) > 0) {
		echo PHP_EOL . $filename . PHP_EOL;
		$result = array_unique( array_merge(array_keys($result_diff_a), array_keys($result_diff_b), array_keys($result_assoc)) );
		foreach ($result as $key => $value) {
			$hasErrors = true;
			echo "	Expected value for [" . $value . "] = '"
				. (isset($expected[$value]) ? $expected[$value] : 'null') . "' but got '"
				. (isset($meta[$value]) ? $meta[$value] : 'null') . "'" . PHP_EOL;
		}
		echo PHP_EOL;
	}

	if ( $hasErrors == true ) {
		echo "Errors encountered" . PHP_EOL;
		exit(1);
	}
}

if ( $hasErrors == false ) {
	echo "No errors encountered" . PHP_EOL;
}
?>
