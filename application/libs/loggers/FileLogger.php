<?php

namespace loggers;

class FileLogger extends \Logger
{
	public function __construct()
	{
		$base_path = \Config::GetPath("Logging/path", null);
		$prefix = \Config::Get("Logging/prefix", "log_");
		if ( strlen($base_path) == 0 ) {
			throw new \LogFileOpenErrorException('No path set in configuration for logging');
		}
		makeRequiredDirectory($base_path, 'Logging directory');
		$this->logfile = appendPath( $base_path, $prefix.date('Y-m-d').'.txt');
	}

	public function __destruct()
	{
	}

	private function writeToLogFile($message)
	{
		$logfilehandle = fopen($this->logfile,"a");
		if ( ! $logfilehandle )
			throw new \LogFileOpenErrorException('Could not open Logfile in append-mode (' . $this->logfile . ')');

		if (flock($logfilehandle,LOCK_EX)) {
			fwrite($logfilehandle, $message."\n");
			fflush($logfilehandle);
			flock($logfilehandle,LOCK_UN);
		}
		else {
			throw new \LogFileOpenErrorException('Could not open Logfile in exclusive (' . $this->logfile . ')');
		}

		fclose($logfilehandle);
	}

	private function getTime()
	{
		return date("d.m.Y - H:i:s");
	}

	public function _doLog($level = \Logger::INFO, $message, $trace = null, $traceId = null, $context = null, $context_id = null)
	{
		if ( is_string($message) == false)
			throw new \NotAStringException('$message is not a string');

		if ($level != \Logger::INFO && $level != \Logger::WARNING && $level != \Logger::ERROR && $level != \Logger::FATAL)
			throw new \InvalidMessageTypeException('Wrong $messagetype given');

		$this->writeToLogFile(
			"[" . $this->getTime() . "] "
			. $level
			. (isset($trace) ? ' |' . $trace . (isset($traceId) ? ':' . $traceId : '') . '|' : '')
			. (isset($context) ? ' |' . $context . (isset($context_id) ? ':' . $context_id : '') . '|' : '')
			. " - " . $message
		);
		return true;
	}
}

