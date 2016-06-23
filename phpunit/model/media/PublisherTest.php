<?php
/**
 * DO NOT EDIT CUSTOM MARKERS
 * Generated from Class.tpl by PhpTestClassGenerator.php on 2016-06-19 09:31:47.
 * https://phpunit.de/manual/current/en/appendixes.assertions.html
 */

namespace model\media;

use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;
/* * Generated from UseStatements.tpl by PhpTestClassGenerator.php on 2016-06-19 09:31:47. */
use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \model\media\PublisherDBO as PublisherDBO;
use \model\media\Series as Series;
use \model\media\SeriesDBO as SeriesDBO;
use \model\media\Character as Character;
use \model\media\CharacterDBO as CharacterDBO;
/* * Generated from UseStatements.tpl by PhpTestClassGenerator.php on 2016-06-22 10:33:00. */
use \model\media\Story_Arc as Story_Arc;
use \model\media\Story_ArcDBO as Story_ArcDBO;
/* {useStatements} */

class PublisherTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
		test_initializeDatabase(true);
		test_importTestData( array( "Publisher" ) );
    }

    public static function tearDownAfterClass()
    {
    }

    protected function setUp()
    {
    	$this->model = Model::Named('Publisher');
    	$this->assertNotNull( $this->model, "Could not find 'Publisher' model" );
    }

    protected function tearDown()
    {
    }

/*	 Test functions */

	/**
	 * @covers	createObject
	 * 			T_FUNCTION T_PUBLIC createObject ( $values)
	const name = 'name';
	const created = 'created';
	const xurl = 'xurl';
	const xsource = 'xsource';
	const xid = 'xid';
	const xupdated = 'xupdated';
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-19 09:31:47.
	 */
	public function testCreateObject()
	{
		list($type, $errors) = $this->model->createObject( array( "name" => "Test Publisher" ));
		$this->assertNull( $errors, "Failed to create new record" );
		$this->assertTrue( $type != false, "Failed to create new record" );

		list($type, $errors) = $this->model->createObject( array(
			"name" => "Test External Publisher",
			"xurl" => "http://localhost/publisher/123",
			"xsource" => "No Endpoint Name",
			"xid" => "123"
			)
		);
		$this->assertNull( $errors, "Failed to create new record" );
		$this->assertTrue( $type != false, "Failed to create new record" );
	}

	/**
	 * @covers	updateObject
	 * 			T_FUNCTION T_PUBLIC updateObject ( DataObject $object = null, $values)
	 * @todo	Implement testUpdateObject().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-19 09:31:47.
	 */
	public function testUpdateObject()
	{
		$publisher = $this->model->objectForExternal( "123", "No Endpoint Name" );
		$this->assertTrue( $publisher != false, "Failed to find 'Test Publisher'" );

		list( $updated, $errors ) = $this->model->updateObject( $publisher, array( "xid" => "9876"));
		$this->assertNull( $errors, "Failed to updated record" . var_export($errors, true) );
		$this->assertEquals( "9876", $updated->xid(), "Should be new xid" );
	}

/*  Test functions */

	/**
	 * @covers	attributesFor
	 * 			T_FUNCTION T_PUBLIC attributesFor ( $object = null, $type = null)
	 * @todo	Implement testAttributesFor().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-22 20:44:25.
	 */
	public function testAttributesFor()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	attributeOptions
	 * 			T_FUNCTION T_PUBLIC attributeOptions ( $object = null, $type = null, $attr)
	 * @todo	Implement testAttributeOptions().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-22 20:44:25.
	 */
	public function testAttributeOptions()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	findExternalOrCreate
	 * 			T_FUNCTION T_PUBLIC findExternalOrCreate ( $name, $xid, $xsrc, $xurl = null)
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-22 20:44:25.
	 */
	public function testFindExternalOrCreate()
	{
		$newobj = $this->model->findExternalOrCreate (
			"test external",
			"GHI-123",
			"ComicVine",
			"http://localhost/publisher/GHI-123"
		);
		$this->assertTrue( $newobj instanceof PublisherDBO, "Failed to create publisher " . var_export($newobj, true) );
	}


/* {functions} */
}
