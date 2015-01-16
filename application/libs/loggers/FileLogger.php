<?php

namespace loggers;

class FileLogger extends \Logger
{
	private $logfilehandle = null;

	public function __construct()
	{
		if ($this->logfilehandle == null) {
			$base_path = $Config::GetPath("Logging/path", null);
			if ( strlen($base_path) == 0 ) {
				throw new \LogFileOpenErrorException('No path set in configuration for logging');
			}
			makeRequiredDirectory($base_path, 'Logging directory');

			$logfile = appendPath( $base_path, 'log_'.date('Y-m-d').'.txt');
			$this->openLogFile($logfile);
		}
	}

	public function __destruct()
	{
		$this->closeLogFile();
	}

	public function openLogFile($logfile)
	{
		//close old logfile if opened;
		$this->closeLogFile();
		$this->logfilehandle = @fopen($logfile,"a");
		if ( ! $this->logfilehandle )
			throw new \LogFileOpenErrorException('Could not open Logfile in append-mode');
	}

	private function writeToLogFile($message)
	{
		flock($this->logfilehandle,LOCK_EX);
		fwrite($this->logfilehandle,$message."\n");
		flock($this->logfilehandle,LOCK_UN);
	}

	public function closeLogFile() {
		if ($this->logfilehandle != null) {
			fclose($this->logfilehandle);
			$this->logfilehandle = null;
		}
	}

	private function getTime()
	{
		return date("d.m.Y - H:i:s");
	}

	public function _doLog($level = \Logger::INFO, $message, $trace = null, $traceId = null, $context = null, $context_id = null)
	{
		if ($this->logfilehandle == null)
			throw new \LogFileNotOpenException('Logfile is not opened.');

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

