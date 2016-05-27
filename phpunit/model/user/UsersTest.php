<?php
/**
 * DO NOT EDIT CUSTOM MARKERS
 * Generated from Class.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
 */

namespace model\user;

use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;
/* * Generated from UseStatements.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58. */
use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \model\user\UsersDBO as UsersDBO;
use \model\network\User_Network as User_Network;
use \model\network\User_NetworkDBO as User_NetworkDBO;
use \model\User_Series as User_Series;
use \model\User_SeriesDBO as User_SeriesDBO;
/* {useStatements} */

class UsersTest extends PHPUnit_Framework_TestCase
{
	public $model;

    public static function setUpBeforeClass()
    {
		test_initializeDatabase(false);
		test_importTestData( array( "Users" ) );
    }

    public static function tearDownAfterClass()
    {
// 		test_exportTestData( array( "Users" ) );
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
	 * @covers	create
	 * 			T_FUNCTION T_PUBLIC create ( $name, $email, $active, $account_type, $rememberme_token, $api_hash, $password_hash, $password_reset_hash, $activation_hash, $failed_logins, $creation_timestamp, $last_login_timestamp, $last_failed_login, $password_reset_timestamp)
	 * @todo	Implement testCreate().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testCreate()
	{
	}

	/**
	 * @covers	update
	 * 			T_FUNCTION T_PUBLIC update ( UsersDBO $obj, $name, $email, $active, $account_type, $rememberme_token, $api_hash, $password_reset_hash, $activation_hash, $failed_logins, $creation_timestamp, $last_login_timestamp, $last_failed_login, $password_reset_timestamp)
	 * @todo	Implement testUpdate().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testUpdate()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	attributesFor
	 * 			T_FUNCTION T_PUBLIC attributesFor ( $object = null, $type = null)
	 * @todo	Implement testAttributesFor().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testAttributesFor()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	attributesMandatory
	 * 			T_FUNCTION T_PUBLIC attributesMandatory ( $object = null)
	 * @todo	Implement testAttributesMandatory().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testAttributesMandatory()
	{
		$attr = $this->model->attributesMandatory(null);
		$this->assertCount( 3, $attr, var_export($attr, true) );
		$this->assertEquals(array( Users::name, Users::email, Users::password_hash), $attr);
	}

	/**
	 * @covers	attributeIsEditable
	 * 			T_FUNCTION T_PUBLIC attributeIsEditable ( $object = null, $type = null, $attr)
	 * @todo	Implement testAttributeIsEditable().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testAttributeIsEditable()
	{
		$names = $this->model->allColumnNames();
		foreach( $names as $attr ) {
			$expected = ($this->model->tablePK() != $attr);
			$tested = $this->model->attributeIsEditable( null, null, $attr );
			$this->assertEquals($expected, $tested, "attributeIsEditable for $attr = " . var_export($tested, true));
		}
	}

	/**
	 * @covers	attributeEditPattern
	 * 			T_FUNCTION T_PUBLIC attributeEditPattern ( $object = null, $type = null, $attr)
	 * @todo	Implement testAttributeEditPattern().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testAttributeEditPattern()
	{
		$pattern = $this->model->attributeEditPattern ( null, null, Users::name);
		$this->assertNotNull( $pattern, "Edit pattern for 'name'" );

		$test1Char = test_generateRandomString( 1 );
		$match = preg_match( $pattern, $test1Char );
		$this->assertEquals( 0, $match, "1 Character username '$test1Char'" );

		$test2Char = test_generateRandomString( 2 );
		$match = preg_match( $pattern, $test2Char );
		$this->assertEquals( 1, $match, "$match  2 Character username '$test2Char'" );

		$test64Char = test_generateRandomString( 64 );
		$match = preg_match( $pattern, $test64Char );
		$this->assertEquals( 1, $match, "$match  64 Character username '$test64Char'" );

		$test65Char = test_generateRandomString( 65 );
		$match = preg_match( $pattern, $test65Char );
		$this->assertEquals( 0, $match, "$match 65 Character username '$test65Char'" );
	}

	/**
	 * @covers	attributeOptions
	 * 			T_FUNCTION T_PUBLIC attributeOptions ( $object = null, $type = null, $attr)
	 * @todo	Implement testAttributeOptions().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testAttributeOptions()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	validate_name
	 * 			T_FUNCTION validate_name ( $object = null, $value)
	 * @todo	Implement testValidate_name().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testValidate_name()
	{
		$length = array( null, '', '   ', 'vito' );
		foreach ($length as $l ) {
			$validation = $this->model->validate_name( null, $l );
			echo "Validation for '$l' = '" . $validation . "'" . PHP_EOL;
			$this->assertNotNull( $validation, "'$l' username" );
		}

		$existing = $this->model->objectForName('vito');
		$validation = $this->model->validate_name( $existing, 'vito' );
		echo "Validation for 'vito' = '" . $validation . "'" . PHP_EOL;
		$this->assertNotNull( $validation, "'$l' username" );

	}

	/**
	 * @covers	validate_email
	 * 			T_FUNCTION validate_email ( $object = null, $value)
	 * @todo	Implement testValidate_email().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testValidate_email()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	validate_active
	 * 			T_FUNCTION validate_active ( $object = null, $value)
	 * @todo	Implement testValidate_active().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testValidate_active()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	validate_account_type
	 * 			T_FUNCTION validate_account_type ( $object = null, $value)
	 * @todo	Implement testValidate_account_type().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testValidate_account_type()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	validate_rememberme_token
	 * 			T_FUNCTION validate_rememberme_token ( $object = null, $value)
	 * @todo	Implement testValidate_rememberme_token().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testValidate_rememberme_token()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	validate_api_hash
	 * 			T_FUNCTION validate_api_hash ( $object = null, $value)
	 * @todo	Implement testValidate_api_hash().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testValidate_api_hash()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	validate_password_reset_hash
	 * 			T_FUNCTION validate_password_reset_hash ( $object = null, $value)
	 * @todo	Implement testValidate_password_reset_hash().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testValidate_password_reset_hash()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	validate_activation_hash
	 * 			T_FUNCTION validate_activation_hash ( $object = null, $value)
	 * @todo	Implement testValidate_activation_hash().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testValidate_activation_hash()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	validate_failed_logins
	 * 			T_FUNCTION validate_failed_logins ( $object = null, $value)
	 * @todo	Implement testValidate_failed_logins().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testValidate_failed_logins()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	validate_creation_timestamp
	 * 			T_FUNCTION validate_creation_timestamp ( $object = null, $value)
	 * @todo	Implement testValidate_creation_timestamp().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testValidate_creation_timestamp()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	validate_last_login_timestamp
	 * 			T_FUNCTION validate_last_login_timestamp ( $object = null, $value)
	 * @todo	Implement testValidate_last_login_timestamp().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testValidate_last_login_timestamp()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	validate_last_failed_login
	 * 			T_FUNCTION validate_last_failed_login ( $object = null, $value)
	 * @todo	Implement testValidate_last_failed_login().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testValidate_last_failed_login()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	validate_password_reset_timestamp
	 * 			T_FUNCTION validate_password_reset_timestamp ( $object = null, $value)
	 * @todo	Implement testValidate_password_reset_timestamp().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testValidate_password_reset_timestamp()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}


/*  Test functions */

	/**
	 * @covers	attributeRestrictionMessage
	 * 			T_FUNCTION T_PUBLIC attributeRestrictionMessage ( $object = null, $type = null, $attr)
	 * @todo	Implement testAttributeRestrictionMessage().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 17:35:11.
	 */
	public function testAttributeRestrictionMessage()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	attributeDefaultValue
	 * 			T_FUNCTION T_PUBLIC attributeDefaultValue ( $object = null, $type = null, $attr)
	 * @todo	Implement testAttributeDefaultValue().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 17:35:11.
	 */
	public function testAttributeDefaultValue()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	validate_password_hash
	 * 			T_FUNCTION validate_password_hash ( $object = null, $value)
	 * @todo	Implement testValidate_password_hash().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 17:35:11.
	 */
	public function testValidate_password_hash()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}


/* {functions} */
}
