<?php
/**
 * DO NOT EDIT CUSTOM MARKERS
 * Generated from Class.tpl by PhpTestClassGenerator.php on 2016-06-22 20:44:30.
 * https://phpunit.de/manual/current/en/appendixes.assertions.html
 */

namespace model\media;

use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;
/* * Generated from UseStatements.tpl by PhpTestClassGenerator.php on 2016-06-22 20:44:30. */
use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \model\media\PublicationDBO as PublicationDBO;
use \model\media\Series as Series;
use \model\media\SeriesDBO as SeriesDBO;
use \model\media\Media as Media;
use \model\media\MediaDBO as MediaDBO;
use \model\media\Story_Arc_Publication as Story_Arc_Publication;
use \model\media\Story_Arc_PublicationDBO as Story_Arc_PublicationDBO;
use \model\media\Publication_Character as Publication_Character;
use \model\media\Publication_CharacterDBO as Publication_CharacterDBO;
/* {useStatements} */

class PublicationTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
		test_initializeDatabase(true);
		test_importTestData( array( "Publisher", "Series" ) );
    }

    public static function tearDownAfterClass()
    {
    }

    protected function setUp()
    {
    	$this->model = Model::Named('Publication');
    	$this->assertNotNull( $this->model, "Could not find 'Publication' model" );
    }

    protected function tearDown()
    {
    }

/*	 Test functions */

	/**
	 * @covers	createObject
	 * 			T_FUNCTION T_PUBLIC createObject ( $values)
	 * @todo	Implement testCreateObject().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-22 20:44:30.
	 */
	public function testCreateObject()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	updateObject
	 * 			T_FUNCTION T_PUBLIC updateObject ( DataObject $object = null, $values)
	 * @todo	Implement testUpdateObject().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-22 20:44:30.
	 */
	public function testUpdateObject()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	findExternalOrCreate
	 * 			T_FUNCTION T_PUBLIC findExternalOrCreate ( $series = null, $name, $desc, $issue_num = null, $xid, $xsrc, $xurl = null)
	 * @todo	Implement testFindExternalOrCreate().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-22 20:44:30.
	 */
	public function testFindExternalOrCreate()
	{
		$series = Model::Named('Series')->objectForId( 1 );
		$newobj = $this->model->findExternalOrCreate (
			$series,
			"test external",
			"this is a description of the test publication",
			12,
			"QWR-123",
			"ComicVine",
			"http://localhost/publication/QWR-123"
		);
		$this->assertTrue( $newobj instanceof PublicationDBO, "Failed to create publication " . var_export($newobj, true) );
	}

	/**
	 * @covers	allObjectsNeedingExternalUpdate
	 * 			T_FUNCTION T_PUBLIC allObjectsNeedingExternalUpdate ( $limit)
	 * @todo	Implement testAllObjectsNeedingExternalUpdate().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-22 20:44:30.
	 */
	public function testAllObjectsNeedingExternalUpdate()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	publicationsLike
	 * 			T_FUNCTION T_PUBLIC publicationsLike ( $seriesName = '', $issue = null, $year = null)
	 * @todo	Implement testPublicationsLike().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-22 20:44:30.
	 */
	public function testPublicationsLike()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	activePublicationsForSeries
	 * 			T_FUNCTION T_PUBLIC activePublicationsForSeries ( model SeriesDBO $obj = null)
	 * @todo	Implement testActivePublicationsForSeries().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-22 20:44:30.
	 */
	public function testActivePublicationsForSeries()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}


/* {functions} */
}
