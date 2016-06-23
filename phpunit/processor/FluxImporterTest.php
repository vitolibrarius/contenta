<?php
/**
 * DO NOT EDIT CUSTOM MARKERS
 * Generated from Class.tpl by PhpTestClassGenerator.php on 2016-06-17 09:57:08.
 * https://phpunit.de/manual/current/en/appendixes.assertions.html
 */

namespace processor;

use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;
/* * Generated from UseStatements.tpl by PhpTestClassGenerator.php on 2016-06-17 09:57:08. */
use \Processor as Processor;
use \Migrator as Migrator;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \Exception as Exception;
use \model\user\Users as Users;
use \model\network\Flux as Flux;
use \model\network\FluxDBO as FluxDBO;
use \model\network\RssDBO as RssDBO;
use \model\media\Publisher as Publisher;
use \model\media\Character as Character;
use \model\media\Series as Series;
use \model\media\Publication as Publication;
use \model\media\PublicationDBO as PublicationDBO;
use \model\network\Endpoint as Endpoint;
use \model\network\Endpoint_Type as Endpoint_Type;
use \model\network\EndpointDBO as EndpointDBO;
use \model\media\Story_Arc as Story_Arc;
use \model\media\Story_Arc_Character as Story_Arc_Character;
use \model\media\Story_Arc_Series as Story_Arc_Series;
use \connectors\NewznabConnector as NewznabConnector;
use \processor\NewznabSearchProcessor as NewznabSearchProcessor;
/* {useStatements} */

class FluxImporterTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
		test_initializeDatabase(true);
		$sab_EndpointType = array(
		    "name" => "Test Endpoint Type",
			"code" => "SabnzbdTest",
			"data_type" => "Test",
			"site_url" => "http://localhost",
			"api_url" => "http://localhost",
			"favicon_url" => "http://localhost/favicon.ico",
			"comments" => "Test endpoints retreive data from the phpunit resources",
			"throttle_hits" => "1",
			"throttle_time" => "2"
		);
		list($type, $errors) = Model::Named( "Endpoint_Type" )->createObject($sab_EndpointType);
		$type != null || die( "Could not create 'sab' endpoint type for FluxImporterTest " . var_export($errors, true) );

		$sab_Endpoint = array(
			"name" => "FluxImporterTest-Sabnzbd",
			"type_id" => $type->id,
			"base_url" => "http://localhost/" . TEST_RESOURCE_PATH,
			"api_key" => "xml",
			"username" => '',
			"enabled" => Model::TERTIARY_TRUE,
			"compressed" => Model::TERTIARY_FALSE
		);
		list($endpoint, $errors) = Model::Named( "Endpoint" )->createObject($sab_Endpoint);
		$endpoint != null || die( "Could not create 'test' endpoint for FluxImporterTest " . var_export($errors, true)  );
    }

    public static function tearDownAfterClass()
    {
    }

    protected function setUp()
    {
		$this->importer = new FluxImporter( basename(__file__) );
    	$this->assertNotNull( $this->importer, "Could not find 'FluxImporter'" );
    }

    protected function tearDown()
    {
    }

/*	 Test functions */

	/**
	 * @covers	setEndpoint
	 * 			T_FUNCTION T_PUBLIC setEndpoint ( EndpointDBO $point = null)
	 * @todo	Implement testSetEndpoint().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-17 09:57:08.
	 */
	public function testSetEndpoint()
	{
		$endpointArray = Model::Named('Endpoint')->allForTypeCode( "SabnzbdTest" );
		$this->assertCount( 1, $endpointArray );
		$this->importer->setEndpoint($endpointArray[0]);
		$this->assertNotNull( $this->importer->endpoint(), "Could not find 'RssImporter' endpoint" );
	}

	/**
	 * @covers	importFluxRSS
	 * 			T_FUNCTION T_PUBLIC importFluxRSS ( RssDBO $rss = null)
	 * @todo	Implement testImportFluxRSS().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-17 09:57:08.
	 */
	public function testImportFluxRSS()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	importFluxValues
	 * 			T_FUNCTION T_PUBLIC importFluxValues ( EndpointDBO $endpoint = null, $name = null, $guid = null, $publishedDate = null, $url = null)
	 * @todo	Implement testImportFluxValues().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-17 09:57:08.
	 */
	public function testImportFluxValues()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	importFlux
	 * 			T_FUNCTION T_PUBLIC importFlux ( FluxDBO $flux = null)
	 * @todo	Implement testImportFlux().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-17 09:57:08.
	 */
	public function testImportFlux()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	findPostingsForPublication
	 * 			T_FUNCTION T_PUBLIC findPostingsForPublication ( PublicationDBO $publication, NewznabConnector $nzbSearch)
	 * @todo	Implement testFindPostingsForPublication().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-17 09:57:08.
	 */
	public function testFindPostingsForPublication()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	processData
	 * 			T_FUNCTION T_PUBLIC processData ( )
	 * @todo	Implement testProcessData().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-17 09:57:08.
	 */
	public function testProcessData()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}


/* {functions} */
}
