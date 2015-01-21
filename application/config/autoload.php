<?php

// guard to ensure basic configuration is loaded
defined('APPLICATION_PATH') || exit("APPLICATION_PATH not found.");

class ClassNotFoundException extends Exception {}

/**
 * the auto-loading function, which will be called every time a file "is missing"
 * NOTE: don't get confused, this is not "__autoload", the now deprecated function
 * The PHP Framework Interoperability Group (@see https://github.com/php-fig/fig-standards) recommends using a
 * standardized auto-loader https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md, so we do:
 */
class Autoloader
{
    static public function loader($className)
    {
    	$className = ltrim($className, '\\');
		$parts = explode('\\', $className);
		if ( count($parts) == 1 || in_array($parts[0], array('controller', 'model', 'processor')) == false ) {
			array_unshift($parts, 'libs');
		}
		array_unshift($parts, APPLICATION_PATH);
		$filename = implode(DIRECTORY_SEPARATOR, $parts) . ".php";
        if (file_exists($filename)) {
            include($filename);
            if (class_exists($className)) {
                return TRUE;
            }
        }

        throw new ClassNotFoundException($className);
        return FALSE;
    }
}
spl_autoload_register('Autoloader::loader');
