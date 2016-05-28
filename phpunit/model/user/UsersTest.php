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
		$userDBO = $this->model->objectForEmail('test@home.com');
		$newObj = $this->model->update( $userDBO
			, "NewTestName" // $name
			, null // $email
			, null // $active
			, null // $account_type
			, null // $rememberme_token
			, null // $api_hash
			, null // $password_hash
			, null // $password_reset_hash
			, null // $activation_hash
			, null // $failed_logins
			, null // $creation_timestamp
			, null // $last_login_timestamp
			, null // $last_failed_login
			, null // $password_reset_timestamp
		);

		$this->assertTrue( ($newObj != false), var_export($newObj, true) );
		$this->assertNotEquals( $userDBO->name, $newObj->name, "Names should be differnt" );

	}

	/**
	 * @covers	attributesFor
	 * 			T_FUNCTION T_PUBLIC attributesFor ( $object = null, $type = null)
	 * @todo	Implement testAttributesFor().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testAttributesFor()
	{
		$attr = $this->model->attributesFor(null, null);
		$this->assertCount( 14, $attr, var_export($attr, true) );

		$userDBO = $this->model->objectForEmail('test@home.com');
		$attr = $this->model->attributesFor($userDBO, null);
		$this->assertCount( 14, $attr, var_export($attr, true) );
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

		$strWithSpaces = 'test string with spaces';
		$match = preg_match( $pattern, $strWithSpaces );
		$this->assertEquals( 0, $match, "String with spaces '$strWithSpaces'" );

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
		$length = array( null, '', '   ', 'vito', 'name with spaces' );
		foreach ($length as $l ) {
			$validation = $this->model->validate_name( null, $l );
			$this->assertNotNull( $validation, "'$l' username" );
		}

		$existing = $this->model->objectForName('vito');
		$validation = $this->model->validate_name( $existing, 'vito' );
		$this->assertNull( $validation, "'$l' username" );
	}

	/**
	 * @covers	validate_email
	 * 			T_FUNCTION validate_email ( $object = null, $value)
	 * @todo	Implement testValidate_email().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testValidate_email()
	{
		$length = array( null, '', '   ', 'vitolibrarius', 'vitolibrarius@notADomain' );
		foreach ($length as $l ) {
			$validation = $this->model->validate_email( null, $l );
// 			echo "Validation for '".$l."' = '" . $validation . "'" . PHP_EOL;
			$this->assertNotNull( $validation, "'$l' email" );
		}

		$existing = $this->model->objectForEmail('test@home.com');
		$validation = $this->model->validate_email( $existing, 'vitolibrarius@gmail.com' );
		$this->assertNotNull( $validation, "'vitolibrarius@gmail.com email is already in use" );

		$existing = $this->model->objectForEmail('vitolibrarius@gmail.com');
		$validation = $this->model->validate_email( $existing, 'vitolibrarius@gmail.com' );
		$this->assertNull( $validation, "'vitolibrarius@gmail.com email should be for the exising object" );
	}

	/**
	 * @covers	validate_active
	 * 			T_FUNCTION validate_active ( $object = null, $value)
	 * @todo	Implement testValidate_active().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testValidate_active()
	{
		$validation = $this->model->validate_active( null, null );
		$this->assertNotNull( $validation, $validation );

		$validation = $this->model->validate_active( null, 'bad value' );
		$this->assertNotNull( $validation, $validation );

		$validation = $this->model->validate_active( null, 'yes');
		$this->assertNull( $validation, $validation );

		$validation = $this->model->validate_active( null, true);
		$this->assertNull( $validation, $validation );

		$validation = $this->model->validate_active( null, 'no');
		$this->assertNull( $validation, $validation );

		$validation = $this->model->validate_active( null, false);
		$this->assertNull( $validation, $validation );
	}

	/**
	 * @covers	validate_account_type
	 * 			T_FUNCTION validate_account_type ( $object = null, $value)
	 * @todo	Implement testValidate_account_type().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testValidate_account_type()
	{
		$existing = $this->model->objectForEmail('test@home.com');

		$validation = $this->model->validate_account_type( $existing, 'bad value' );
		$this->assertNotNull( $validation, $validation );

		$validation = $this->model->validate_account_type( $existing, Users::StandardRole);
		$this->assertNull( $validation, $validation );

		$validation = $this->model->validate_account_type( $existing, Users::AdministratorRole);
		$this->assertNull( $validation, $validation );
	}

	/**
	 * @covers	validate_rememberme_token
	 * 			T_FUNCTION validate_rememberme_token ( $object = null, $value)
	 * @todo	Implement testValidate_rememberme_token().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testValidate_rememberme_token()
	{
	}

	/**
	 * @covers	validate_api_hash
	 * 			T_FUNCTION validate_api_hash ( $object = null, $value)
	 * @todo	Implement testValidate_api_hash().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testValidate_api_hash()
	{
	}

	/**
	 * @covers	validate_password_reset_hash
	 * 			T_FUNCTION validate_password_reset_hash ( $object = null, $value)
	 * @todo	Implement testValidate_password_reset_hash().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testValidate_password_reset_hash()
	{
	}

	/**
	 * @covers	validate_activation_hash
	 * 			T_FUNCTION validate_activation_hash ( $object = null, $value)
	 * @todo	Implement testValidate_activation_hash().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testValidate_activation_hash()
	{
	}

	/**
	 * @covers	validate_failed_logins
	 * 			T_FUNCTION validate_failed_logins ( $object = null, $value)
	 * @todo	Implement testValidate_failed_logins().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testValidate_failed_logins()
	{
		$validation = $this->model->validate_failed_logins( null, null );
		$this->assertNotNull( $validation, $validation );

		$validation = $this->model->validate_failed_logins( null, 'bad value' );
		$this->assertNotNull( $validation, $validation );

		$validation = $this->model->validate_failed_logins( null, 'yes');
		$this->assertNotNull( $validation, $validation );

		$validation = $this->model->validate_failed_logins( null, 123);
		$this->assertNull( $validation, $validation );

		$validation = $this->model->validate_failed_logins( null, -123);
		$this->assertNotNull( $validation, $validation );

		$validation = $this->model->validate_failed_logins( null, 0);
		$this->assertNull( $validation, $validation );
	}

	/**
	 * @covers	validate_creation_timestamp
	 * 			T_FUNCTION validate_creation_timestamp ( $object = null, $value)
	 * @todo	Implement testValidate_creation_timestamp().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testValidate_creation_timestamp()
	{
	}

	/**
	 * @covers	validate_last_login_timestamp
	 * 			T_FUNCTION validate_last_login_timestamp ( $object = null, $value)
	 * @todo	Implement testValidate_last_login_timestamp().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testValidate_last_login_timestamp()
	{
	}

	/**
	 * @covers	validate_last_failed_login
	 * 			T_FUNCTION validate_last_failed_login ( $object = null, $value)
	 * @todo	Implement testValidate_last_failed_login().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testValidate_last_failed_login()
	{
	}

	/**
	 * @covers	validate_password_reset_timestamp
	 * 			T_FUNCTION validate_password_reset_timestamp ( $object = null, $value)
	 * @todo	Implement testValidate_password_reset_timestamp().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testValidate_password_reset_timestamp()
	{
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
		$restricted = array(Users::name, Users::email, "password" );
		foreach( $restricted as $attr ) {
			$message = $this->model->attributeRestrictionMessage( null, null, $attr);
			$this->assertNotNull( $message, $message );
		}
	}

	/**
	 * @covers	attributeDefaultValue
	 * 			T_FUNCTION T_PUBLIC attributeDefaultValue ( $object = null, $type = null, $attr)
	 * @todo	Implement testAttributeDefaultValue().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 17:35:11.
	 */
	public function testAttributeDefaultValue()
	{
		$restricted = array(Users::account_type => Users::StandardRole, Users::failed_logins => 0);
		foreach( $restricted as $attr => $expected ) {
			$def = $this->model->attributeDefaultValue( null, null, $attr);
			$this->assertEquals( $expected, $def );
		}
	}

	/**
	 * @covers	validate_password_hash
	 * 			T_FUNCTION validate_password_hash ( $object = null, $value)
	 * @todo	Implement testValidate_password_hash().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 17:35:11.
	 */
	public function testValidate_password_hash()
	{
	}


/* {functions} */
}
