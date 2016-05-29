<?php
/**
 * DO NOT EDIT CUSTOM MARKERS
 * Generated from Class.tpl by PhpTestClassGenerator.php on 2016-05-16 21:44:52.
 */



use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;
/* * Generated from UseStatements.tpl by PhpTestClassGenerator.php on 2016-05-16 21:44:52. */
use \Logger as Logger;
use \Exception as Exception;
use \Metadata as Metadata;
/* {useStatements} */

class LocalizedTest extends PHPUnit_Framework_TestCase
{
	static $previousDebugMode;
    public static function setUpBeforeClass()
    {
    	// this will prevent the localizer from trying to create any non-existant keys used in testing
    	LocalizedTest::$previousDebugMode = Config::Get("Debug/localized");
		Config::instance()->setValue("Debug/localized", false) || die("Failed to change the configured localized debug mode");
    }

    public static function tearDownAfterClass()
    {
		Config::instance()->setValue("Debug/localized", LocalizedTest::$previousDebugMode)
			|| die("Failed to change the configured localized debug mode");
    }

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
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:44:52.
	 */
	public function testInstance()
	{
		$en1 = Localized::instance();
		$en2 = Localized::instance();
		$this->assertTrue( $en1 === $en2, "multiple instances should be singletons" );
	}

	/**
	 * @covers	HasLanguage
	 * 			T_FUNCTION T_STATIC T_PUBLIC HasLanguage ( $lang = 'en')
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:44:52.
	 */
	public function testHasLanguage()
	{
		// for sure we have 'en'
		$this->assertTrue( Localized::HasLanguage( 'en' ), "Missing english localization" );

		// for sure we dont have have 'xx'
		$this->assertFalse( Localized::HasLanguage( 'xx' ), "Where did 'xx' localization appear" );
	}

	/**
	 * @covers	Get
	 * 			T_FUNCTION T_STATIC T_PUBLIC Get ( )
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:44:52.
	 */
	public function testGet()
	{
		$key = array( "Model", "users", "name", "label" );
		$value = Localized::Get( $key );
		$this->assertNotNull($value, "Failed to find value for ". implode('/', $key));

		$key = array( "No", "key", "will", "match" );
		$value = Localized::Get( $key );
		$this->assertNull($value, "Found value for ". implode('/', $key));
	}

	/**
	 * @covers	GlobalLabel
	 * 			T_FUNCTION T_STATIC T_PUBLIC GlobalLabel ( )
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:44:52.
	 */
	public function testGlobalLabel()
	{
		// this is simply inserting a key value of 'GLOBAL' as a prefix
		$key = array( "SaveButton" );
		$value = Localized::GlobalLabel( $key );
		$this->assertNotNull($value, "Failed to find value for ". implode('/', $key));

		$key = array( "No", "key", "will", "match" );
		$value = Localized::GlobalLabel( $key );
		$this->assertNull($value, "Found value for ". implode('/', $key));
	}

	/**
	 * @covers	ModelLabel
	 * 			T_FUNCTION T_STATIC T_PUBLIC ModelLabel ( $table, $attr)
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:44:52.
	 */
	public function testModelLabel()
	{
		// this is simply inserting a key value of 'MODEL' as a prefix, and 'label' as a suffix
		$value = Localized::ModelLabel( "users", "name" );
		$this->assertNotNull($value, "Failed to find value for users/name");

		$value = Localized::ModelLabel( "NoTable", "key" );
		$this->assertNull($value, "Found value for NoTable/key");
	}

	/**
	 * @covers	ModelValidation
	 * 			T_FUNCTION T_STATIC T_PUBLIC ModelValidation ( $table, $attr, $validate = 'validation')
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:44:52.
	 */
	public function testModelValidation()
	{
		// this is simply inserting a key value of 'MODEL' as a prefix, and 'FIELD_EMPTY' as a suffix
		$value = Localized::ModelValidation( "users", "name", "FIELD_EMPTY" );
		$this->assertNotNull($value, "Failed to find value for users/name/FIELD_EMPTY");

		$value = Localized::ModelValidation( "NoTable", "key", "FIELD_EMPTY" );
		$this->assertNull($value, "Found value for NoTable/key/FIELD_EMPTY");
	}

	/**
	 * @covers	ModelRestriction
	 * 			T_FUNCTION T_STATIC T_PUBLIC ModelRestriction ( $table, $attr)
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:44:52.
	 */
	public function testModelRestriction()
	{
		// this is simply inserting a key value of 'MODEL' as a prefix, and 'restriction' as a suffix
		$value = Localized::ModelRestriction( "users", "name" );
		$this->assertNotNull($value, "Failed to find value for users/name");

		$value = Localized::ModelRestriction( "NoTable", "key" );
		$this->assertNull($value, "Found value for NoTable/key");
	}

	/**
	 * @covers	ModelSearch
	 * 			T_FUNCTION T_STATIC T_PUBLIC ModelSearch ( $table, $attr, $search = 'search')
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:44:52.
	 */
	public function testModelSearch()
	{
		// this is simply inserting a key value of 'MODEL' as a prefix, and 'search' as a suffix
		$value = Localized::ModelSearch( "series", "name" );
		$this->assertNotNull($value, "Failed to find value for series/name");

		$value = Localized::ModelSearch( "NoTable", "key" );
		$this->assertNull($value, "Found value for NoTable/key");
	}

	/**
	 * @covers	getValue
	 * 			T_FUNCTION T_PUBLIC getValue ( $key)
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:44:52.
	 */
	public function testGetValue()
	{
		$local = Localized::instance();
		$value = $local->getValue( "Model", "users", "name", "label" );
		$this->assertNotNull($value, "Failed to find value for Model/users/name/label");

		$value = $local->getValue( "NoTable", "key" );
		$this->assertNull($value, "Found value for NoTable/key");
	}


/* {functions} */
}
