<?php

$system_path = dirname(dirname(__FILE__));
if (realpath($system_path) !== FALSE)
{
	$system_path = realpath($system_path).'/';
}

define('SYSTEM_PATH', str_replace("\\", "/", $system_path));
define('APPLICATION_PATH', SYSTEM_PATH . DIRECTORY_SEPARATOR . 'application' . DIRECTORY_SEPARATOR);

require SYSTEM_PATH .'application/config/bootstrap.php';
require SYSTEM_PATH .'application/config/autoload.php';
require SYSTEM_PATH .'application/config/common.php';
require SYSTEM_PATH .'application/config/errors.php';
require SYSTEM_PATH .'application/libs/Config.php';
require SYSTEM_PATH .'application/libs/Cache.php';
require SYSTEM_PATH .'application/libs/Logger.php';

$config = Config::instance();
$config->setValue('Repository/cache', appendPath( sys_get_temp_dir(), 'TestCache') ) || die('Failed to change cache directory');
$config->setValue("Logging/type", "Print") || die("Failed to change the configured logger");
Logger::resetInstance();

echo "Cache path " . $config->cacheDirectory() . PHP_EOL;

function getImage($wrapper, $name) {
	$original = $wrapper->wrappedDataForName($name);
	$thumbnail = $wrapper->wrappedThumbnailForName($name);

	if ($original ) {
		echo "Saving original " . '/tmp/' . sanitize($name) . PHP_EOL;
		file_put_contents( '/tmp/' . sanitize($name), $original );
	}
	else {
		echo "No original data " . $name . PHP_EOL;
	}

	if ($thumbnail ) {
		echo "Saving thumbnail " . '/tmp/' . sanitize($wrapper->wrappedThumbnailNameForName($name)) . PHP_EOL;
		file_put_contents( '/tmp/' . sanitize($wrapper->wrappedThumbnailNameForName($name)), $thumbnail );
	}
	else {
		echo "No thumbnail data " . $name . PHP_EOL;
	}
}

$options = getopt( "f:c:e::");
echo "Options " . var_export($options, true) . PHP_EOL. PHP_EOL;

if (is_file($options['f']) ) {
	$wrapper = utilities\FileWrapper::instance($options['f']);
	echo 'wrapper ' . var_export($wrapper, true) . PHP_EOL;
	if ($wrapper != false) {
		echo 'file list ' . var_export($wrapper->wrapperContents(), true) . PHP_EOL;


		if ( isset($options['c']) ) {
			$cmd = $options['c'];
			$temp = appendPath( '/tmp', $wrapper->cacheKeyRoot(), $cmd );
			echo "Temp dir $temp" . PHP_EOL;
			if ( is_dir($temp) ) {
				destroy_dir($temp) || die( "unable to delete $temp" );
			}
			mkdir( $temp ) || die( "Unable to create $temp" );

			if ( $cmd == 'unwrap' ) {
				$success = $wrapper->unwrapToDirectory( $temp );
				echo "Unwrap " . ($success == true ? "success" :  "fail") . PHP_EOL;
			}

			if ( $cmd == 'wrap' ) {
				$success = $wrapper->unwrapToDirectory( $temp );
				echo "Unwrap " . ($success == true ? "success" :  "fail") . PHP_EOL;
				$ext = "zip";
				if ( isset($options['e']) ) {
					$ext = $options['e'];
				}
				$newfile = $wrapper->cacheKeyRoot() . "." . $ext;
				$dest = appendPath( '/tmp', $wrapper->cacheKeyRoot(), $newfile );
				(file_exists($dest) == false) || unlink($dest) || die( "Unable to delete old $dest");
				$newWrapper = utilities\FileWrapper::createWrapperForSource($temp, $dest, $ext);
				if ( $newWrapper == false ) {
					echo "Wrap failed " . PHP_EOL;
				}
				else {
					echo "new Wrap $dest " . PHP_EOL;
				}

			}

			if ( $cmd == 'image' ) {
				if ( isset($options['e']) ) {
					getImage($wrapper, $options['e']);
				}
				else {
					$list = $wrapper->wrapperContents();
					getImage($wrapper, $list[0]);
				}
			}
		}
	}
	else {
		echo "Not a wrapper " . var_export($wrapper, true) . PHP_EOL;
	}
}
else {
	echo "Not a file " . $options['f'] . PHP_EOL;
}

?>
