<?php
/**
 * DO NOT EDIT CUSTOM MARKERS
 * Generated from Class.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
 */

namespace processor;

use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;
/* * Generated from UseStatements.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01. */
use \Processor as Processor;
use \Migrator as Migrator;
use \Config as Config;
use \Logger as Logger;
use \Exception as Exception;
use \Model as Model;
use \processor\EndpointImporter as EndpointImporter;
use \connectors\ComicVineConnector as ComicVineConnector;
use \model\Users as Users;
use \model\Publisher as Publisher;
use \model\Character as Character;
use \model\Series as Series;
use \model\Publication as Publication;
use \model\Endpoint as Endpoint;
use \model\Endpoint_Type as Endpoint_Type;
use \model\EndpointDBO as EndpointDBO;
use \model\Story_Arc as Story_Arc;
use \model\Story_Arc_Character as Story_Arc_Character;
use \model\Story_Arc_Series as Story_Arc_Series;
/* {useStatements} */

class ComicVineImporterTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

/*	 Test functions */

	/**
	 * @covers	setEndpoint
	 * 			T_FUNCTION T_PUBLIC setEndpoint ( EndpointDBO $point = null)
	 * @todo	Implement testSetEndpoint().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testSetEndpoint()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	connection
	 * 			T_FUNCTION T_PUBLIC connection ( )
	 * @todo	Implement testConnection().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testConnection()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	descriptionForRecord
	 * 			T_FUNCTION T_PUBLIC descriptionForRecord ( $cvRecord)
	 * @todo	Implement testDescriptionForRecord().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testDescriptionForRecord()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	convert_gender
	 * 			T_FUNCTION T_PUBLIC convert_gender ( $code = "3")
	 * @todo	Implement testConvert_gender().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testConvert_gender()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	convert_desc
	 * 			T_FUNCTION T_PUBLIC convert_desc ( $desc = null)
	 * @todo	Implement testConvert_desc().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testConvert_desc()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	convert_pub_date
	 * 			T_FUNCTION T_PUBLIC convert_pub_date ( $coverDate = null)
	 * @todo	Implement testConvert_pub_date().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testConvert_pub_date()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	convert_pub_count
	 * 			T_FUNCTION T_PUBLIC convert_pub_count ( $pub_count = null)
	 * @todo	Implement testConvert_pub_count().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testConvert_pub_count()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	convert_start_year
	 * 			T_FUNCTION T_PUBLIC convert_start_year ( $start_year = null)
	 * @todo	Implement testConvert_start_year().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testConvert_start_year()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	importMap_publisher
	 * 			T_FUNCTION T_PUBLIC importMap_publisher ( )
	 * @todo	Implement testImportMap_publisher().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testImportMap_publisher()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	importMap_character
	 * 			T_FUNCTION T_PUBLIC importMap_character ( )
	 * @todo	Implement testImportMap_character().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testImportMap_character()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	importMap_series
	 * 			T_FUNCTION T_PUBLIC importMap_series ( )
	 * @todo	Implement testImportMap_series().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testImportMap_series()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	importMap_publication
	 * 			T_FUNCTION T_PUBLIC importMap_publication ( )
	 * @todo	Implement testImportMap_publication().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testImportMap_publication()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	importMap_story_arc
	 * 			T_FUNCTION T_PUBLIC importMap_story_arc ( )
	 * @todo	Implement testImportMap_story_arc().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testImportMap_story_arc()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	preprocessRelationship
	 * 			T_FUNCTION T_PUBLIC preprocessRelationship ( $model = null, $path = "error", $cvData, $map, $forceMeta = false, $forceImages = false)
	 * @todo	Implement testPreprocessRelationship().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testPreprocessRelationship()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	preprocess_publisher
	 * 			T_FUNCTION T_PUBLIC preprocess_publisher ( $metaRecord)
	 * @todo	Implement testPreprocess_publisher().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testPreprocess_publisher()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	preprocess_character
	 * 			T_FUNCTION T_PUBLIC preprocess_character ( $metaRecord)
	 * @todo	Implement testPreprocess_character().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testPreprocess_character()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	preprocess_series
	 * 			T_FUNCTION T_PUBLIC preprocess_series ( $metaRecord)
	 * @todo	Implement testPreprocess_series().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testPreprocess_series()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	preprocess_story_arc
	 * 			T_FUNCTION T_PUBLIC preprocess_story_arc ( $metaRecord)
	 * @todo	Implement testPreprocess_story_arc().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testPreprocess_story_arc()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	preprocess_publication
	 * 			T_FUNCTION T_PUBLIC preprocess_publication ( $metaRecord)
	 * @todo	Implement testPreprocess_publication().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testPreprocess_publication()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	finalize_publisher
	 * 			T_FUNCTION T_PUBLIC finalize_publisher ( $metaRecord)
	 * @todo	Implement testFinalize_publisher().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testFinalize_publisher()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	finalize_character
	 * 			T_FUNCTION T_PUBLIC finalize_character ( $metaRecord)
	 * @todo	Implement testFinalize_character().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testFinalize_character()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	finalize_series
	 * 			T_FUNCTION T_PUBLIC finalize_series ( $metaRecord)
	 * @todo	Implement testFinalize_series().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testFinalize_series()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	finalize_story_arc
	 * 			T_FUNCTION T_PUBLIC finalize_story_arc ( $metaRecord)
	 * @todo	Implement testFinalize_story_arc().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testFinalize_story_arc()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	finalize_publication
	 * 			T_FUNCTION T_PUBLIC finalize_publication ( $metaRecord)
	 * @todo	Implement testFinalize_publication().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testFinalize_publication()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	refreshPublicationsForObject
	 * 			T_FUNCTION refreshPublicationsForObject ( $object = null)
	 * @todo	Implement testRefreshPublicationsForObject().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testRefreshPublicationsForObject()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}


/* {functions} */
}
