<?php
/**
 * DO NOT EDIT CUSTOM MARKERS
 * Generated from Class.tpl by PhpTestClassGenerator.php on 2016-05-16 21:44:52.
 */



use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;
/* * Generated from UseStatements.tpl by PhpTestClassGenerator.php on 2016-05-16 21:44:52. */
use \utilities\Stopwatch as Stopwatch;
/* {useStatements} */

class DatabaseTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
		test_initializeDatabase(false);
    }

    protected function tearDown()
    {
    }

/*	 Test functions */

	/**
	 * @covers	instance
	 * 			T_FUNCTION T_STATIC T_PUBLIC T_FINAL instance ( )
	 * @todo	Implement testInstance().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:44:52.
	 */
	public function testInstance()
	{
		$db = Database::instance();
		$this->assertNotNull( $db );
	}

	/**
	 * @covers	verifyDatabase
	 * 			T_FUNCTION T_PUBLIC verifyDatabase ( )
	 * @todo	Implement testVerifyDatabase().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:44:52.
	 */
	public function testVerifyDatabase()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}


/* {functions} */
}
