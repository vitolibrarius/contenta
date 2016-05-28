<?php
/**
 * DO NOT EDIT CUSTOM MARKERS
 * Generated from Class.tpl by PhpTestClassGenerator.php on 2016-05-28 15:53:59.
 */

namespace model\user;

use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;
/* * Generated from UseStatements.tpl by PhpTestClassGenerator.php on 2016-05-28 15:53:59. */
use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \model\user\Users as Users;
use \model\network\User_Network as User_Network;
use \model\network\User_NetworkDBO as User_NetworkDBO;
use \model\User_Series as User_Series;
use \model\User_SeriesDBO as User_SeriesDBO;
/* {useStatements} */

class UsersDBOTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
		test_initializeDatabase(false);
		test_importTestData( array( "Users" ) );
    }

    public static function tearDownAfterClass()
    {
    }

    protected function setUp()
    {
    	$this->model = Model::Named('Users');
    	$this->assertNotNull( $this->model, "Could not find 'Users' model" );
    }

    protected function tearDown()
    {
    }

/*	 Test functions */

	/**
	 * @covers	recordLoginFromIp_address
	 * 			T_FUNCTION T_PUBLIC recordLoginFromIp_address ( $ip = null)
	 * @todo	Implement testRecordLoginFromIp_address().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-28 15:53:59.
	 */
	public function testRecordLoginFromIp_address()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	allLoginIP
	 * 			T_FUNCTION T_PUBLIC allLoginIP ( )
	 * @todo	Implement testAllLoginIP().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-28 15:53:59.
	 */
	public function testAllLoginIP()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	seriesBeingRead
	 * 			T_FUNCTION T_PUBLIC seriesBeingRead ( $limit)
	 * @todo	Implement testSeriesBeingRead().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-28 15:53:59.
	 */
	public function testSeriesBeingRead()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	addSeries
	 * 			T_FUNCTION T_PUBLIC addSeries ( $series = null)
	 * @todo	Implement testAddSeries().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-28 15:53:59.
	 */
	public function testAddSeries()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	saveChanges
	 * 			T_FUNCTION T_PUBLIC saveChanges ( $series = null)
	 */
	public function testSaveChanges()
	{
		$userDBO = $this->model->objectForName('test');
		$userDBO->setName( "johnsmith" );
		$userDBO->setActive( false );

		$this->assertEquals( "johnsmith", $userDBO->name(), "Should show updated value 'johnsmith'" );
		$this->assertEquals( false, $userDBO->active(), "Should show updated value 'false'" );
		$success = $userDBO->saveChanges();
		$this->assertTrue( $success != false, "Save failed" );

		$updated = $this->model->refreshObject($userDBO);
		$this->assertTrue( $updated instanceof UsersDBO, "Wrong class" );
		$this->assertEquals( "johnsmith", $updated->name, "saved value 'johnsmith'" );
		$this->assertEquals( false, $updated->active, "saved value 'false'" );
	}


/* {functions} */
}
