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
		test_initializeDatabase(true);
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
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-28 15:53:59.
	 */
	public function testRecordLoginFromIp_address()
	{
		$userDBO = $this->model->objectForName('test');
		$result = $userDBO->recordLoginFromIp_address( "192.168.1.99" );
		$this->assertTrue( $result != false, "Failed to create login" );
	}

	/**
	 * @covers	allLoginIP
	 * 			T_FUNCTION T_PUBLIC allLoginIP ( )
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-28 15:53:59.
     * @depends testRecordLoginFromIp_address
	 */
	public function testAllLoginIP()
	{
		$userDBO = $this->model->objectForName('test');
		$allLogin = $userDBO->allLoginIP();
		$this->assertCount( 1, $allLogin, var_export($allLogin, true) );
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


/*  Test functions */

	/**
	 * @covers	clearFailedLogin
	 * 			T_FUNCTION T_PUBLIC clearFailedLogin ( )
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-29 17:04:25.
	 */
	public function testClearFailedLogin()
	{
		$userDBO = $this->model->objectForName('johnnyfail');
		$this->assertTrue( $userDBO != false, "Failed to find user" );
		$this->assertEquals( 2, $userDBO->failed_logins(), "Incorrect initial failed login count" );
		$this->assertEquals( "May 22, 2016 16:37", $userDBO->formattedDateTime_last_failed_login(), "Incorrect initial failed login date" );

		$userDBO->clearFailedLogin();
		$this->assertEquals( 0, $userDBO->failed_logins(), "Incorrect updated failed login count" );
		$this->assertNull( $userDBO->formattedDateTime_last_failed_login(), "Incorrect updated failed login date" );

		$userDBO = $this->model->refreshObject($userDBO);
		$this->assertEquals( 0, $userDBO->failed_logins(), "Incorrect refreshed failed login count" );
		$this->assertNull( $userDBO->formattedDateTime_last_failed_login(), "Incorrect refreshed failed login date" );
	}


/*  Test functions */

	/**
	 * @covers	increaseFailedLogin
	 * 			T_FUNCTION increaseFailedLogin ( )
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-29 19:22:47.
	 */
	public function testIncreaseFailedLogin()
	{
		$userDBO = $this->model->objectForName('vito');
		$this->assertEquals( 0, $userDBO->failed_logins(), "Incorrect failed login count" );
		$this->assertNull( $userDBO->last_failed_login(), "Incorrect failed login date" );

		$userDBO->increaseFailedLogin();
		$this->assertEquals( 1, $userDBO->failed_logins(), "Incorrect updated failed login count" );
		$this->assertNotNull( $userDBO->last_failed_login(), "Incorrect updated failed login date" );

		$userDBO = $this->model->refreshObject($userDBO);
		$this->assertEquals( 1, $userDBO->failed_logins(), "Incorrect updated failed login count" );
		$this->assertNotNull( $userDBO->last_failed_login(), "Incorrect updated failed login date" );
	}

	/**
	 * @covers	stampLogin
	 * 			T_FUNCTION stampLogin ( )
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-29 19:22:47.
	 */
	public function testStampLogin()
	{
		$userDBO = $this->model->objectForName('vito');
		$userDBO->setLast_login_timestamp(null);
		$userDBO->increaseFailedLogin();

		$this->assertNull( $userDBO->last_login_timestamp(), "Incorrect last login date" );
		$this->assertTrue( $userDBO->failed_logins() > 0, "Incorrect failed login count" );
		$this->assertNotNull( $userDBO->last_failed_login(), "Incorrect updated failed login date" );

		$userDBO->stampLogin();
		$userDBO = $this->model->refreshObject($userDBO);
		$this->assertNotNull( $userDBO->last_login_timestamp(), "Incorrect updated last login date" );
		$this->assertEquals( 0, $userDBO->failed_logins(), "Incorrect failed login count" );
		$this->assertNull( $userDBO->last_failed_login(), "Incorrect failed login date" );
	}

	/**
	 * @covers	generateRememberme_token
	 * 			T_FUNCTION generateRememberme_token ( )
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-29 19:22:47.
	 */
	public function testGenerateRememberme_token()
	{
		$userDBO = $this->model->objectForName('vito');
		$userDBO->setRememberme_token(null);
		$userDBO->saveChanges();

		$this->assertNull( $userDBO->rememberme_token(), "Incorrect initial rememberme_token" );

		$userDBO->generateRememberme_token();
		$userDBO = $this->model->refreshObject($userDBO);
		$this->assertNotNull( $userDBO->rememberme_token(), "Incorrect updated rememberme_token" );
	}

	/**
	 * @covers	generateActivation_hash
	 * 			T_FUNCTION generateActivation_hash ( )
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-29 19:22:47.
	 */
	public function testGenerateActivation_hash()
	{
		$userDBO = $this->model->objectForName('vito');
		$userDBO->setActivation_hash(null);
		$userDBO->saveChanges();

		$this->assertNull( $userDBO->activation_hash(), "Incorrect initial Activation_hash" );

		$userDBO->generateActivation_hash();
		$userDBO = $this->model->refreshObject($userDBO);
		$this->assertNotNull( $userDBO->activation_hash(), "Incorrect updated Activation_hash" );
	}

	/**
	 * @covers	generatePassword_reset_hash
	 * 			T_FUNCTION generatePassword_reset_hash ( )
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-29 19:22:47.
	 */
	public function testGeneratePassword_reset_hash()
	{
		$userDBO = $this->model->objectForName('vito');
		$userDBO->setPassword_reset_hash(null);
		$userDBO->saveChanges();

		$this->assertNull( $userDBO->password_reset_hash(), "Incorrect initial Password_reset_hash" );

		$userDBO->generatePassword_reset_hash();
		$userDBO = $this->model->refreshObject($userDBO);
		$this->assertNotNull( $userDBO->password_reset_hash(), "Incorrect updated Password_reset_hash" );
	}

	/**
	 * @covers	generateApi_hash
	 * 			T_FUNCTION T_PUBLIC generateApi_hash ( $userObj = null)
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-29 19:22:47.
	 */
	public function testGenerateApi_hash()
	{
		$userDBO = $this->model->objectForName('vito');
		$userDBO->setApi_hash(null);
		$userDBO->saveChanges();

		$this->assertNull( $userDBO->api_hash(), "Incorrect initial Api_hash" );

		$userDBO->generateApi_hash();
		$userDBO = $this->model->refreshObject($userDBO);
		$this->assertNotNull( $userDBO->api_hash(), "Incorrect updated Api_hash" );
	}


/* {functions} */
}
