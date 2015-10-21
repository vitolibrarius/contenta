<?php

// guard to ensure basic configuration is loaded
defined('SYSTEM_PATH') || exit("SYSTEM_PATH not found.");

	function exceptionHandler($exception)
	{
		try {
			\Logger::logException($exception);
		}
		catch (Exception $e) {
			print get_class($e)." thrown within the exception handler. Message: ".$e->getMessage()." on line ".$e->getLine();
		}
	}

	set_exception_handler('exceptionHandler');

	function shutdown($msg)
	{
		try {
			\Logger::instance()->fatal($msg);
		}
		catch (Exception $e) {
			print get_class($e)." thrown within the shutdown action. Message: ".$e->getMessage()." on line ".$e->getLine();
		}
		die($msg);
	}

	function shutdownHandler()
	{
		try {
			$error = error_get_last();
			if ( $error !== NULL) {
				$f = shortendPath($error["file"], 3);
				\Logger::instance()->fatal( FriendlyErrorType($error["type"]) . " - " . $error["message"], $f, $error["line"]);

				debug_print_backtrace();
			}
		}
		catch (Exception $e) {
			print get_class($e)." thrown within the shutdown handler. Message: ".$e->getMessage()." on line ".$e->getLine();
		}
	}

	# Registering shutdown function
	register_shutdown_function('shutdownHandler');

	function customError($errno, $errstr, $errfile, $errline) {
	    $trace = debug_backtrace();
	    array_shift($trace);

		$backtrace = "(".$errno.") " . $errstr;
        foreach($trace as $item) {
            $backtrace .= "\n\t" . (isset($item['file']) ? shortendPath($item['file'], 3) : '<unknown file>')
            	. ' ' . (isset($item['line']) ? $item['line'] : '<unknown line>')
            	. ' calling ' . (isset($item['function']) ? $item['function'] : '<unknown function>') . '()';
        }

		//echo "$backtrace, $errfile, $errline";
 		\Logger::LogError( $backtrace, shortendPath($errfile), $errline );
		return true;
	}

	//	set error handler
	set_error_handler("customError", E_ALL);

	function FriendlyErrorType($type)
	{
		switch($type)
		{
			case E_ERROR: // 1 //
				return 'E_ERROR';
			case E_WARNING: // 2 //
				return 'E_WARNING';
			case E_PARSE: // 4 //
				return 'E_PARSE';
			case E_NOTICE: // 8 //
				return 'E_NOTICE';
			case E_CORE_ERROR: // 16 //
				return 'E_CORE_ERROR';
			case E_CORE_WARNING: // 32 //
				return 'E_CORE_WARNING';
			case E_COMPILE_ERROR: // 64 //
				return 'E_COMPILE_ERROR';
			case E_COMPILE_WARNING: // 128 //
				return 'E_COMPILE_WARNING';
			case E_USER_ERROR: // 256 //
				return 'E_USER_ERROR';
			case E_USER_WARNING: // 512 //
				return 'E_USER_WARNING';
			case E_USER_NOTICE: // 1024 //
				return 'E_USER_NOTICE';
			case E_STRICT: // 2048 //
				return 'E_STRICT';
			case E_RECOVERABLE_ERROR: // 4096 //
				return 'E_RECOVERABLE_ERROR';
			case E_DEPRECATED: // 8192 //
				return 'E_DEPRECATED';
			case E_USER_DEPRECATED: // 16384 //
				return 'E_USER_DEPRECATED';
		}
		return var_export($type, true);
	}

	function jsonErrorString($code)
	{
		$constants = get_defined_constants(true);
		$json_errors = array();
		foreach ($constants["json"] as $name => $value) {
			if (!strncmp($name, "JSON_ERROR_", 11)) {
				$json_errors[$value] = $name;
			}
		}
		return $json_errors[$code];
	}

	function zipFileErrMsg($errno)
	{
		// using constant name as a string to make this function PHP4 compatible
		$zipFileFunctionsErrors = array(
			'ZIPARCHIVE::ER_MULTIDISK' => 'Multi-disk zip archives not supported.',
			'ZIPARCHIVE::ER_RENAME' => 'Renaming temporary file failed.',
			'ZIPARCHIVE::ER_CLOSE' => 'Closing zip archive failed',
			'ZIPARCHIVE::ER_SEEK' => 'Seek error',
			'ZIPARCHIVE::ER_READ' => 'Read error',
			'ZIPARCHIVE::ER_WRITE' => 'Write error',
			'ZIPARCHIVE::ER_CRC' => 'CRC error',
			'ZIPARCHIVE::ER_ZIPCLOSED' => 'Containing zip archive was closed',
			'ZIPARCHIVE::ER_NOENT' => 'No such file.',
			'ZIPARCHIVE::ER_EXISTS' => 'File already exists',
			'ZIPARCHIVE::ER_OPEN' => 'Can\'t open file',
			'ZIPARCHIVE::ER_TMPOPEN' => 'Failure to create temporary file.',
			'ZIPARCHIVE::ER_ZLIB' => 'Zlib error',
			'ZIPARCHIVE::ER_MEMORY' => 'Memory allocation failure',
			'ZIPARCHIVE::ER_CHANGED' => 'Entry has been changed',
			'ZIPARCHIVE::ER_COMPNOTSUPP' => 'Compression method not supported.',
			'ZIPARCHIVE::ER_EOF' => 'Premature EOF',
			'ZIPARCHIVE::ER_INVAL' => 'Invalid argument',
			'ZIPARCHIVE::ER_NOZIP' => 'Not a zip archive',
			'ZIPARCHIVE::ER_INTERNAL' => 'Internal error',
			'ZIPARCHIVE::ER_INCONS' => 'Zip archive inconsistent',
			'ZIPARCHIVE::ER_REMOVE' => 'Can\'t remove file',
			'ZIPARCHIVE::ER_DELETED' => 'Entry has been deleted',
		);

		foreach ($zipFileFunctionsErrors as $constName => $errorMessage) {
			if (defined($constName) and constant($constName) === $errno) {
				return $errorMessage;
			}
		}
		return 'Unknown Zip Error';
	}
?>
