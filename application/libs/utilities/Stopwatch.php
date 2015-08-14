<?php

namespace utilities;

class StopWatch
{
	private static $startTimes = array();

	public static function start($timerName = 'default')
	{
		self::$startTimes[$timerName] = microtime(true);
	}

	public static function elapsed($timerName = 'default')
	{
		return microtime(true) - self::$startTimes[$timerName];
	}
}
