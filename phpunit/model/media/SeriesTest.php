<?php
/**
 * DO NOT EDIT CUSTOM MARKERS
 * Generated from Class.tpl by PhpTestClassGenerator.php on 2016-06-22 10:33:00.
 * https://phpunit.de/manual/current/en/appendixes.assertions.html
 */

namespace model\media;

use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;
/* * Generated from UseStatements.tpl by PhpTestClassGenerator.php on 2016-06-22 10:33:00. */
use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \model\media\SeriesDBO as SeriesDBO;
use \model\media\Series_Alias as Series_Alias;
use \model\media\Series_AliasDBO as Series_AliasDBO;
use \model\media\Publisher as Publisher;
use \model\media\PublisherDBO as PublisherDBO;
/* * Generated from UseStatements.tpl by PhpTestClassGenerator.php on 2016-06-22 20:44:16. */
use \model\media\Publication as Publication;
use \model\media\PublicationDBO as PublicationDBO;
use \model\media\Series_Character as Series_Character;
use \model\media\Series_CharacterDBO as Series_CharacterDBO;
use \model\media\Story_Arc_Series as Story_Arc_Series;
use \model\media\Story_Arc_SeriesDBO as Story_Arc_SeriesDBO;
use \model\media\User_Series as User_Series;
use \model\media\User_SeriesDBO as User_SeriesDBO;
/* {useStatements} */

class SeriesTest extends PHPUnit_Framework_TestCase
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
    	$this->model = Model::Named('Series');
    	$this->assertNotNull( $this->model, "Could not find 'Series' model" );
    }

    protected function tearDown()
    {
    }

/*	 Test functions */

	/**
	 * @covers	createObject
	 * 			T_FUNCTION T_PUBLIC createObject ( $values)
	 * @todo	Implement testCreateObject().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-22 10:33:00.
	 */
	public function testCreateObject()
	{
		$publisher = Model::Named('Publisher')->objectForId( 4 );

		$values = array(
			"publisher" => $publisher,
			Series::name => 'Test Series',
		);
		list($seriesDBO, $errors) = $this->model->createObject($values);
		$this->assertNull( $errors, "Failed to create new record" );
		$this->assertTrue( $seriesDBO != false, "Failed to create new record" );
		$this->assertEquals( $publisher, $seriesDBO->publisher(), "Publisher not set" );

		$publisher = Model::Named('Publisher')->objectForId( 1 );
		$values = array(
			"publisher" => $publisher,
			Series::name => 'test ' . $publisher->name,
			Series::desc => 'this is a test series',
			Series::start_year => 2012,
			Series::issue_count => 10,
			Series::pub_active => false,
			Series::pub_count => 10,
			Series::pub_available => 10,
			Series::pub_wanted => false
		);
		list($seriesDBO, $errors) = $this->model->createObject($values);
		$this->assertNull( $errors, "Failed to create new record" );
		$this->assertTrue( $seriesDBO != false, "Failed to create new record" );
		$this->assertEquals( $publisher, $seriesDBO->publisher(), "Publisher not set" );

		$this->assertEquals( 'test dc comics', $seriesDBO->search_name(), "search_name not set" );
	}

	/**
	 * @covers	updateObject
	 * 			T_FUNCTION T_PUBLIC updateObject ( DataObject $object = null, $values)
	 * @depends	testCreateObject
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-22 10:33:00.
	 */
	public function testUpdateObject()
	{
		$publisher = Model::Named('Publisher')->objectForId( 1 );
		$allSeries = $this->model->allForPublisher($publisher);
		$this->assertTrue( $allSeries != false, "Failed to find series" );
		$this->assertCount( 3, $allSeries, "Failed to find series" );

		$orig_seriesDBO = $allSeries[0];
		list($updated, $errors) = $this->model->updateObject( $orig_seriesDBO, array(
			Series::name => "A New Series Name",
			Series::xid => uuid(),
			Series::xsource => "ComicVine",
			Series::xurl => "http://localhost/series/12345"
			)
		);
		$this->assertNull( $errors, "Failed to update record" );
		$this->assertTrue( $updated != false, "Failed to update record" );
		$this->assertEquals( $publisher, $updated->publisher(), "Publisher not set" );
		$this->assertEquals( "A New Series Name", $updated->name(), "Series name not updated" );
		$this->assertEquals( "a new series name", $updated->search_name(), "Series search_name not updated" );

		$orig_seriesDBO = $updated;
		list($updated, $errors) = $this->model->updateObject( $orig_seriesDBO, array(
			Series::name => "Twisted again",
			Series::search_name => "User Override"
			)
		);
		$this->assertNull( $errors, "Failed to update record" );
		$this->assertTrue( $updated != false, "Failed to update record" );
		$this->assertEquals( "Twisted again", $updated->name(), "Series name not updated" );
		$this->assertEquals( "User Override", $updated->search_name(), "Series search_name not updated" );



	}

/*  Test functions */

	/**
	 * @covers	findExternalOrCreate
	 * 			T_FUNCTION T_PUBLIC findExternalOrCreate ( $publishObj = null, $name, $year, $count, $xid, $xsrc, $xurl = null, $desc = null, $aliases = null)
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-22 20:44:16.
	 */
	public function testFindExternalOrCreate()
	{
		$publisher = Model::Named('Publisher')->objectForId( 1 );
		$newobj = $this->model->findExternalOrCreate (
			$publisher,
			"test external",
			2016,
			10,
			"ABC-123",
			"ComicVine",
			"http://localhost/series/ABC-123",
			"this is a description of the test series",
			array("test alias 1", "test alias 2")
		);
		$this->assertTrue( $newobj instanceof SeriesDBO, "Failed to create series " . var_export($newobj, true) );

	}

	/**
	 * @covers	updateStatistics
	 * 			T_FUNCTION T_PUBLIC updateStatistics ( $xid, $xsource = null)
	 * @todo	Implement testUpdateStatistics().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-22 20:44:16.
	 */
	public function testUpdateStatistics()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}


/* {functions} */
}
