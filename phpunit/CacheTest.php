<?php
/**
 * DO NOT EDIT CUSTOM MARKERS
 * Generated from Class.tpl by PhpTestClassGenerator.php on 2016-05-16 20:36:16.
 */



use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;
/* * Generated from UseStatements.tpl by PhpTestClassGenerator.php on 2016-05-16 20:36:16. */
use \Config as Config;
use \Logger as Logger;
/* {useStatements} */

class CacheTest extends PHPUnit_Framework_TestCase
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
	 * 			T_FUNCTION T_STATIC T_PUBLIC T_FINAL T_PRIVATE instance ( )
	 * @todo	Implement testInstance().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:36:16.
	 */
	public function testInstance()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	MakeKey
	 * 			T_FUNCTION T_STATIC T_PUBLIC T_FINAL MakeKey ( )
	 * @todo	Implement testMakeKey().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:36:16.
	 */
	public function testMakeKey()
	{
		$key = Cache::MakeKey("Vito", "Librarius", "Contenta");
		$this->assertEquals( $key, "Vito/Librarius/Contenta" );
	}

	/**
	 * @covers	Clear
	 * 			T_FUNCTION T_STATIC T_PUBLIC T_FINAL Clear ( $key)
	 * @todo	Implement testClear().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:36:16.
	 */
	public function testClear()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	Fetch
	 * 			T_FUNCTION T_STATIC T_PUBLIC T_FINAL Fetch ( $key, $default = null, $customTTL)
	 * @todo	Implement testFetch().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:36:16.
	 */
	public function testFetch()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	Store
	 * 			T_FUNCTION T_STATIC T_PUBLIC T_FINAL Store ( $key, $data = null)
	 * @todo	Implement testStore().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:36:16.
	 */
	public function testStore()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	fullpath
	 * 			T_FUNCTION T_PRIVATE fullpath ( $key)
	 * @todo	Implement testFullpath().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:36:16.
	 */
	public function testFullpath()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	fetchCachedValue
	 * 			T_FUNCTION T_PUBLIC fetchCachedValue ( $key, $default = null, $customTTL)
	 * @todo	Implement testFetchCachedValue().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:36:16.
	 */
	public function testFetchCachedValue()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	storeCachedValue
	 * 			T_FUNCTION T_PUBLIC storeCachedValue ( $key, $data)
	 * @todo	Implement testStoreCachedValue().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:36:16.
	 */
	public function testStoreCachedValue()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	clearCachedValue
	 * 			T_FUNCTION T_PUBLIC clearCachedValue ( $key)
	 * @todo	Implement testClearCachedValue().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:36:16.
	 */
	public function testClearCachedValue()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	purgeExpired
	 * 			T_FUNCTION T_PUBLIC purgeExpired ( )
	 * @todo	Implement testPurgeExpired().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:36:16.
	 */
	public function testPurgeExpired()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}


/* {functions} */
}
