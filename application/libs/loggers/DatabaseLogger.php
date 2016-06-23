<?php

namespace loggers;

use \Model as Model;
use \Logger as Logger;
use \model\media\logs\Log as Log;
use \model\media\logs\Log_Level as Log_Level;

class DatabaseLogger extends Logger
{
	private $model = null;

	public function __construct()
	{
		$this->model = Model::Named('Log');
		if ( $this->model == false ) {
			throw new \LogFileNotOpenException('Log Model is not opened.');
		}
	}

	public function _doLog($level = Logger::WARNING, $message, $trace = null, $traceId = null, $context = null, $contextId = null)
	{
		if ( is_string($message) == false)
			throw new \NotAStringException('$message is not a string');

		$success = true;
		if ($level != Logger::INFO && $level != Logger::WARNING && $level != Logger::ERROR && $level != Logger::FATAL)
			throw new \InvalidMessageTypeException('Wrong $level given ' . $level);

		try {
			$obj = $this->model->base_create( $level, $trace, $traceId, $context, $contextId, $message, null);
			if ( $obj == false ) {
				$success = false;
				Logger::catastrophicFailure();
				Logger::instance()->_doLog($level, $message, $trace, $traceId, $context, $context_id);
			}
		}
		catch (\Exception $e) {
			$success = false;
			Logger::catastrophicFailure();
			print get_class($e)." thrown within the exception handler. Message: ".$e->getMessage()." on line ".$e->getLine();
		}
		return ($success != false);
	}
}

?>
