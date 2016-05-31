<?php
/**
 * DO NOT EDIT CUSTOM MARKERS
 * Generated from Class.tpl by PhpTestClassGenerator.php on 2016-05-30 21:53:20.
 * https://phpunit.de/manual/current/en/appendixes.assertions.html
 */

namespace http;


use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;
/* {useStatements} */

class SessionTest extends PHPUnit_Framework_TestCase
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
	 * 			T_FUNCTION T_STATIC T_PUBLIC init ( SessionAdapter $adapter = null)
	 * @todo	Implement testInit().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-30 21:53:20.
	 */
	public function testInit()
	{
	}

	/**
	 * @covers	set
	 * 			T_FUNCTION T_STATIC T_PUBLIC set ( $key, $value)
	 * @todo	Implement testSet().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-30 21:53:20.
	 */
	public function testSet()
	{
		Session::init( new \http\GlobalMemoryAdapter() );
		Session::set( "TestKey", "the value" );

		$this->assertNotEmpty( Session::get("TestKey", null), "Failed to get value after setting it" );
		$this->assertEquals( "the value", Session::get("TestKey", null), "Failed to get value after setting it" );
	}

	/**
	 * @covers	get
	 * 			T_FUNCTION T_STATIC T_PUBLIC get ( $key, $default = null)
	 * @todo	Implement testGet().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-30 21:53:20.
	 */
	public function testGet()
	{
		Session::init( new \http\GlobalMemoryAdapter() );
		Session::set( "TestKey", "the value" );

		$this->assertNotEmpty( Session::get("TestKey", null), "Failed to get value after setting it" );
		$this->assertEquals( "the value", Session::get("TestKey", null), "Failed to get value after setting it" );
	}

	/**
	 * @covers	clearAllFeedback
	 * 			T_FUNCTION T_STATIC T_PUBLIC clearAllFeedback ( )
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-30 21:53:20.
	 */
	public function testClearAllFeedback()
	{
		Session::init( new \http\GlobalMemoryAdapter() );
		Session::addNegativeFeedback( "Something bad or unexpected happened" );
		Session::addNegativeFeedback( "Something else bad happened" );
		Session::addPositiveFeedback( "Something good happened" );

		$this->assertNotEmpty( Session::negativeFeedback(), "Failed to get negativeFeedback after setting it" );
		$this->assertCount( 1, Session::positiveFeedback(), "Failed to get positiveFeedback after setting it" );

		Session::clearAllFeedback();
		$this->assertEmpty( Session::negativeFeedback(), "negativeFeedback should be cleared" );
	}

	/**
	 * @covers	negativeFeedback
	 * 			T_FUNCTION T_STATIC T_PUBLIC negativeFeedback ( )
	 * @todo	Implement testNegativeFeedback().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-30 21:53:20.
	 */
	public function testNegativeFeedback()
	{
		Session::init( new \http\GlobalMemoryAdapter() );
		Session::addNegativeFeedback( "Something bad or unexpected happened" );
		Session::addNegativeFeedback( "Something else bad happened" );

		$this->assertNotEmpty( Session::negativeFeedback(), "Failed to get negativeFeedback after setting it" );
		$this->assertCount( 2, Session::negativeFeedback(), "Failed to get negativeFeedback after setting it" );
	}

	/**
	 * @covers	addNegativeFeedback
	 * 			T_FUNCTION T_STATIC T_PUBLIC addNegativeFeedback ( $message = null)
	 * @todo	Implement testAddNegativeFeedback().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-30 21:53:20.
	 */
	public function testAddNegativeFeedback()
	{
		Session::init( new \http\GlobalMemoryAdapter() );
		Session::addNegativeFeedback( "Something bad or unexpected happened" );
		Session::addNegativeFeedback( "Something else bad happened" );

		$this->assertNotEmpty( Session::negativeFeedback(), "Failed to get negativeFeedback after setting it" );
		$this->assertCount( 2, Session::negativeFeedback(), "Failed to get negativeFeedback after setting it" );
	}

	/**
	 * @covers	addValidationFeedback
	 * 			T_FUNCTION T_STATIC T_PUBLIC addValidationFeedback ( $message = null)
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-30 21:53:20.
	 */
	public function testAddValidationFeedback()
	{
		Session::init( new \http\GlobalMemoryAdapter() );
		Session::addValidationFeedback( "Something bad or unexpected happened" );
		Session::addValidationFeedback( "Something else bad happened" );

		$this->assertNotEmpty( Session::negativeFeedback(), "Failed to get addValidationFeedback after setting it" );
		$this->assertCount( 2, Session::negativeFeedback(), "Failed to get addValidationFeedback after setting it" );
	}

	/**
	 * @covers	positiveFeedback
	 * 			T_FUNCTION T_STATIC T_PUBLIC positiveFeedback ( )
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-30 21:53:20.
	 */
	public function testPositiveFeedback()
	{
		Session::init( new \http\GlobalMemoryAdapter() );
		Session::addPositiveFeedback( "Something good happened" );

		$this->assertCount( 1, Session::positiveFeedback(), "Failed to get positiveFeedback after setting it" );
	}

	/**
	 * @covers	addPositiveFeedback
	 * 			T_FUNCTION T_STATIC T_PUBLIC addPositiveFeedback ( $message = null)
	 * @todo	Implement testAddPositiveFeedback().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-30 21:53:20.
	 */
	public function testAddPositiveFeedback()
	{
		Session::init( new \http\GlobalMemoryAdapter() );
		Session::addPositiveFeedback( "Something good happened" );

		$this->assertCount( 1, Session::positiveFeedback(), "Failed to get positiveFeedback after setting it" );
	}

	/**
	 * @covers	isUserLoggedIn
	 * 			T_FUNCTION T_STATIC T_PUBLIC isUserLoggedIn ( )
	 * @todo	Implement testIsUserLoggedIn().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-30 21:53:20.
	 */
	public function testIsUserLoggedIn()
	{
		Session::init( new \http\GlobalMemoryAdapter() );
		Session::addPositiveFeedback( "Something good happened" );

		$this->assertFalse( Session::isUserLoggedIn(), "Failed to get isUserLoggedIn" );
	}


/* {functions} */
}
