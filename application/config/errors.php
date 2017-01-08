<?php

// guard to ensure basic configuration is loaded
defined('SYSTEM_PATH') || exit("SYSTEM_PATH not found.");

	function printMemory( $file, $line, $cmt = null )
	{
		$unit = array('b','kb','mb','gb','tb','pb');
		$s = memory_get_usage(true);
		$p = memory_get_peak_usage(true);
    	$ms = @round($s/pow(1024,($i=floor(log($s,1024)))),2).' '.$unit[$i];
    	$mp = @round($p/pow(1024,($i=floor(log($p,1024)))),2).' '.$unit[$i];

		print 'Memory at ' . $file . ':' . $line . ' (' . $ms . ' / ' . $mp . ')' . ((isset($cmt) && strlen($cmt) >0) ? ' -- ' . $cmt : '') .PHP_EOL;
	}

	function printBacktrace($cmt = null)
	{
		print backtraceString($cmt);
	}

	function backtraceString( $cmt = null )
	{
	    $trace = debug_backtrace();
	    array_shift($trace);

		$msg = ((isset($cmt) && strlen($cmt) >0) ? $cmt.PHP_EOL : '');
        foreach($trace as $item) {
            $msg .= "\t" . (isset($item['file']) ? shortendPath($item['file'], 3) : '<unknown file>')
            	. ' ' . (isset($item['line']) ? $item['line'] : '<unknown line>')
            	. ' calling ' . (isset($item['function']) ? $item['function'] : '<unknown function>') . '()'
            	. PHP_EOL;
        }
        return $msg;
	}

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
			if ( $error !== NULL && $error["type"] == E_ERROR) {
				$errName = FriendlyErrorType($error["type"]);
				$backtrace = "(".$errName.") " . $error["message"];

				$trace = debug_backtrace();
				foreach($trace as $item) {
					$backtrace .= "\n\t" . (isset($item['file']) ? shortendPath($item['file'], 3) : '<unknown file>')
						. ' ' . (isset($item['line']) ? $item['line'] : '<unknown line>')
						. ' calling ' . (isset($item['function']) ? $item['function'] : '<unknown function>') . '()';
				}

				\Logger::instance()->fatal( $backtrace, shortendPath($error["file"], 3), $error["line"]);
			}
		}
		catch (Exception $e) {
			print get_class($e)." thrown within the shutdown handler. Message: ".$e->getMessage()." on line ".$e->getLine();
		}
	}

	# Registering shutdown function
	register_shutdown_function('shutdownHandler');

	function customError($errno, $errstr, $errfile, $errline) {
		$errName = FriendlyErrorType($errno);
		if ( $errno == E_ERROR ) {
			$trace = debug_backtrace();
			array_shift($trace);

			$backtrace = "(".$errName.") " . $errstr;
			foreach($trace as $item) {
				$backtrace .= "\n\t" . (isset($item['file']) ? shortendPath($item['file'], 3) : '<unknown file>')
					. ' ' . (isset($item['line']) ? $item['line'] : '<unknown line>')
					. ' calling ' . (isset($item['function']) ? $item['function'] : '<unknown function>') . '()';
			}

			//echo "$backtrace, $errfile, $errline";
			\Logger::LogError( $backtrace, shortendPath($errfile), $errline );
		}
		else {
			\Logger::LogWarning( "(".$errName.") " . $errstr, shortendPath($errfile), $errline );
		}

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
