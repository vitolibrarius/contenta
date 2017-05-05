<?php

interface LoggerInterface {
	public function info($message, $context = null, $context_id = null);
	public function warn($message, $context = null, $context_id = null);
	public function error($message, $context = null, $context_id = null);
	public function fatal($message, $context = null, $context_id = null);
}

class LogFileDoesNotExistException extends \Exception {}
class LogFileOpenErrorException extends \Exception {}
class LogFileNotOpenException extends \Exception {}
class LogFileAlreadyExistsException extends \Exception {}
class FileCreationErrorException extends \Exception {}
class NotAStringException extends \Exception {}
class NotAIntegerException extends \Exception {}
class InvalidMessageTypeException extends \Exception {}

abstract class Logger implements LoggerInterface {
	const INFO = 'info';
	const WARNING = 'warning';
	const ERROR = 'error';
	const FATAL = 'fatal';

	private $currentTrace;
	private $currentTraceId;

	private static $instance = null;

	private static $catastrophicFail = false;
	public static function catastrophicFailure() {
		\Logger::$catastrophicFail = true;
	}

	final public static function instance()
	{
		if ( \Logger::$catastrophicFail ) {
			\Logger::$instance = new loggers\PrintLogger();
		}
		else if ( null == \Logger::$instance ) {
			try {
				$loggerClass = 'loggers\\' . Config::Get("Logging/type", "Print") . 'Logger';
				\Logger::$instance = new $loggerClass();
			}
			catch (Exception $e) {
				\Logger::$catastrophicFail = true;
				\Logger::$instance = new loggers\PrintLogger();
				\Logger::$instance->logException( $e );
				\Logger::$instance->error( "Failed to construct logger for '" . Config::Get("Logging/type") . "'" );
			}
		}

		return \Logger::$instance;
	}

	final public static function resetInstance()
	{
		\Logger::$instance = null;
		$catastrophicFail = false;
	}


	final public static function logToFile($message, $context = null, $context_id = null) {
		$log = new loggers\FileLogger();
		return $log->error( $message, $context, $context_id );
	}

	final public static function logInfo($message, $context = null, $context_id = null) {
		return \Logger::instance()->info( $message, $context, $context_id );
	}

	final public static function logWarning($message, $context = null, $context_id = null) {
		return \Logger::instance()->warn( $message, $context, $context_id );
	}

	final public static function logError($message, $context = null, $context_id = null) {
		return \Logger::instance()->error( $message, $context, $context_id );
	}

	final public static function logSQL( $clazz = 'Model', $method = 'unknown', $sql = '', $params = null)
	{
		$msg = $sql . (isset($params) ? ' ' . var_export($params, true) : '');
		return \Logger::instance()->info( $msg, $clazz, $method );
	}

	final public static function logSQLError( $clazz = 'Model', $method = 'unknown', $pdocode, $pdoError, $sql, $params = null)
	{
		$msg = 'PDO Error(' . $pdocode . ') ' . $pdoError . ' for [' . $sql . '] ' . (isset($params) ? var_export($params, true) : 'No Parameters');
		return \Logger::instance()->error( $msg, $clazz, $method );
	}

	final public static function exceptionMessage($exception) {
		if ( is_a($exception, '\Exception') ) {
			// these are our templates
			$traceline = "#%s %s(%s): %s(%s)";
			$msg = "'%s' with message '%s' in %s:%s\nStack trace:\n%s";

			// alter your trace as you please, here
			$trace = $exception->getTrace();
			foreach ($trace as $key => $stackPoint) {
				// I'm converting arguments to their type
				// (prevents passwords from ever getting logged as anything other than 'string')
				if ( isset($trace[$key]['args']) ) {
					$trace[$key]['args'] = array_map('gettype', $trace[$key]['args']);
				}
				else {
					$trace[$key]['args'] = array();
				}
			}

			// build your tracelines
			$result = array();
			foreach ($trace as $key => $stackPoint) {
				$result[] = sprintf(
					$traceline,
					$key,
					(isset($stackPoint['file']) ? shortendPath($stackPoint['file'], 3) : ''),
					(isset($stackPoint['line']) ? $stackPoint['line'] : ''),
					(isset($stackPoint['function']) ? $stackPoint['function'] : ''),
					implode(', ', $stackPoint['args'])
				);
			}

			// write tracelines into main template
			$msg = sprintf(
				$msg,
				get_class($exception),
				$exception->getMessage(),
				shortendPath($exception->getFile(), 3),
				$exception->getLine(),
				implode("\n", $result)
			);
			return array($msg, shortendPath($exception->getFile(), 3), $exception->getLine());
		}

		return array(get_class($exception)." thrown? Message: " . var_export($exception, true), "File", null);
	}

