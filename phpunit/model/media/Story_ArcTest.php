<?php
/**
 * DO NOT EDIT CUSTOM MARKERS
 * Generated from Class.tpl by PhpTestClassGenerator.php on 2016-06-22 20:44:37.
 * https://phpunit.de/manual/current/en/appendixes.assertions.html
 */

namespace model\media;

use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;
/* * Generated from UseStatements.tpl by PhpTestClassGenerator.php on 2016-06-22 20:44:37. */
use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \model\media\Story_ArcDBO as Story_ArcDBO;
use \model\media\Publisher as Publisher;
use \model\media\PublisherDBO as PublisherDBO;
use \model\media\Story_Arc_Characters as Story_Arc_Characters;
use \model\media\Story_Arc_CharactersDBO as Story_Arc_CharactersDBO;
use \model\media\Story_Arc_Publication as Story_Arc_Publication;
use \model\media\Story_Arc_PublicationDBO as Story_Arc_PublicationDBO;
use \model\media\Story_Arc_Series as Story_Arc_Series;
use \model\media\Story_Arc_SeriesDBO as Story_Arc_SeriesDBO;
/* {useStatements} */

class Story_ArcTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
		test_initializeDatabase(true);
		test_importTestData( array( "Publisher", "Series", "Story_Arc", "Publication" ) );
    }

    public static function tearDownAfterClass()
    {
    }

    protected function setUp()
    {
    	$this->model = Model::Named('Story_Arc');
    	$this->assertNotNull( $this->model, "Could not find 'Story_Arc' model" );
    }

    protected function tearDown()
    {
    }

/*	 Test functions */

	/**
	 * @covers	createObject
	 * 			T_FUNCTION T_PUBLIC createObject ( $values)
	 * @todo	Implement testCreateObject().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-22 20:44:37.
	 */
	public function testCreateObject()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	updateObject
	 * 			T_FUNCTION T_PUBLIC updateObject ( DataObject $object = null, $values)
	 * @todo	Implement testUpdateObject().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-22 20:44:37.
	 */
	public function testUpdateObject()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	findExternalOrCreate
	 * 			T_FUNCTION T_PUBLIC findExternalOrCreate ( $publisher = null, $name, $desc, $xid, $xsrc, $xurl = null)
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-22 20:44:37.
	 */
	public function testFindExternalOrCreate()
	{
		$publisher = Model::Named('Publisher')->objectForId( 1 );
		$newobj = $this->model->findExternalOrCreate (
			$publisher,
			"test external",
			"this is a description of the test story arc",
			"DEF-123",
			"ComicVine",
			"http://localhost/storyarc/DEF-123"
		);
		$this->assertTrue( $newobj instanceof Story_ArcDBO, "Failed to create storyarc " . var_export($newobj, true) );
	}


/* {functions} */
}
