<?php
/**
 * DO NOT EDIT CUSTOM MARKERS
 * Generated from Class.tpl by PhpTestClassGenerator.php on 2016-06-14 17:28:40.
 * https://phpunit.de/manual/current/en/appendixes.assertions.html
 */

namespace processor;

use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;
/* * Generated from UseStatements.tpl by PhpTestClassGenerator.php on 2016-06-14 17:28:40. */
use \Processor as Processor;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \SimpleXMLElement as SimpleXMLElement;
use \model\Endpoint_Type as Endpoint_Type;
use \model\Endpoint as Endpoint;
use \model\EndpointDBO as EndpointDBO;
/* {useStatements} */

class PreviewsWorldImporterTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
		test_initializeDatabase(true);
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
		$type != null || die( "Could not create 'test' endpoint type for PreviewsWorldImporterTest " . var_export($errors, true) );

		$testEndpoint = array(
			"name" => "PreviewsWorldImporterTest",
			"type_id" => $type->id,
			"base_url" => "http://localhost/" . TEST_RESOURCE_PATH,
			"api_key" => "txt",
			"username" => '',
			"enabled" => Model::TERTIARY_TRUE,
			"compressed" => Model::TERTIARY_FALSE
		);
		list($endpoint, $errors) = Model::Named( "Endpoint" )->createObject($testEndpoint);
		$endpoint != null || die( "Could not create 'test' endpoint type for PreviewsWorldImporterTest " . var_export($errors, true)  );

		$dict = array("endpoint_type_id" => $type->id);
		$notQual = \db\Qualifier::NotEquals( "endpoint_type_id", $type->id);
		$update = \SQL::Update(Model::Named("Pull_List_Expansion"), $notQual, $dict);
		$update->commitTransaction();
		$update = \SQL::Update(Model::Named("Pull_List_Exclusion"), $notQual, $dict);
		$update->commitTransaction();
   }

    public static function tearDownAfterClass()
    {
    }

    protected function setUp()
    {
		$this->importer = new PreviewsWorldImporter( basename(__file__) );
    	$this->assertNotNull( $this->importer, "Could not find 'PreviewsWorldImporter'" );
    }

    protected function tearDown()
    {
    }

/*	 Test functions */

	/**
	 * @covers	setEndpoint
	 * 			T_FUNCTION T_PUBLIC setEndpoint ( EndpointDBO $point = null)
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-14 17:28:40.
	 */
	public function testSetEndpoint()
	{
		$endpointArray = Model::Named('Endpoint')->allForTypeCode( "Test" );
		$this->assertCount( 1, $endpointArray );
		$this->importer->setEndpoint($endpointArray[0]);
		$this->assertNotNull( $this->importer->endpoint(), "Could not find 'PreviewsWorldImporter' endpoint" );
	}

	/**
	 * @covers	processData
	 * 			T_FUNCTION T_PUBLIC processData ( )
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-14 17:28:40.
	 */
	public function testProcessData()
	{
		$endpointArray = Model::Named('Endpoint')->allForTypeCode( "Test" );
		$this->assertCount( 1, $endpointArray );
		$this->importer->setEndpoint($endpointArray[0]);
		$this->assertNotNull( $this->importer->endpoint(), "Could not find 'PreviewsWorldImporter' endpoint" );

		$this->importer->processData();

		$pl_count = Model::Named('Pull_List')->countForKeyValue();
		$this->assertEquals( 1, $pl_count, "Should be 1 pull_list record" );

		$plg_count = Model::Named('Pull_List_Group')->countForKeyValue();
		$this->assertEquals( 7, $plg_count, "Should be 7 pull_list_group record" );

		$pli_count = Model::Named('Pull_List_Item')->countForKeyValue();
		$this->assertEquals( 136, $pli_count, "Should be 7 pull_list_item record" );

		// run process again, should not insert anything
		$endpointArray = Model::Named('Endpoint')->allForTypeCode( "Test" );
		$this->assertCount( 1, $endpointArray );
		$this->importer->setEndpoint($endpointArray[0]);
		$this->assertNotNull( $this->importer->endpoint(), "Could not find 'PreviewsWorldImporter' endpoint" );

		$this->importer->processData();

		$pl_count = Model::Named('Pull_List')->countForKeyValue();
		$this->assertEquals( 1, $pl_count, "Should be 1 pull_list record" );

		$plg_count = Model::Named('Pull_List_Group')->countForKeyValue();
		$this->assertEquals( 7, $plg_count, "Should be 7 pull_list_group record" );

		$pli_count = Model::Named('Pull_List_Item')->countForKeyValue();
		$this->assertEquals( 136, $pli_count, "Should be 7 pull_list_item record" );
	}


/* {functions} */
}
