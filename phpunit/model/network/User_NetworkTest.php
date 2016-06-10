<?php
/**
 * DO NOT EDIT CUSTOM MARKERS
 * Generated from Class.tpl by PhpTestClassGenerator.php on 2016-06-08 16:58:25.
 * https://phpunit.de/manual/current/en/appendixes.assertions.html
 */

namespace model\network;

use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;
/* * Generated from UseStatements.tpl by PhpTestClassGenerator.php on 2016-06-08 16:58:25. */
use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \model\network\User_NetworkDBO as User_NetworkDBO;
use \model\user\Users as Users;
use \model\user\UsersDBO as UsersDBO;
use \model\network\Network as Network;
use \model\network\NetworkDBO as NetworkDBO;
/* {useStatements} */

class User_NetworkTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
		test_initializeDatabase(true);
		test_importTestData( array( "Users", "Network" ) );
    }

    public static function tearDownAfterClass()
    {
    }

    protected function setUp()
    {
    	$this->model = Model::Named('User_Network');
    	$this->assertNotNull( $this->model, "Could not find 'User_Network' model" );
    }

    protected function tearDown()
    {
    }

/*	 Test functions */

	/**
	 * @covers	createForIp_address
	 * 			T_FUNCTION T_PUBLIC createForIp_address ( $user, $ipAddress)
	 * @todo	Implement testCreateForIp_address().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-08 16:58:25.
	 */
	public function testCreateForIp_address()
	{
		$userDBO = Model::Named("Users")->objectForEmail('test@home.com');
		list($join, $errors) = $this->model->createForIp_address( $userDBO, "192.168.1.99" );
		$this->assertNull( $errors, "Failed to create new record" . var_export($errors, true) );
		$this->assertTrue( $join != false, "Failed to create join" );
	}

	/**
	 * @covers	createObject
	 * 			T_FUNCTION T_PUBLIC createObject ( $values)
	 * @todo	Implement testCreateObject().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-08 16:58:25.
	 */
	public function testCreateObject()
	{
		list($join, $errors) = $this->model->createObject( array( "user_id" => 1, "network_id" => 2) );
		$this->assertNull( $errors, "Failed to create new record" . var_export($errors, true) );
		$this->assertTrue( $join != false, "Failed to create join" );

		$userDBO = Model::Named("Users")->objectForEmail('test@home.com');
		$networkDBO = Model::Named("Network")->objectForIp_address('127.0.0.1');
		list($join, $errors) = $this->model->createObject( array( "user" => $userDBO, "network" => $networkDBO) );
		$this->assertNull( $errors, "Failed to create new record" . var_export($errors, true) );
		$this->assertTrue( $join != false, "Failed to create join" );
	}

	/**
	 * @covers	updateObject
	 * 			T_FUNCTION T_PUBLIC updateObject ( DataObject $object = null, $values)
	 * @todo	Implement testUpdateObject().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-08 16:58:25.
	 */
	public function testUpdateObject()
	{
	}

	/**
	 * @covers	attributesFor
	 * 			T_FUNCTION T_PUBLIC attributesFor ( $object = null, $type = null)
	 * @todo	Implement testAttributesFor().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-08 16:58:25.
	 */
	public function testAttributesFor()
	{
	}

	/**
	 * @covers	attributeIsEditable
	 * 			T_FUNCTION T_PUBLIC attributeIsEditable ( $object = null, $type = null, $attr)
	 * @todo	Implement testAttributeIsEditable().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-08 16:58:25.
	 */
	public function testAttributeIsEditable()
	{
	}

	/**
	 * @covers	attributeDefaultValue
	 * 			T_FUNCTION T_PUBLIC attributeDefaultValue ( $object = null, $type = null, $attr)
	 * @todo	Implement testAttributeDefaultValue().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-08 16:58:25.
	 */
	public function testAttributeDefaultValue()
	{
	}

	/**
	 * @covers	attributeEditPattern
	 * 			T_FUNCTION T_PUBLIC attributeEditPattern ( $object = null, $type = null, $attr)
	 * @todo	Implement testAttributeEditPattern().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-08 16:58:25.
	 */
	public function testAttributeEditPattern()
	{
	}

	/**
	 * @covers	attributeOptions
	 * 			T_FUNCTION T_PUBLIC attributeOptions ( $object = null, $type = null, $attr)
	 * @todo	Implement testAttributeOptions().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-08 16:58:25.
	 */
	public function testAttributeOptions()
	{
	}

	/**
	 * @covers	validate_user_id
	 * 			T_FUNCTION validate_user_id ( $object = null, $value)
	 * @todo	Implement testValidate_user_id().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-08 16:58:25.
	 */
	public function testValidate_user_id()
	{
	}

	/**
	 * @covers	validate_network_id
	 * 			T_FUNCTION validate_network_id ( $object = null, $value)
	 * @todo	Implement testValidate_network_id().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-08 16:58:25.
	 */
	public function testValidate_network_id()
	{
	}


/* {functions} */
}
