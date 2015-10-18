<?php

// default permissions for created directories
define('DIR_PERMS', 0750);

function file_ext_strip($filename)
{
	return preg_replace('/\.[^.]*$/', '', $filename);
}

function file_ext($filename)
{
	return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

function safe_mkdir( $path )
{
	if (isset($path) && strlen($path) > 0) {
		if (realpath($path) !== FALSE) {
			$path = realpath($path) . DIRECTORY_SEPARATOR;
		}

		if (is_dir($path) == false) {
			if (true !== @mkdir($path, DIR_PERMS, TRUE)) {
				if (is_dir($path)) {
					// The directory was created by a concurrent process, so do nothing, keep calm and carry on
				}
				else {
					// There is another problem, we manage it (you could manage it with exceptions as well)
					$error = error_get_last();
					trigger_error($error['message'] . ': Failed to create directory ' . $path, E_USER_WARNING);
				}
			}
		}
		return true;
	}
	return false;
}

function findPathForTool($tool)
{
	if (exec('which /usr/bin/' . $tool) != null) {
		return '/usr/bin/' . $tool;
	}
	else if (exec('which /usr/local/bin/' . $tool)) {
		return '/usr/local/bin/' . $tool;
	}
	else if (exec('which /usr/syno/bin/' . $tool)) {
		return '/usr/syno/bin/' . $tool;
	}

	return false;
}

function shortendPath($path = '', $numComponents = 3 )
{
	$parts = explode(DIRECTORY_SEPARATOR, $path);
	$numComponents = min(count($parts), $numComponents);
	$parts = array_slice($parts, (count($parts) - $numComponents), $numComponents);
	return implode(DIRECTORY_SEPARATOR, $parts);
}

function normalizePath( $path = null, $old = DIRECTORY_SEPARATOR, $new = DIRECTORY_SEPARATOR, $prefix = true, $suffix = false)
{
	if ( isset($path) && strlen($path) > 0) {
		if ( is_null($old) ) {
			$old = DIRECTORY_SEPARATOR;
		}

		if ( is_null($new) ) {
			$new = DIRECTORY_SEPARATOR;
		}

		$components = array_filter( explode($old, $path), 'strlen');
		if ( is_array($components) && count($components) > 0) {
			return (boolval($prefix) ? $new : '') . implode($new, $components) . (boolval($suffix) ? $new : '');
		}
	}
	return null;
}

function appendPath()
{
    $finalPath = '';
    $args = func_get_args();
    $paths = array();
    foreach ($args as $arg) {
        $paths = array_merge($paths, (array)$arg);
    }
    $paths = array_filter( $paths, 'strlen' );

    $path_count = count($paths);
    for ( $idx = 0; $idx < $path_count; $idx++) {
		$item = $paths[$idx];
        if ($idx != 0 && $item[0] == DIRECTORY_SEPARATOR)  {
        	$item = substr($item, 1);
        }
        if ($idx != ($path_count - 1) && substr($item, -1) == DIRECTORY_SEPARATOR) {
        	$item = substr($item, 0, -1);
        }
		$finalPath .= $item;
        if ($idx != ($path_count - 1)) {
        	$finalPath .= DIRECTORY_SEPARATOR;
		}
	}
	$finalPath = str_replace("//", "/", $finalPath);
    return $finalPath;
}

function sanitizedPath()
{
    $args = func_get_args();
    $paths = array();
    foreach ($args as $arg) {
        $paths = array_merge($paths, (array)$arg);
    }

    $paths = array_map( function($str) {
    	$trim = trim($str, DIRECTORY_SEPARATOR);
    	return sanitize_filename((string)$trim, 100, false, false);
    }, $paths);
    $paths = array_filter($paths);
    return implode(DIRECTORY_SEPARATOR, $paths);
}

function makeRequiredDirectory($path, $purpose = 'unknown')
{
	isset($path) || die('Required path is not set for ' . $purpose);
	return safe_mkdir( $path );
}

function makeUniqueDirectory( $root, $elements )
{
	if ( is_dir($root) ) {
		$path = sanitizedPath($elements);
		$index = 0;
		$working = $path;
		$full = appendPath($root, $working);
		while ( file_exists($full) == true)
		{
			$index = $index + 1;
			$working = $path . sprintf( ' - 0x%02x', $index);
			$full = appendPath($root, $working);
		}
		return (safe_mkdir($full) ? $full : null);
	}

	\Logger::logError("Unable to find root path '" . (string)$root . "'", "Common", "makeUniqueDirectory");
	return null;
}

function find_entry_with_name($dir, $name)
{
	if ( is_dir($dir) )
	{
		foreach (scandir($dir) as $file)
		{
			if ($file == '.' || $file == '..') continue;
			if ($file === $name)
			{
				return $dir . DIRECTORY_SEPARATOR . $file;
			}
			else if (is_dir($dir . DIRECTORY_SEPARATOR . $file) === true)
			{
				$success = find_entry_with_name($dir . DIRECTORY_SEPARATOR . $file, $name);
				if ( isset($success) === true )
				{
					return $success;
				}
			}
		}
	}

	return null;
}

function find_entry_with_extension($dir, $ext)
{
	if ( is_dir($dir) )
	{
		foreach (scandir($dir) as $file)
		{
			if ($file == '.' || $file == '..') continue;
			if (file_ext($file) === $ext)
			{
				return $dir . DIRECTORY_SEPARATOR . $file;
			}
			else if (is_dir($dir . DIRECTORY_SEPARATOR . $file) === true)
			{
				$success = find_entry_with_extension($dir . DIRECTORY_SEPARATOR . $file, $ext);
				if ( isset($success) === true )
				{
					return $success;
				}
			}
		}
	}

	return null;
}

function recursive_copy($src, $dst, $purgeDestination = false)
{
	if (file_exists($dst) ) {
		if ( $purgeDestination == false || destroy_dir($dst) == false ) {
			return false;
		}
	}

	if (is_dir($src)) {
		is_dir($dst) || mkdir($dst, DIR_PERMS, true) || die('Failed to create destination directory ' . $dst);

		foreach (scandir($src) as $file) {
			if ($file == '.' || $file == '..') continue;
			if ( is_dir($file) ) {
				if ( recusive_copy( appendPath($src, $file), appendPath($dst, basename($file)))  == false ) {
					return false;
				}
			}
			else {
				if ( @copy( appendPath($src, $file), appendPath($dst, $file)) == false ) {
					return false;
				}
			}
		}
	}
	else if (file_exists($src)) {
		if ( @copy($src, $dst) == false ) {
			return false;
		}
	}
	return true;
}

function is_sub_dir($path = '', $parent_folder = SYSTEM_PATH)
{
	$path = realpath($path);
	$parent_folder = realpath($parent_folder);

	if ( strlen($path) == 0 || strlen($path) < strlen($parent_folder) ) {
		// if path is a subfolder, then it must be longer
		return false;
	}

	$parent = explode( DIRECTORY_SEPARATOR, $parent_folder );
	$subs = explode( DIRECTORY_SEPARATOR, $path );
	for( $i=0; $i < count($parent); $i++) {
		if ( $parent[$i] != $subs[$i]) {
			return false;
		}
	}
	return true;
}

function destroy_dir($dir)
{
	if (file_exists($dir)) {
		if (!is_dir($dir) || is_link($dir)) return unlink($dir);
		foreach (scandir($dir) as $file)
		{
			if ($file == '.' || $file == '..') continue;
			if (!destroy_dir($dir . DIRECTORY_SEPARATOR . $file))
			{
				chmod($dir . DIRECTORY_SEPARATOR . $file, 0777);
				if (!destroy_dir($dir . DIRECTORY_SEPARATOR . $file)) return false;
			};
		}
		return rmdir($dir);
	}
	return true;
}

function randomPath()
{
	$hi = sprintf( '%04x', mt_rand(0, 65535));
	$mid = sprintf( '%04x',	bindec(substr_replace(sprintf('%016b', mt_rand(0, 65535)), '0100', 11, 4)));
	$low = sprintf( '%08x', mt_rand());
	return $hi . DIRECTORY_SEPARATOR . $mid . DIRECTORY_SEPARATOR . $low;
}
