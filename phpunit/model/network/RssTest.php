<?php
/**
 * DO NOT EDIT CUSTOM MARKERS
 * Generated from Class.tpl by PhpTestClassGenerator.php on 2016-06-08 16:58:30.
 * https://phpunit.de/manual/current/en/appendixes.assertions.html
 */

namespace model\network;

use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;
/* * Generated from UseStatements.tpl by PhpTestClassGenerator.php on 2016-06-08 16:58:30. */
use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \utilities\MediaFilename as MediaFilename;
use \model\network\RssDBO as RssDBO;
use \model\Endpoint as Endpoint;
use \model\EndpointDBO as EndpointDBO;
use \model\Flux as Flux;
use \model\FluxDBO as FluxDBO;
/* {useStatements} */

class RssTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
		test_initializeDatabase(true);
		test_importTestData( array( "Endpoint", "Rss" ) );
    }

    public static function tearDownAfterClass()
    {
    }

    protected function setUp()
    {
    	$this->model = Model::Named('Rss');
    	$this->assertNotNull( $this->model, "Could not find 'Rss' model" );
    }

    protected function tearDown()
    {
    }

/*	 Test functions */

	/**
	 * @covers	createObject
	 * 			T_FUNCTION T_PUBLIC createObject ( $values)
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-08 16:58:30.
	 */
	public function testCreateObject()
	{
		$endpoint = Model::Named('Endpoint')->objectForId( 4 );
    	$filename = "Swamp Thing 012(1974)(FB-DCP)(C2C).cbz";

		$values = array(
			Rss::title => $filename,
			Rss::desc => "Test item",
			Rss::pub_date => time(),
			Rss::guid => uuid(),
			Rss::enclosure_url => "http://url/to/file",
			Rss::enclosure_length => 10000,
			Rss::enclosure_mime => null,
			Rss::enclosure_hash => '34e4eacc9168cf97e0625699e9b5cb65',
			Rss::enclosure_password => false,
			"endpoint" => $endpoint
		);
		list($rssDBO, $errors) = $this->model->createObject($values);
		$this->assertNull( $errors, "Failed to create new record" );
		$this->assertTrue( $rssDBO != false, "Failed to create new record" );
	}


/*  Test functions */

	/**
	 * load rss records that can be used for the delete test
     */
    public function testFilename()
    {
		$endpoint = Model::Named('Endpoint')->objectForId( 5 );
		$this->assertTrue( $endpoint != false, "Failed to find endpoint (5)" );

    	for( $idx = 0; $idx < 100; $idx++ ) {
    		$name = test_RandomWords();
			$issue = test_RandomNumber();
			$year = test_RandomNumber( 1990, 2016 );

			$values = array(
				Rss::title => $name . " " . str_pad($issue, 3, "0", STR_PAD_LEFT) . " (" . $year . ")",
				Rss::desc => "Test item " . $name,
				Rss::pub_date => time(),
				Rss::guid => uuid(),
				Rss::enclosure_url => "http://url/to/file",
				Rss::enclosure_length => 10000,
				Rss::enclosure_mime => null,
				Rss::enclosure_hash => '34e4eacc9168cf97e0625699e9b5cb65',
				Rss::enclosure_password => false,
				"endpoint" => $endpoint
			);
			list($rssDBO, $errors) = $this->model->createObject($values);
			$this->assertNull( $errors, "Failed to create new record" );
			$this->assertTrue( $rssDBO != false, "Failed to create new record" );
    	}
    }

	/**
	 * @covers	deleteAllForEndpoint
	 * 			T_FUNCTION T_PUBLIC deleteAllForEndpoint ( EndpointDBO $obj)
     * @depends testFilename
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-08 22:34:45.
     */
	public function testDeleteAllForEndpoint()
	{
		$endpoint = Model::Named('Endpoint')->objectForId( 5 );
		$this->assertTrue( $endpoint != false, "Failed to find endpoint (5)" );

		$count = $this->model->countForFK( Rss::endpoint_id, $endpoint );
		$this->assertEquals( 100, $count, "Should be 100 rss records to delete" );

		$this->model->deleteAllForEndpoint( $endpoint );
		$count = $this->model->countForFK( Rss::endpoint_id, $endpoint );
		$this->assertEquals( 0, $count, "Should be 0 rss records left" );
	}

	/**
	 * @covers	objectsForNameIssueYear
	 * 			T_FUNCTION T_PUBLIC objectsForNameIssueYear ( $name, $issue, $year)
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-08 22:34:45.
	 */
	public function testObjectsForNameIssueYear()
	{
		$found = $this->model->objectsForNameIssueYear(null, null, null);
		$this->assertCount( 11, $found, "Should have found all 11 rss" );

		$found = $this->model->objectsForNameIssueYear("Conan", null, null);
		$this->assertCount( 2, $found, "Should have found 2 Conan" );

		$found = $this->model->objectsForNameIssueYear("Conan", 20, null);
		$this->assertCount( 1, $found, "Should have found only Conan (20)" );
		$rssDBO = $found[0];
		$this->assertEquals( 20, $rssDBO->clean_issue, "Should have found only Conan (20)" );

		$found = $this->model->objectsForNameIssueYear(null, null, 2016);
		$this->assertCount( 4, $found, "Should have found 3 rss from 2016" );
	}

	/**
	 * @covers	objectForEndpointGUID
	 * 			T_FUNCTION T_PUBLIC objectForEndpointGUID ( $endpoint, $guid)
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-08 22:34:45.
	 */
	public function testObjectForEndpointGUID()
	{
		try {
			$found = $this->model->objectForEndpointGUID(null, null);
			$this->assertTrue( false, "Should have thrown exception" );
		}
		catch( \Exception $e ) {
		}

		$endpoint = Model::Named('Endpoint')->objectForId( 4 );
		$this->assertTrue( $endpoint != false, "Failed to find endpoint (4)" );
		$guid = "https://www.usenet-crawler.com/details/85c7be5fc67bbb5ac5bcd9090b4a9e31";

		$found = $this->model->objectForEndpointGUID($endpoint, $guid);
		$this->assertTrue( $found != false, "Failed to find rss" );
		$this->assertEquals( $guid, $found->guid, "Found wrong guid" );
	}


/* {functions} */
}
