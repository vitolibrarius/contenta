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
use \model\Series as Series;
use \model\SeriesDBO as SeriesDBO;
use \model\Character as Character;
use \model\CharacterDBO as CharacterDBO;
use \model\Story_Arc as Story_Arc;
use \model\Story_ArcDBO as Story_ArcDBO;
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

/* {functions} */
}
