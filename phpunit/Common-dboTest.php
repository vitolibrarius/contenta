<?php
/**
 * DO NOT EDIT CUSTOM MARKERS
 * Generated from Class.tpl by PhpTestClassGenerator.php on 2016-09-27 09:16:52.
 * https://phpunit.de/manual/current/en/appendixes.assertions.html
 */



use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;
/* * Generated from UseStatements.tpl by PhpTestClassGenerator.php on 2016-09-27 09:16:52. */
use \DataObject as DataObject;
/* {useStatements} */

class Common_dboTest extends PHPUnit_Framework_TestCase
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
	 * @covers	dbo_valueForKeypath
	 * 			T_FUNCTION dbo_valueForKeypath ( $keypath, DataObject $dbo = null, $separator = '/')
	 * @todo	Implement testDbo_valueForKeypath().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-09-27 09:16:52.
	 */
	public function testDbo_valueForKeypath()
	{
		test_initializeDatabase(true);
		test_importTestData( array( "Publisher", "Series", "Publication" ) );
		$publisher = Model::Named('Publisher')->objectForId( 1 );
		$keypaths = array( 
			"series/name" => array( "BatVito", "Nightwing" ),
			"name" => "DC Comics",
			"series/publications/name" => array( "The Big Burn: Sparks", "Knightmoves", "Bad Blood" )
		);
		
		foreach( $keypaths as $kp => $expected ) {
			$value = dbo_valueForKeypath( $kp, $publisher );
			$this->assertEquals( $expected, $value, "values do not match" );
		}
	}

	/**
	 * @covers	dbo_setValueForKeypath
	 * 			T_FUNCTION dbo_setValueForKeypath ( $keypath, $value, DataObject $dbo = null, $separator = '/')
	 * @todo	Implement testDbo_setValueForKeypath().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-09-27 09:16:52.
	 */
	public function testDbo_setValueForKeypath()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}


/* {functions} */
}
