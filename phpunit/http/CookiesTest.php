<?php
/**
 * DO NOT EDIT CUSTOM MARKERS
 * Generated from Class.tpl by PhpTestClassGenerator.php on 2016-05-31 09:05:55.
 * https://phpunit.de/manual/current/en/appendixes.assertions.html
 */

namespace http;


use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;
/* {useStatements} */

class CookiesTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
    }

    public static function tearDownAfterClass()
    {
    }

    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

/*	 Test functions */

	/**
	 * @covers	init
	 * 			T_FUNCTION T_STATIC T_PUBLIC T_PRIVATE init ( interfaces GlobalAdapter $adapter = null)
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-31 09:05:55.
	 */
	public function testInit()
	{
	}

	/**
	 * @covers	set
	 * 			T_FUNCTION T_STATIC T_PUBLIC set ( $key, $value)
	 * @todo	Implement testSet().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-31 09:05:55.
	 */
	public function testSet()
	{
		Cookies::init( new \http\GlobalMemoryAdapter() );
		Cookies::set( "TestKey", "the value" );

		$this->assertNotEmpty( Cookies::get("TestKey", null), "Failed to get value after setting it" );
		$this->assertEquals( "the value", Cookies::get("TestKey", null), "Failed to get value after setting it" );
	}

	/**
	 * @covers	get
	 * 			T_FUNCTION T_STATIC T_PUBLIC get ( $key, $default = null)
	 * @todo	Implement testGet().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-31 09:05:55.
	 */
	public function testGet()
	{
		Cookies::init( new \http\GlobalMemoryAdapter() );
		Cookies::set( "TestKey", "the value" );

		$this->assertNotEmpty( Cookies::get("TestKey", null), "Failed to get value after setting it" );
		$this->assertEquals( "the value", Cookies::get("TestKey", null), "Failed to get value after setting it" );
	}


/*  Test functions */

	/**
	 * @covers	deleteCookie
	 * 			T_FUNCTION T_STATIC T_PUBLIC deleteCookie ( )
	 * @todo	Implement testDeleteCookie().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-31 10:31:41.
	 */
	public function testDeleteCookie()
	{
		Cookies::init( new \http\GlobalMemoryAdapter() );
		Cookies::set( "TestKey", "the value" );

		$this->assertNotEmpty( Cookies::get("TestKey", null), "Failed to get value after setting it" );
		$this->assertEquals( "the value", Cookies::get("TestKey", null), "Failed to get value after setting it" );

		Cookies::deleteCookie( "TestKey" );
		$this->assertEmpty( Cookies::get("TestKey", null), "Failed to get value after setting it" );
	}

/* {functions} */
}