	final public static function logException($exception) {
		list($message, $file, $line) = Logger::exceptionMessage( $exception );
		return \Logger::instance()->error( $message, $file, $line );
	}

	final public static function logFatal($message, $context = null, $context_id = null) {
		return \Logger::instance()->fatal( $message, $context, $context_id );
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

	abstract protected function _doLog($level = \Logger::INFO, $message, $trace = null, $traceId = null, $context = null, $context_id = null);

	public function info($message, $context = null, $context_id = null) {
		try {
			$this->_doLog(\Logger::INFO, $message, $this->currentTrace, $this->currentTraceId, $context , $context_id);
		}
		catch (Exception $e) {
			\Logger::$catastrophicFail = true;
			$plog = new \loggers\PrintLogger();
			$plog->_doLog(\Logger::INFO, (string)$message, $this->currentTrace, $this->currentTraceId, $context , $context_id);

			list($message, $file, $line) = Logger::exceptionMessage( $e );
			$plog->_doLog(\Logger::ERROR, get_class($e)." thrown. Message: ".$e->getMessage(),
				$this->currentTrace, $this->currentTraceId, $file, $line);
		}
	}

	public function warn($message, $context = null, $context_id = null) {
		try {
			$this->_doLog(\Logger::WARNING, $message, $this->currentTrace, $this->currentTraceId, $context , $context_id);
		}
		catch (Exception $e) {
			\Logger::$catastrophicFail = true;
			$plog = new \loggers\PrintLogger();
			$plog->_doLog(\Logger::WARNING, $message, $this->currentTrace, $this->currentTraceId, $context , $context_id);
			$plog->_doLog(\Logger::ERROR, get_class($e)." thrown. Message: ".$e->getMessage(),
				$this->currentTrace, $this->currentTraceId, "Line", $e->getLine());
		}
	}

	public function error($message, $context = null, $context_id = null) {
		try {
			$this->_doLog(\Logger::ERROR, $message, $this->currentTrace, $this->currentTraceId, $context , $context_id);
		}
		catch (Exception $e) {
			\Logger::$catastrophicFail = true;
			$plog = new \loggers\PrintLogger();
			$plog->_doLog(\Logger::ERROR, $message, $this->currentTrace, $this->currentTraceId, $context , $context_id);

			list($message, $file, $line) = Logger::exceptionMessage( $e );
				$trace = $e->getTrace();
				foreach($trace as $item) {
					$backtrace .= "\n\t" . (isset($item['file']) ? shortendPath($item['file'], 3) : '<unknown file>')
						. ' ' . (isset($item['line']) ? $item['line'] : '<unknown line>')
						. ' calling ' . (isset($item['function']) ? $item['function'] : '<unknown function>') . '()';
				}
			$plog->_doLog(\Logger::ERROR, get_class($e)." thrown. Message: ".$e->getMessage() . $backtrace,
				$this->currentTrace, $this->currentTraceId, $file, $line);
		}
	}

	public function fatal($message, $context = null, $context_id = null) {
		try {
			$this->_doLog(\Logger::FATAL, $message, $this->currentTrace, $this->currentTraceId, $context , $context_id);
		}
		catch (Exception $e) {
			\Logger::$catastrophicFail = true;
			$plog = new \loggers\PrintLogger();
			$plog->_doLog(\Logger::FATAL, $message, $this->currentTrace, $this->currentTraceId, $context , $context_id);
			$plog->_doLog(\Logger::ERROR, get_class($e)." thrown. Message: ".$e->getMessage(),
				$this->currentTrace, $this->currentTraceId, "Line", $e->getLine());
		}
	}
}
