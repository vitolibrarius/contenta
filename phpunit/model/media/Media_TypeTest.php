<?php
/**
 * DO NOT EDIT CUSTOM MARKERS
 * Generated from Class.tpl by PhpTestClassGenerator.php on 2016-06-19 09:31:40.
 * https://phpunit.de/manual/current/en/appendixes.assertions.html
 */

namespace model\media;

use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;
/* * Generated from UseStatements.tpl by PhpTestClassGenerator.php on 2016-06-19 09:31:40. */
use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \model\media\Media_TypeDBO as Media_TypeDBO;
/* {useStatements} */

class Media_TypeTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
		test_initializeDatabase(true);
		test_importTestData( array( "Media_Type" ) );
    }

    public static function tearDownAfterClass()
    {
    }

    protected function setUp()
    {
    	$this->model = Model::Named('Media_Type');
    	$this->assertNotNull( $this->model, "Could not find 'Media_Type' model" );
    }

    protected function tearDown()
    {
    }

/*	 Test functions */

	/**
	 * @covers	cbz
	 * 			T_FUNCTION T_PUBLIC cbz ( )
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-19 09:31:40.
	 */
	public function testCbz()
	{
		$cbz = $this->model->cbz();
		$this->assertTrue( $cbz != false, "Failed to find Media_Type (cbz)" );
	}

	/**
	 * @covers	createObject
	 * 			T_FUNCTION T_PUBLIC createObject ( $values)
	 * @todo	Implement testCreateObject().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-19 09:31:40.
	 */
	public function testCreateObject()
	{
		list($type, $errors) = $this->model->createObject( array( "code" => "test", "name" => "Test Media Type" ));
		$this->assertNull( $errors, "Failed to create new record" );
		$this->assertTrue( $type != false, "Failed to create new record" );
	}

	/**
	 * @covers	updateObject
	 * 			T_FUNCTION T_PUBLIC updateObject ( DataObject $object = null, $values)
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-19 09:31:40.
	 */
	public function testUpdateObject()
	{
		$type = $this->model->objectForCode( "pdf" );
		$this->assertTrue( $type != false, "Failed to find 'pdf'" );
		list( $updated, $errors ) = $this->model->updateObject( $type, array( "name" => null));
		$this->assertNotNull( $errors, "Failed to updated record" . var_export($errors, true) );
		$this->assertCount( 1, $errors, "Should be validation error" );
	}


/* {functions} */
}
