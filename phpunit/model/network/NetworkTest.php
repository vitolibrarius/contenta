<?php
/**
 * DO NOT EDIT CUSTOM MARKERS
 * Generated from Class.tpl by PhpTestClassGenerator.php on 2016-06-06 21:11:17.
 * https://phpunit.de/manual/current/en/appendixes.assertions.html
 */

namespace model\network;

use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;
/* * Generated from UseStatements.tpl by PhpTestClassGenerator.php on 2016-06-06 21:11:17. */
use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \model\network\NetworkDBO as NetworkDBO;
use \model\network\User_Network as User_Network;
use \model\network\User_NetworkDBO as User_NetworkDBO;
/* {useStatements} */

class NetworkTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
		test_initializeDatabase(true);
		test_importTestData( array( "Network", "Users", "User_Network" ) );
    }

    public static function tearDownAfterClass()
    {
//  		test_exportTestData( array( "Network", "Users", "User_Network" ) );
    }

    protected function setUp()
    {
    	$this->model = Model::Named('Network');
    	$this->assertNotNull( $this->model, "Could not find 'Network' model" );
    }

    protected function tearDown()
    {
    }

/*	 Test functions */

	/**
	 * @covers	create
	 * 			T_FUNCTION T_PUBLIC createObject ( $values)
	 * @todo	Implement testCreate().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-06 21:11:17.
	 */
	public function testCreateObject()
	{
		$this->model->createObject( array( "ip_address" => "127.0.0.1", "disable" => false));
		$this->model->createObject( array( "ip_address" => "192.168.1.77") );
		$this->model->createObject( array( "ip_address" => "192.30.252.122", "disable" => true));  // github
		$this->model->createObject( array( "ip_address" => "17.172.224.47", "disable" => "on"));  // apple
	}

	/**
	 * @covers	update
	 * 			T_FUNCTION T_PUBLIC updateObject ( NetworkDBO $obj, $values)
	 * @depends	testCreateObject
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-06 21:11:17.
	 */
	public function testUpdateObject()
	{
		$github = $this->model->objectForIp_address("192.30.252.122");

	}

	/**
	 * @covers	attributesFor
	 * 			T_FUNCTION T_PUBLIC attributesFor ( $object = null, $type = null)
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-06 21:11:17.
	 */
	public function testAttributesFor()
	{
		$attr = $this->model->attributesFor(null, null);
		$this->assertCount( 4, $attr, var_export($attr, true) );

		$github = $this->model->objectForIp_address("192.30.252.122");
		$attr = $this->model->attributesFor($github, null);
		$this->assertCount( 4, $attr, var_export($attr, true) );
	}

	/**
	 * @covers	attributesMandatory
	 * 			T_FUNCTION T_PUBLIC attributesMandatory ( $object = null)
	 * @todo	Implement testAttributesMandatory().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-06 21:11:17.
	 */
	public function testAttributesMandatory()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	attributeIsEditable
	 * 			T_FUNCTION T_PUBLIC attributeIsEditable ( $object = null, $type = null, $attr)
	 * @todo	Implement testAttributeIsEditable().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-06 21:11:17.
	 */
	public function testAttributeIsEditable()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	attributeDefaultValue
	 * 			T_FUNCTION T_PUBLIC attributeDefaultValue ( $object = null, $type = null, $attr)
	 * @todo	Implement testAttributeDefaultValue().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-06 21:11:17.
	 */
	public function testAttributeDefaultValue()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	attributeEditPattern
	 * 			T_FUNCTION T_PUBLIC attributeEditPattern ( $object = null, $type = null, $attr)
	 * @todo	Implement testAttributeEditPattern().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-06 21:11:17.
	 */
	public function testAttributeEditPattern()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	attributeOptions
	 * 			T_FUNCTION T_PUBLIC attributeOptions ( $object = null, $type = null, $attr)
	 * @todo	Implement testAttributeOptions().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-06 21:11:17.
	 */
	public function testAttributeOptions()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	validate_ip_address
	 * 			T_FUNCTION validate_ip_address ( $object = null, $value)
	 * @todo	Implement testValidate_ip_address().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-06 21:11:17.
	 */
	public function testValidate_ip_address()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	validate_ip_hash
	 * 			T_FUNCTION validate_ip_hash ( $object = null, $value)
	 * @todo	Implement testValidate_ip_hash().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-06 21:11:17.
	 */
	public function testValidate_ip_hash()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	validate_created
	 * 			T_FUNCTION validate_created ( $object = null, $value)
	 * @todo	Implement testValidate_created().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-06 21:11:17.
	 */
	public function testValidate_created()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	validate_disable
	 * 			T_FUNCTION validate_disable ( $object = null, $value)
	 * @todo	Implement testValidate_disable().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-06 21:11:17.
	 */
	public function testValidate_disable()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}


/* {functions} */
}
