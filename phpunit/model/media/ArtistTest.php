<?php
/**
 * DO NOT EDIT CUSTOM MARKERS
 * Generated from Class.tpl by PhpTestClassGenerator.php on 2017-05-20 09:08:34.
 * https://phpunit.de/manual/current/en/appendixes.assertions.html
 */

namespace model\media;

use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;
/* * Generated from UseStatements.tpl by PhpTestClassGenerator.php on 2017-05-20 09:08:34. */
use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \model\media\ArtistDBO as ArtistDBO;
use \model\media\Artist_Alias as Artist_Alias;
use \model\media\Artist_AliasDBO as Artist_AliasDBO;
use \model\media\Publication_Artists as Publication_Artists;
use \model\media\Publication_ArtistsDBO as Publication_ArtistsDBO;
use \model\media\Series_Artists as Series_Artists;
use \model\media\Series_ArtistsDBO as Series_ArtistsDBO;
use \model\media\Story_Arc_Artist as Story_Arc_Artist;
use \model\media\Story_Arc_ArtistDBO as Story_Arc_ArtistDBO;
/* {useStatements} */

class ArtistTest extends PHPUnit_Framework_TestCase
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
			"users"
			)
		);
    }

    public static function tearDownAfterClass()
    {
    }

    protected function setUp()
    {
    	$this->model = Model::Named('Artist');
    	$this->assertNotNull( $this->model, "Could not find 'Artist' model" );
    }

    protected function tearDown()
    {
    }

/*	 Test functions */

	/**
	 * @covers	searchQualifiers
	 * 			T_FUNCTION T_PUBLIC searchQualifiers ( $query)
	 * @todo	Implement testSearchQualifiers().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2017-05-20 09:08:34.
	 */
	public function testSearchQualifiers()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	createObject
	 * 			T_FUNCTION T_PUBLIC createObject ( $values)
	 * @todo	Implement testCreateObject().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2017-05-20 09:08:34.
	 */
	public function testCreateObject()
	{
		$values = array(
			Artist::name => 'vito librarius',
			Artist::desc => "The most awesome artist",
			Artist::gender => 'other',
			Artist::birth_date => time() - (3600 * 365 * 25)
		);
		list($dbo, $errors) = $this->model->createObject($values);
		$this->assertNull( $errors, "Failed to create new record" );
		$this->assertTrue( $dbo != false, "Failed to create new record" );
		$this->assertEquals( "vito librarius", $dbo->name(), "name not set" );
	}

	/**
	 * @covers	updateObject
	 * 			T_FUNCTION T_PUBLIC updateObject ( DataObject $object = null, $values)
	 * @todo	Implement testUpdateObject().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2017-05-20 09:08:34.
	 */
	public function testUpdateObject()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	attributesFor
	 * 			T_FUNCTION T_PUBLIC attributesFor ( $object = null, $type = null)
	 * @todo	Implement testAttributesFor().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2017-05-20 09:08:34.
	 */
	public function testAttributesFor()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	attributeOptions
	 * 			T_FUNCTION T_PUBLIC attributeOptions ( $object = null, $type = null, $attr)
	 * @todo	Implement testAttributeOptions().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2017-05-20 09:08:34.
	 */
	public function testAttributeOptions()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}


/*  Test functions */

	/**
	 * @covers	findExternalOrCreate
	 * 			T_FUNCTION T_PUBLIC findExternalOrCreate ( $name, $desc, $gender, $birth_date, $death_date, $pub_wanted, $aliases, $xid, $xsrc, $xurl = null)
	 * @todo	Implement testFindExternalOrCreate().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2017-05-23 21:17:24.
	 */
	public function testFindExternalOrCreate()
	{
		$newobj = $this->model->findExternalOrCreate (
			"Alan Moore",
			"Story God",
			"male",
			null,
			null,
			true,
			array("Curt Vile","Jill de Ray","Translucia Baboon","The Original Writer"),
			"40382",
			"ComicVine",
			"http://localhost/person/4040-40382"
		);
		$this->assertTrue( $newobj instanceof ArtistDBO, "Failed to create artist " . var_export($newobj, true) );
	}


/* {functions} */
}
