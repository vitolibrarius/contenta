<?php

namespace utilities;

use \Logger as Logger;
use \Cache as Cache;
use utilities\ShellCommand as ShellCommand;

class Singleton
{
    private static $instances = array();

    protected function __construct() {}
    protected function __clone() {}

    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }

    public static function Instance()
    {
        $cls = get_called_class();
        if (isset(self::$instances[$cls]) === false) {
            self::$instances[$cls] = new static;
        }
        return self::$instances[$cls];
    }

	final public static function ResetInstance()
	{
        $cls = get_called_class();
        if (isset(self::$instances[$cls]) === true) {
            unset(self::$instances[$cls]);
        }
	}
}
