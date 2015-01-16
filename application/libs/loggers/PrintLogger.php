<?php

namespace loggers;

class PrintLogger extends \Logger
{
	public function __construct()
	{
	}


	public function _doLog($level = \Logger::INFO, $message, $trace = null, $traceId = null, $context = null, $context_id = null)
	{
		print "[" . date("d.m.Y - H:i:s") . "] "
			. $level
			. (isset($trace) ? ' |' . $trace . (isset($traceId) ? ':' . $traceId : '') . '|' : '')
			. (isset($context) ? ' |' . $context . (isset($context_id) ? ':' . $context_id : '') . '|' : '')
			. " - " . $message . PHP_EOL;
		return true;
	}

}
