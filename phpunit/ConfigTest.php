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
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	Get
	 * 			T_FUNCTION T_STATIC T_PUBLIC Get ( $key, $default = '')
	 * @todo	Implement testGet().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testGet()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
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
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	AppName
	 * 			T_FUNCTION T_STATIC T_PUBLIC T_FINAL AppName ( )
	 * @todo	Implement testAppName().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testAppName()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	Web
	 * 			T_FUNCTION T_STATIC T_PUBLIC T_FINAL Web ( )
	 * @todo	Implement testWeb().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testWeb()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	Url
	 * 			T_FUNCTION T_STATIC T_PUBLIC T_FINAL Url ( $path = null)
	 * @todo	Implement testUrl().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testUrl()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	initialize
	 * 			T_FUNCTION T_PRIVATE initialize ( )
	 * @todo	Implement testInitialize().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testInitialize()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	dumpConfig
	 * 			T_FUNCTION T_PUBLIC dumpConfig ( )
	 * @todo	Implement testDumpConfig().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testDumpConfig()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	getValue
	 * 			T_FUNCTION T_PUBLIC getValue ( $key, $default = '')
	 * @todo	Implement testGetValue().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testGetValue()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	setValue
	 * 			T_FUNCTION T_PUBLIC setValue ( $key, $value)
	 * @todo	Implement testSetValue().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testSetValue()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	getIntegerValue
	 * 			T_FUNCTION T_PUBLIC getIntegerValue ( $key, $default)
	 * @todo	Implement testGetIntegerValue().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testGetIntegerValue()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	absolutePathValue
	 * 			T_FUNCTION T_PUBLIC absolutePathValue ( $key, $default = null)
	 * @todo	Implement testAbsolutePathValue().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testAbsolutePathValue()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	repositoryDirectory
	 * 			T_FUNCTION T_PUBLIC repositoryDirectory ( )
	 * @todo	Implement testRepositoryDirectory().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testRepositoryDirectory()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	mediaDirectory
	 * 			T_FUNCTION T_PUBLIC mediaDirectory ( )
	 * @todo	Implement testMediaDirectory().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testMediaDirectory()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	cacheDirectory
	 * 			T_FUNCTION T_PUBLIC cacheDirectory ( )
	 * @todo	Implement testCacheDirectory().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testCacheDirectory()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	processingDirectory
	 * 			T_FUNCTION T_PUBLIC processingDirectory ( )
	 * @todo	Implement testProcessingDirectory().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testProcessingDirectory()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	loggingDirectory
	 * 			T_FUNCTION T_PUBLIC loggingDirectory ( )
	 * @todo	Implement testLoggingDirectory().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:35:50.
	 */
	public function testLoggingDirectory()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}


/* {functions} */
}
