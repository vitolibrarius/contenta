<?php

namespace utilities;

class StopWatch
{
	private static $startTimes = array();

	public static function start($timerName = 'default')
	{
		self::$startTimes[$timerName] = microtime(true);
		return $timerName;
	}

	public static function elapsed($timerName = 'default')
	{
		return microtime(true) - self::$startTimes[$timerName];
	}

	public static function clear($timerName = 'default')
	{
		if ( isset( self::$startTimes[$timerName])) {
			unset( self::$startTimes[$timerName] );
		}
	}

	public static function end($timerName = 'default')
	{
		$value = 0.0;
		if ( isset( self::$startTimes[$timerName])) {
			$value = microtime(true) - self::$startTimes[$timerName];
			unset( self::$startTimes[$timerName] );
		}
		return $value;
	}
}
