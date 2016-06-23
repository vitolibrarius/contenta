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
use \model\media\Publisher as Publisher;
use \model\media\Series as Series;
use \model\media\SeriesDBO as SeriesDBO;
use \model\media\Character as Character;
use \model\media\CharacterDBO as CharacterDBO;
use \model\media\Story_Arc as Story_Arc;
use \model\media\Story_ArcDBO as Story_ArcDBO;
/* {useStatements} */

class PublisherDBOTest extends PHPUnit_Framework_TestCase
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
	 * @covers	publisher
	 * 			T_FUNCTION T_PUBLIC publisher ( )
	 * @todo	Implement testPublisher().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-22 10:33:00.
	 */
	public function testPublisher()
	{
		$allPublishers = $this->model->allObjects();
		$this->assertTrue( $allPublishers != false, "Failed to find 'Publishers'" );
		$this->assertCount( 10, $allPublishers, "Failed to find 'Test Publisher'" );

	}


/* {functions} */
}
