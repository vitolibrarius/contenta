<?php

interface LoggerInterface {
	public function info($message, $context = null, $context_id = null);
	public function warn($message, $context = null, $context_id = null);
	public function error($message, $context = null, $context_id = null);
	public function fatal($message, $context = null, $context_id = null);
}

class LogFileDoesNotExistException extends Exception {}
class LogFileOpenErrorException extends Exception {}
class LogFileNotOpenException extends Exception {}
class LogFileAlreadyExistsException extends Exception {}
class FileCreationErrorException extends Exception {}
class NotAStringException extends Exception {}
class NotAIntegerException extends Exception {}
class InvalidMessageTypeException extends Exception {}

abstract class Logger implements LoggerInterface {
	const INFO = 'info';
	const WARNING = 'warning';
	const ERROR = 'error';
	const FATAL = 'fatal';

	private $currentTrace;
	private $currentTraceId;

	private static $catastrophicFail = false;
	public static function catastrophicFailure() {
		Logger::$catastrophicFail = true;
	}

	final public static function instance()
	{
		static $instance = null;
		if ( Logger::$catastrophicFail ) {
			$instance = new loggers\PrintLogger();
		}
		else if ( null == $instance ) {
			try {
				$loggerClass = 'loggers\\' . Config::Get("Logging/type", "Print") . 'Logger';
				$instance = new $loggerClass();
			}
			catch (Exception $e) {
				Logger::$catastrophicFail = true;
				$instance = new loggers\PrintLogger();
				$instance->error( get_class($e) . " thrown within the Log Constructor. Message: " . $e->getMessage() . " on line " . $e->getLine() );
				$instance->error( "Failed to construct logger for '" . Config::Get("Logging/type") . "'" );
			}
		}

		return $instance;
	}

	final public static function resetInstance()
	{
		$instance = null;
		$catastrophicFail = false;
	}


	final public static function logToFile($message, $context = null, $context_id = null) {
		$log = new FileLogger();
		return $log->error( $message, $context, $context_id );
	}

	final public static function logInfo($message, $context = null, $context_id = null) {
		return Logger::instance()->info( $message, $context, $context_id );
	}

	final public static function logWarning($message, $context = null, $context_id = null) {
		return Logger::instance()->warn( $message, $context, $context_id );
	}

	final public static function logError($message, $context = null, $context_id = null) {
		return Logger::instance()->error( $message, $context, $context_id );
	}

	final public static function logSQLError( $clazz = 'Model', $method = 'unknown', $pdocode, $pdoError, $sql, $params = null)
	{
		$msg = 'PDO Error(' . $pdocode . ') ' . $pdoError . ' for [' . $sql . '] ' . (isset($params) ? var_export($params, true) : 'No Parameters');
		return Logger::instance()->error( $msg, $clazz, $method );
	}

	final public static function logException($exception) {
		if ( is_a($exception, 'Exception') ) {
			return Logger::instance()->error(
				get_class($exception)." thrown. Message: " . $exception->getMessage() . " Backtrace " . $exception->getTraceAsString(),
				"File",
				$exception->getFile() . ":" . $exception->getLine()
			);
		}
		else {
			return Logger::instance()->error(
				get_class($exception)." thrown? Message: " . var_export($exception, true),
				"File",
				null
			);

		}
	}

	final public static function logFatal($message, $context = null, $context_id = null) {
		return Logger::instance()->fatal( $message, $context, $context_id );
	}

	private function __clone()
	{
	}

	private function __wakeup()
	{
	}

	public function __destruct()
	{
	}

	protected function __construct()
	{
	}

	public function setTrace($newContext, $newId)
	{
		$this->currentTrace = $newContext;
		$this->currentTraceId = $newId;
	}

	public function clearTrace()
	{
		$this->currentTrace = null;
		$this->currentTraceId = null;
	}

	abstract protected function _doLog($level = Logger::INFO, $message, $trace = null, $traceId = null, $context = null, $context_id = null);

	public function info($message, $context = null, $context_id = null) {
		try {
			$success = $this->_doLog(Logger::INFO, $message, $this->currentTrace, $this->currentTraceId, $context , $context_id);
		}
		catch (Exception $e) {
			Logger::$catastrophicFail = true;
			$plog = new PrintLogger();
			$plog->_doLog(Logger::INFO, $message, $this->currentTrace, $this->currentTraceId, $context , $context_id);
			$plog->_doLog(Logger::ERROR, get_class($e)." thrown. Message: ".$e->getMessage(),
				$this->currentTrace, $this->currentTraceId, "Line", $e->getLine());
		}
		return $success;
	}

	public function warn($message, $context = null, $context_id = null) {
		try {
			$success = $this->_doLog(Logger::WARNING, $message, $this->currentTrace, $this->currentTraceId, $context , $context_id);
		}
		catch (Exception $e) {
			Logger::$catastrophicFail = true;
			$plog = new PrintLogger();
			$plog->_doLog(Logger::WARNING, $message, $this->currentTrace, $this->currentTraceId, $context , $context_id);
			$plog->_doLog(Logger::ERROR, get_class($e)." thrown. Message: ".$e->getMessage(),
				$this->currentTrace, $this->currentTraceId, "Line", $e->getLine());
		}
		return $success;
	}

	public function error($message, $context = null, $context_id = null) {
		try {
			$success = $this->_doLog(Logger::ERROR, $message, $this->currentTrace, $this->currentTraceId, $context , $context_id);
		}
		catch (Exception $e) {
			Logger::$catastrophicFail = true;
			$plog = new PrintLogger();
			$plog->_doLog(Logger::ERROR, $message, $this->currentTrace, $this->currentTraceId, $context , $context_id);
			$plog->_doLog(Logger::ERROR, get_class($e)." thrown. Message: ".$e->getMessage(),
				$this->currentTrace, $this->currentTraceId, "Line", $e->getLine());
		}
		return $success;
	}

	public function fatal($message, $context = null, $context_id = null) {
		try {
			$success = $this->_doLog(Logger::FATAL, $message, $this->currentTrace, $this->currentTraceId, $context , $context_id);
		}
		catch (Exception $e) {
			Logger::$catastrophicFail = true;
			$plog = new PrintLogger();
			$plog->_doLog(Logger::FATAL, $message, $this->currentTrace, $this->currentTraceId, $context , $context_id);
			$plog->_doLog(Logger::ERROR, get_class($e)." thrown. Message: ".$e->getMessage(),
				$this->currentTrace, $this->currentTraceId, "Line", $e->getLine());
		}
		return $success;
	}
}
