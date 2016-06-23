<?php
/**
 * DO NOT EDIT CUSTOM MARKERS
 * Generated from Class.tpl by PhpTestClassGenerator.php on 2016-06-22 20:42:26.
 * https://phpunit.de/manual/current/en/appendixes.assertions.html
 */

namespace model\media;

use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;
/* * Generated from UseStatements.tpl by PhpTestClassGenerator.php on 2016-06-22 20:42:26. */
use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \model\media\MediaDBO as MediaDBO;
use \model\media\Media_Type as Media_Type;
use \model\media\Media_TypeDBO as Media_TypeDBO;
use \model\media\Publication as Publication;
use \model\media\PublicationDBO as PublicationDBO;
/* {useStatements} */

class MediaTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
		test_initializeDatabase(true);
		test_importTestData( array(
			"endpoint",
			"publisher",
			"character",
			"character_alias",
			"series",
			"series_alias",
			"series_character",
			"publication",
			"publication_character",
			"story_arc",
			"story_arc_character",
			"story_arc_publication",
			"story_arc_series",
			"user_series",
			"users"
			)
		);
    }

    public static function tearDownAfterClass()
    {
    }

    protected function setUp()
    {
    	$this->model = Model::Named('Media');
    	$this->assertNotNull( $this->model, "Could not find 'Media' model" );
    }

    protected function tearDown()
    {
    }

/*	 Test functions */

	/**
	 * @covers	createObject
	 * 			T_FUNCTION T_PUBLIC createObject ( $values)
	 * @todo	Implement testCreateObject().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-22 20:42:26.
	 */
	public function testCreateObject()
	{
		$mediaType = Model::Named('Media_Type')->objectForCode( "cbz" );
		$this->assertNotNull( $mediaType, "Failed to find mediaType" );
		$publication = Model::Named('Publication')->objectForId( 2 );
		$this->assertNotNull( $publication, "Failed to find publication" );

		$values = array(
			"publication" => $publication,
			"mediaType" => $mediaType,
			Media::original_filename => "Batman Beyond 001 (2012) v12.cbz",
			Media::checksum => md5_file(__FILE__),
			Media::size => filesize(__FILE__)
		);
		list($mediaDBO, $errors) = $this->model->createObject($values);
		$this->assertNull( $errors, "Failed to create new record" );
		$this->assertTrue( $mediaDBO != false, "Failed to create new record" );
		$this->assertEquals( $publication, $mediaDBO->publication(), "publication not set" );
		$this->assertEquals( "Nightwing - 2 - 2014.cbz", $mediaDBO->filename(), "filename not set" );
	}

	/**
	 * @covers	updateObject
	 * 			T_FUNCTION T_PUBLIC updateObject ( DataObject $object = null, $values)
	 * @todo	Implement testUpdateObject().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-22 20:42:26.
	 */
	public function testUpdateObject()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	mostRecent
	 * 			T_FUNCTION T_PUBLIC mostRecent ( $limit)
	 * @todo	Implement testMostRecent().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-22 20:42:26.
	 */
	public function testMostRecent()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}


/* {functions} */
}
