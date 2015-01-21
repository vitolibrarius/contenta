<?php

// guard to ensure basic configuration is loaded
defined('APPLICATION_PATH') || exit("APPLICATION_PATH not found.");

class AutoLoader {

    public static $loader;

    public static function init()
    {
        if (self::$loader == NULL)
            self::$loader = new self();

        return self::$loader;
    }

    public function __construct()
    {
        spl_autoload_register(array($this,'library'));
        spl_autoload_register(array($this,'model'));
        spl_autoload_register(array($this,'processor'));
        spl_autoload_register(array($this,'controller'));
    }

    public function library($class)
    {
        set_include_path( APPLICATION_PATH . DIRECTORY_SEPARATOR . 'libs/');
        spl_autoload_extensions('.php');
        spl_autoload($class);
    }

    public function controller($class)
    {
    	$className = str_replace( 'controller', '', $class );
		$className = ltrim($className, '\\');
		$parts = explode('\\', $className);
		$className =  implode(DIRECTORY_SEPARATOR, $parts);
    	echo "		 " .APPLICATION_PATH . DIRECTORY_SEPARATOR . 'controller/'. $className .'.controller.php' . PHP_EOL;

        set_include_path( APPLICATION_PATH . DIRECTORY_SEPARATOR . 'controller/' );
        spl_autoload_extensions('.controller.php');
        spl_autoload($className);
    }

    public function model($class)
    {
    	$className = str_replace( 'model', '', $class );
		$className = ltrim($className, '\\');
		$parts = explode('\\', $className);
		$className =  implode(DIRECTORY_SEPARATOR, $parts);
    	echo "		 " .APPLICATION_PATH . DIRECTORY_SEPARATOR . 'model/'. $className .'.model.php' . PHP_EOL;
    	echo "		 " .APPLICATION_PATH . DIRECTORY_SEPARATOR . 'model/'. $className .'.data.php' . PHP_EOL;

        set_include_path( APPLICATION_PATH . DIRECTORY_SEPARATOR . 'model/' );
        spl_autoload_extensions('.model.php,.data.php');
        spl_autoload($className);
    }

    public function processor($class)
    {
    	$className = str_replace( 'processor', '', $class );
		$className = ltrim($className, '\\');
		$parts = explode('\\', $className);
		$className =  implode(DIRECTORY_SEPARATOR, $parts);
    	echo "		 " .APPLICATION_PATH . DIRECTORY_SEPARATOR . 'processor/'. $className .'.processor.php' . PHP_EOL;

        set_include_path( APPLICATION_PATH . DIRECTORY_SEPARATOR . 'processor/' );
        spl_autoload_extensions('.processor.php');
        spl_autoload($className);
    }
}

AutoLoader::init();

