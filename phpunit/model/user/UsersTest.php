<?php
/**
 * DO NOT EDIT CUSTOM MARKERS
 * Generated from Class.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
 * https://phpunit.de/manual/current/en/appendixes.assertions.html
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
/* * Generated from UseStatements.tpl by PhpTestClassGenerator.php on 2016-05-29 19:22:52. */
use \Localized as Localized;
/* {useStatements} */

class UsersTest extends PHPUnit_Framework_TestCase
{
	public $model;

    public static function setUpBeforeClass()
    {
		test_initializeDatabase(true);
		test_importTestData( array( "Users" ) );
    }

    public static function tearDownAfterClass()
    {
// 		test_exportTestData( "UsersTest", array( "Users" ) );
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
	 * @covers	attributesFor
	 * 			T_FUNCTION T_PUBLIC attributesFor ( $object = null, $type = null)
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testAttributesFor()
	{
		$attr = $this->model->attributesFor(null, null);
		$this->assertCount( 5, $attr, var_export($attr, true) );

		$userDBO = $this->model->objectForEmail('test@home.com');
		$attr = $this->model->attributesFor($userDBO, null);
		$this->assertCount( 5, $attr, var_export($attr, true) );
	}

	/**
	 * @covers	attributesMandatory
	 * 			T_FUNCTION T_PUBLIC attributesMandatory ( $object = null)
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testAttributesMandatory()
	{
		$attr = $this->model->attributesMandatory(null);
		$this->assertCount( 5, $attr, var_export($attr, true) );
		$this->assertEquals(array( Users::name, Users::email, Users::active, Users::account_type, "password_hash"), $attr);
	}

	/**
	 * @covers	attributeIsEditable
	 * 			T_FUNCTION T_PUBLIC attributeIsEditable ( $object = null, $type = null, $attr)
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
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testAttributeOptions()
	{
	}

	/**
	 * @covers	validate_name
	 * 			T_FUNCTION validate_name ( $object = null, $value)
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
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testValidate_rememberme_token()
	{
	}

	/**
	 * @covers	validate_api_hash
	 * 			T_FUNCTION validate_api_hash ( $object = null, $value)
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testValidate_api_hash()
	{
	}

	/**
	 * @covers	validate_password_reset_hash
	 * 			T_FUNCTION validate_password_reset_hash ( $object = null, $value)
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testValidate_password_reset_hash()
	{
	}

	/**
	 * @covers	validate_activation_hash
	 * 			T_FUNCTION validate_activation_hash ( $object = null, $value)
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testValidate_activation_hash()
	{
	}

	/**
	 * @covers	validate_failed_logins
	 * 			T_FUNCTION validate_failed_logins ( $object = null, $value)
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testValidate_failed_logins()
	{
		$validation = $this->model->validate_failed_logins( null, null );
		$this->assertNull( $validation, $validation );

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
	 * @covers	validate_created
	 * 			T_FUNCTION validate_created ( $object = null, $value)
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testValidate_created()
	{
	}

	/**
	 * @covers	validate_last_login_timestamp
	 * 			T_FUNCTION validate_last_login_timestamp ( $object = null, $value)
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testValidate_last_login_timestamp()
	{
	}

	/**
	 * @covers	validate_last_failed_login
	 * 			T_FUNCTION validate_last_failed_login ( $object = null, $value)
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testValidate_last_failed_login()
	{
	}

	/**
	 * @covers	validate_password_reset_timestamp
	 * 			T_FUNCTION validate_password_reset_timestamp ( $object = null, $value)
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 12:44:58.
	 */
	public function testValidate_password_reset_timestamp()
	{
	}


/*  Test functions */

	/**
	 * @covers	attributeRestrictionMessage
	 * 			T_FUNCTION T_PUBLIC attributeRestrictionMessage ( $object = null, $type = null, $attr)
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
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-22 17:35:11.
	 */
	public function testValidate_password_hash()
	{
	}

	/**
	 * @covers	joinAttributes
	 * 			T_FUNCTION joinAttributes ( $joinModel = null)
	 */
	public function testJoinAttributes()
	{
		$expected = array( Users::id, "user_id"  );
		$user_network = Model::Named( "user_network" );
		$joins = $this->model->joinAttributes( $user_network );
		$this->assertEmpty(array_merge(array_diff($expected, $joins), array_diff($joins, $expected)));

		$user_series = Model::Named( "user_series" );
		$joins = $this->model->joinAttributes( $user_series );
		$this->assertEmpty(array_merge(array_diff($expected, $joins), array_diff($joins, $expected)));
	}

/*  Test functions */

	/**
	 * @covers	createObject
	 * 			T_FUNCTION T_PUBLIC createObject ( $values)
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-29 19:22:52.
	 */
	public function testCreateObject()
	{
		$values = array(
			Users::name => "NewlyCreated",
			Users::email => "newuser@home.com",
			"password" => "abc123_!890xyz",
			"password_check" => "abc123_!890xyz"
		);
		list($userDBO, $errors) = $this->model->createObject($values);
		$this->assertNull( $errors, "Failed to create new record" );
		$this->assertTrue( $userDBO != false, "Failed to create new record" );

		$values = array(
			Users::name => "Newly Created",
			Users::email => "",
			"password" => "abc123_!890xyz",
			"password_check" => "!890xyzabc123"
		);
		list($userDBO, $errors) = $this->model->createObject($values);
		$this->assertNotNull( $errors, "Failed to create new record" );
		$this->assertTrue( isset($errors[Users::name], $errors[Users::email], $errors["password"]),"Wrong error messages " . var_export($errors, true));
		$this->assertFalse( $userDBO, "created new record, should be invalid" );
	}

	/**
	 * @covers	validateForSave
	 * 			T_FUNCTION T_PUBLIC validateForSave ( $object = null, $values)
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-29 19:22:52.
	 */
	public function testValidateForSave()
	{
		$values = array(
			Users::name => "Newly Created",
			Users::email => "",
			"password" => "abc123_!890xyz",
			"password_check" => "!890xyzabc123"
		);
		$errors = $this->model->validateForSave(null, $values);
		$this->assertNotNull( $errors, "Failed to create new record" );
		$this->assertTrue( isset($errors[Users::name], $errors[Users::email], $errors["password"]),"Wrong error messages " . var_export($errors, true));
	}

	/**
	 * @covers	validatePassword
	 * 			T_FUNCTION validatePassword ( $object = null, $passwd = null, $passwdrepeat = null)
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-29 19:22:52.
	 */
	public function testValidatePassword()
	{
		$validation = $this->model->validatePassword ( null, null, null );
		$this->assertNotNull( $validation, "null passwords" );

		$validation = $this->model->validatePassword ( null, "password", null );
		$this->assertNotNull( $validation, "null passwords" );

		$validation = $this->model->validatePassword ( null, "password", "smoke" );
		$this->assertNotNull( $validation, "mismatched passwords" );

		$validation = $this->model->validatePassword ( null, "a", "a" );
		$this->assertNotNull( $validation, "small passwords" );

		$validation = $this->model->validatePassword ( null, "abcdef", "abcdef" );
		$this->assertNull( $validation, "minimum passwords" );
	}

	/**
	 * @covers	userTypes
	 * 			T_FUNCTION T_PUBLIC userTypes ( )
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-29 19:22:52.
	 */
	public function testUserTypes()
	{
		$expected = array( Users::AdministratorRole => "Administrator", Users::StandardRole => "Standard"  );
		$types = $this->model->userTypes();
		$this->assertEmpty(array_merge(array_diff($expected, $types), array_diff($types, $expected)));
	}

	/**
	 * @covers	userWithRemembermeToken
	 * 			T_FUNCTION T_PUBLIC userWithRemembermeToken ( )
	 */
	public function testUserWithRemembermeToken()
	{
		$token = '29c8a195a205990473745e4f335446e2fdb7a3083a6e52d9d1cc7d466b8dd487';
		$userDBO = $this->model->objectForName('johnnyfail');
		$this->assertTrue( $userDBO instanceof UsersDBO, "Failed to find user record for name 'johnnyfail'" );

		$existing = $this->model->userWithRemembermeToken( $userDBO->id, $token);
		$this->assertTrue( $existing instanceof UsersDBO, "Failed to find user record" );
		$this->assertEquals( $token, $existing->rememberme_token(), "token missmatch" );
	}

/*  Test functions */

	/**
	 * @covers	updateObject
	 * 			T_FUNCTION T_PUBLIC updateObject ( DataObject $object = null, $values)
	 * @depends testCreateObject
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-06 20:55:19.
	 */
	public function testUpdateObject()
	{
		$userDBO = $this->model->objectForEmail('newuser@home.com');
		$this->assertTrue( $userDBO instanceof UsersDBO, "Failed to find user record for email 'newuser@home.com'" );

		list( $updated, $errors ) = $this->model->updateObject( $userDBO, array(
				Users::name => "NewlyUpdated",
				"password" => "_!890xyz_abc123",
				"password_check" => "_!890xyz_abc123"
			)
		);
		$this->assertNull( $errors, "errors during update " . var_export($errors, true) );
		$this->assertTrue( $updated instanceof UsersDBO, "Failed to updated user record for email 'newuser@home.com'" );
		$this->assertEquals( "NewlyUpdated", $updated->name(), "Failed to update name to 'NewlyUpdated'" );
		$this->assertNotEquals( $userDBO->password_hash, $updated->password_hash(), "Failed to update password_hash" );
	}


/* {functions} */
}
