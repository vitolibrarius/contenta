<?php
/**
 * DO NOT EDIT CUSTOM MARKERS
 * Generated from Class.tpl by PhpTestClassGenerator.php on 2016-06-08 20:50:05.
 * https://phpunit.de/manual/current/en/appendixes.assertions.html
 */

namespace processor;

use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;
/* * Generated from UseStatements.tpl by PhpTestClassGenerator.php on 2016-06-08 20:50:05. */
use \Processor as Processor;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \SimpleXMLElement as SimpleXMLElement;
use \model\Endpoint_Type as Endpoint_Type;
use \model\Endpoint as Endpoint;
use \model\EndpointDBO as EndpointDBO;
use \model\network\RssDBO as RssDBO;
/* {useStatements} */

class RSSImporterTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
		test_initializeDatabase(true);
// 		test_importTestData( array( "Endpoint", "Rss" ) );
		$testEndpointType = array(
		    "name" => "Test Endpoint Type",
			"code" => "Test",
			"data_type" => "Test",
			"site_url" => "http://localhost",
			"api_url" => "http://localhost",
			"favicon_url" => "http://localhost/favicon.ico",
			"comments" => "Test endpoints retreive data from the phpunit resources",
			"throttle_hits" => "1",
			"throttle_time" => "2"
		);
		list($type, $errors) = Model::Named( "Endpoint_Type" )->createObject($testEndpointType);
		$type != null || die( "Could not create 'test' endpoint type for RSSImporterTest " . var_export($errors, true) );

		$testEndpoint = array(
			"name" => "RSSImporterTest",
			"type_id" => $type->id,
			"base_url" => "http://localhost/" . TEST_RESOURCE_PATH,
			"api_key" => "xml",
			"username" => '',
			"enabled" => Model::TERTIARY_TRUE,
			"compressed" => Model::TERTIARY_FALSE
		);
		list($endpoint, $errors) = Model::Named( "Endpoint" )->createObject($testEndpoint);
		$endpoint != null || die( "Could not create 'test' endpoint type for RSSImporterTest " . var_export($errors, true)  );
    }

    public static function tearDownAfterClass()
    {
// 		test_exportTestData( "RSSImporterTest", array( ) );
    }

    protected function setUp()
    {
		$this->importer = new RSSImporter( basename(__file__) );
    	$this->assertNotNull( $this->importer, "Could not find 'RssImporter'" );
    }

    protected function tearDown()
    {
    }

/*	 Test functions */

	/**
	 * @covers	setEndpoint
	 * 			T_FUNCTION T_PUBLIC setEndpoint ( EndpointDBO $point = null)
	 * @todo	Implement testSetEndpoint().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-08 20:50:05.
	 */
	public function testSetEndpoint()
	{
		$endpointArray = Model::Named('Endpoint')->allForTypeCode( "Test" );
		$this->assertCount( 1, $endpointArray );
		$this->importer->setEndpoint($endpointArray[0]);
		$this->assertNotNull( $this->importer->endpoint(), "Could not find 'RssImporter' endpoint" );
	}

	/**
	 * @covers	processData
	 * 			T_FUNCTION T_PUBLIC processData ( )
	 * @todo	Implement testProcessData().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-08 20:50:05.
	 */
	public function testProcessData()
	{
		$endpointArray = Model::Named('Endpoint')->allForTypeCode( "Test" );
		$this->assertCount( 1, $endpointArray );
		$this->importer->setEndpoint($endpointArray[0]);
		$this->assertNotNull( $this->importer->endpoint(), "Could not find 'RssImporter' endpoint" );

		$this->importer->processData();
	}


/* {functions} */
}
