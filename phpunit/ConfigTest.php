<?php
/**
 * DO NOT EDIT CUSTOM MARKERS
 * Generated from Class.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
 */



use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;
/* {useStatements} */

class ConfigTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

/*	 Test functions */

	/**
	 * @covers	instance
	 * 			T_FUNCTION T_STATIC T_PUBLIC T_FINAL instance ( )
	 * @todo	Implement testInstance().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testInstance()
	{
		$a = Config::instance();
		$b = COnfig::instance();
		$this->assertEquals( $a, $b );
	}

	/**
	 * @covers	Get
	 * 			T_FUNCTION T_STATIC T_PUBLIC Get ( $key, $default = '')
	 * @todo	Implement testGet().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testGet()
	{
		$value = Config::Get("NoMatchNoDefault", null);
		$this->assertNull( $value );

		$value = Config::Get("NoMatchNoDefault", "default_value");
		$this->assertEquals( "default_value",  $value );

		$expected = array(
			"path" => "/tmp/ContentaTest/phpunit",
			"cache" => "cache",
			"processing" => "processing"
		);
		$value = Config::Get("Repository", null);
		$this->assertEquals( $expected,  $value );
	}

	/**
	 * @covers	GetPath
	 * 			T_FUNCTION T_STATIC T_PUBLIC GetPath ( $key, $default = null)
	 * @todo	Implement testGetPath().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testGetPath()
	{
		$path = Config::GetPath("NoMatchNoDefault");
		$this->assertEquals( "/tmp/ContentaTest/phpunit",  $path );

		$path = Config::GetPath("NoMatch", "this_is_default");
		$this->assertEquals( "/tmp/ContentaTest/phpunit/this_is_default",  $path );

		$path = Config::GetPath("Logging/path");
		$this->assertEquals( "/tmp/ContentaTest/phpunit/logs", $path );
	}

	/**
	 * @covers	GetRepository
	 * 			T_FUNCTION T_STATIC T_PUBLIC GetRepository ( )
	 * @todo	Implement testGetRepository().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testGetRepository()
	{
		$path = Config::GetRepository();
		$this->assertEquals( "/tmp/ContentaTest/phpunit", $path );
	}

	/**
	 * @covers	GetMedia
	 * 			T_FUNCTION T_STATIC T_PUBLIC GetMedia ( )
	 * @todo	Implement testGetMedia().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testGetMedia()
	{
		$path = Config::GetMedia();
		$this->assertEquals( "/tmp/ContentaTest/phpunit/media", $path );
	}

	/**
	 * @covers	GetCache
	 * 			T_FUNCTION T_STATIC T_PUBLIC GetCache ( )
	 * @todo	Implement testGetCache().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testGetCache()
	{
		$path = Config::GetCache();
		$this->assertEquals( "/tmp/ContentaTest/phpunit/cache", $path );
	}

	/**
	 * @covers	GetProcessing
	 * 			T_FUNCTION T_STATIC T_PUBLIC GetProcessing ( )
	 * @todo	Implement testGetProcessing().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testGetProcessing()
	{
		$path = Config::GetProcessing();
		$this->assertEquals( "/tmp/ContentaTest/phpunit/processing", $path );
	}

	/**
	 * @covers	GetLog
	 * 			T_FUNCTION T_STATIC T_PUBLIC GetLog ( $filename = null)
	 * @todo	Implement testGetLog().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testGetLog()
	{
		$path = Config::GetLog();
		$this->assertEquals( "/tmp/ContentaTest/phpunit/logs", $path );

		$path = Config::GetLog("Log_xxx_yyy");
		$this->assertEquals( "/tmp/ContentaTest/phpunit/logs/Log_xxx_yyy", $path );
	}

	/**
	 * @covers	GetInteger
	 * 			T_FUNCTION T_STATIC T_PUBLIC T_FINAL GetInteger ( $key, $default)
	 * @todo	Implement testGetInteger().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testGetInteger()
	{
		$minsize = Config::GetInteger( "UploadImport/MinSize", 5);
		$this->assertEquals( 5, $minsize );

		Config::instance()->setValue( "UploadImport/MinSize", 10 );
		$minsize = Config::GetInteger( "UploadImport/MinSize", 5);
		$this->assertEquals( 10, $minsize );
	}

	/**
	 * @covers	AppName
	 * 			T_FUNCTION T_STATIC T_PUBLIC T_FINAL AppName ( )
	 * @todo	Implement testAppName().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testAppName()
	{
		$AppName = Config::AppName();
		$this->assertEquals( "Contenta", $AppName );
	}

	/**
	 * @covers	Web
	 * 			T_FUNCTION T_STATIC T_PUBLIC T_FINAL Web ( )
	 * @todo	Implement testWeb().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testWeb()
	{
		$root = Config::Web();
		$this->assertEquals( "/contenta", $root );

		$root = Config::Web("Admin");
		$this->assertEquals( "/contenta/Admin", $root );

		$root = Config::Web( "Admin", "editSeries", 1234 );
		$this->assertEquals( "/contenta/Admin/editSeries/1234", $root );

		$root = Config::Web( array("Admin", "editSeries", 1234) );
		$this->assertEquals( "/contenta/Admin/editSeries/1234", $root );
	}

	/**
	 * @covers	Url
	 * 			T_FUNCTION T_STATIC T_PUBLIC T_FINAL Url ( $path = null)
	 * @todo	Implement testUrl().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testUrl()
	{
		$root = Config::Url();
        $this->assertRegExp("~https?://(\\w\\w*-?\\w+)\\.([\\w(\\w*-?\\w)+])+/~", $root);
	}

	/**
	 * @covers	initialize
	 * 			T_FUNCTION T_PRIVATE initialize ( )
	 * @todo	Implement testInitialize().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testInitialize()
	{
	}

	/**
	 * @covers	dumpConfig
	 * 			T_FUNCTION T_PUBLIC dumpConfig ( )
	 * @todo	Implement testDumpConfig().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testDumpConfig()
	{
		$configArray = Config::instance()->dumpConfig();
		$this->assertTrue( is_array( $configArray), "Not an array?" );
		$this->assertGreaterThanOrEqual( 5, count($configArray) );
		$this->assertArrayHasKey("Internet", $configArray);
		$this->assertArrayHasKey("Repository", $configArray);
		$this->assertArrayHasKey("Database", $configArray);
		$this->assertArrayHasKey("Logging", $configArray);
		$this->assertArrayHasKey("Debug", $configArray);
	}

	/**
	 * @covers	getValue
	 * 			T_FUNCTION T_PUBLIC getValue ( $key, $default = '')
	 * @todo	Implement testGetValue().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testGetValue()
	{
		$value = Config::instance()->getValue("NoMatchNoDefault", null);
		$this->assertNull( $value );

		$value = Config::instance()->getValue("NoMatchNoDefault", "default_value");
		$this->assertEquals( "default_value",  $value );

		$expected = array(
			"path" => "/tmp/ContentaTest/phpunit",
			"cache" => "cache",
			"processing" => "processing"
		);
		$value = Config::instance()->getValue("Repository", null);
		$this->assertEquals( $expected,  $value );
	}

	/**
	 * @covers	setValue
	 * 			T_FUNCTION T_PUBLIC setValue ( $key, $value)
	 * @todo	Implement testSetValue().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testSetValue()
	{
		Config::instance()->setValue( "UploadImport/MinSize", 10 );
		$minsize = Config::GetInteger( "UploadImport/MinSize", 5);
		$this->assertEquals( 10, $minsize );
	}

	/**
	 * @covers	getIntegerValue
	 * 			T_FUNCTION T_PUBLIC getIntegerValue ( $key, $default)
	 * @todo	Implement testGetIntegerValue().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testGetIntegerValue()
	{
		Config::instance()->setValue( "UploadImport/MinSize", 10 );
		$minsize = Config::instance()->getIntegerValue( "UploadImport/MinSize", 5);
		$this->assertEquals( 10, $minsize );
	}

	/**
	 * @covers	absolutePathValue
	 * 			T_FUNCTION T_PUBLIC absolutePathValue ( $key, $default = null)
	 * @todo	Implement testAbsolutePathValue().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testAbsolutePathValue()
	{
		$path = Config::instance()->absolutePathValue("Repository/processing", null);
		$this->assertEquals( "/tmp/ContentaTest/phpunit/processing", $path );
	}

	/**
	 * @covers	repositoryDirectory
	 * 			T_FUNCTION T_PUBLIC repositoryDirectory ( )
	 * @todo	Implement testRepositoryDirectory().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testRepositoryDirectory()
	{
		$path = Config::instance()->repositoryDirectory();
		$this->assertEquals( "/tmp/ContentaTest/phpunit", $path );
	}

	/**
	 * @covers	mediaDirectory
	 * 			T_FUNCTION T_PUBLIC mediaDirectory ( )
	 * @todo	Implement testMediaDirectory().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testMediaDirectory()
	{
		$path = Config::instance()->mediaDirectory();
		$this->assertEquals( "/tmp/ContentaTest/phpunit/media", $path );
	}

	/**
	 * @covers	cacheDirectory
	 * 			T_FUNCTION T_PUBLIC cacheDirectory ( )
	 * @todo	Implement testCacheDirectory().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testCacheDirectory()
	{
		$path = Config::instance()->cacheDirectory();
		$this->assertEquals( "/tmp/ContentaTest/phpunit/cache", $path );
	}

	/**
	 * @covers	processingDirectory
	 * 			T_FUNCTION T_PUBLIC processingDirectory ( )
	 * @todo	Implement testProcessingDirectory().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testProcessingDirectory()
	{
		$path = Config::instance()->processingDirectory();
		$this->assertEquals( "/tmp/ContentaTest/phpunit/processing", $path );
	}

	/**
	 * @covers	loggingDirectory
	 * 			T_FUNCTION T_PUBLIC loggingDirectory ( )
	 * @todo	Implement testLoggingDirectory().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testLoggingDirectory()
	{
		$path = Config::instance()->loggingDirectory();
		$this->assertEquals( "/tmp/ContentaTest/phpunit/logs", $path );
	}


/* {functions} */
}
