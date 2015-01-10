<?php

	function exceptionHandler($exception)
	{
		try {
			// these are our templates
			$traceline = "#%s %s(%s): %s(%s)";
			$msg = "Uncaught exception '%s' with message '%s' in %s:%s\nStack trace:\n%s";

			// alter your trace as you please, here
			$trace = $exception->getTrace();
			foreach ($trace as $key => $stackPoint) {
				// I'm converting arguments to their type
				// (prevents passwords from ever getting logged as anything other than 'string')
				$trace[$key]['args'] = array_map('gettype', $trace[$key]['args']);
			}

			// build your tracelines
			$result = array();
			foreach ($trace as $key => $stackPoint) {
				$result[] = sprintf(
					$traceline,
					$key,
					$stackPoint['file'],
					$stackPoint['line'],
					$stackPoint['function'],
					implode(', ', $stackPoint['args'])
				);
			}

			// write tracelines into main template
			$msg = sprintf(
				$msg,
				get_class($exception),
				$exception->getMessage(),
				$exception->getFile(),
				$exception->getLine(),
				implode("\n", $result)
			);
			Logger::instance()->fatal($msg, basename($exception->getFile()), $exception->getLine());

			// log or echo as you please
			error_log('Fatal Error has occured ' . $msg);
		}
		catch (Exception $e) {
			print get_class($e)." thrown within the exception handler. Message: ".$e->getMessage()." on line ".$e->getLine();
		}
	}

	set_exception_handler('exceptionHandler');

	function shutdown($msg) 
	{
		try {
			Logger::instance()->fatal($msg);
		}
		catch (Exception $e) {
			print get_class($e)." thrown within the shutdown action. Message: ".$e->getMessage()." on line ".$e->getLine();
		}
		die($msg);
	}
	
	function shutdownHandler() 
	{
		try {
			# Getting last error
			$error = error_get_last();
			# Checking if last error is a fatal error
			if (($error['type'] === E_ERROR) || ($error['type'] === E_USER_ERROR) || ($error['type'] === E_USER_NOTICE)) {
				# Here we handle the error, displaying HTML, logging, ...
				Logger::instance()->fatal(
					$error['type']. " - " . $error['message'],
					$error['file'],
					$error['line']
				);
			}
		}
		catch (Exception $e) {
			print get_class($e)." thrown within the shutdown handler. Message: ".$e->getMessage()." on line ".$e->getLine();
		}
	}
	 
	# Registering shutdown function
	register_shutdown_function('shutdownHandler');

//	function customError($errno, $errstr) {
//		Logger::logToFile( $errstr, $errno );
//	}
//
//	//set error handler
//	set_error_handler("customError");

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