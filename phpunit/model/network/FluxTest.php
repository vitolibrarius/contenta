<?php
/**
 * DO NOT EDIT CUSTOM MARKERS
 * Generated from Class.tpl by PhpTestClassGenerator.php on 2016-06-17 09:02:41.
 * https://phpunit.de/manual/current/en/appendixes.assertions.html
 */

namespace model\network;

use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;
/* * Generated from UseStatements.tpl by PhpTestClassGenerator.php on 2016-06-17 09:02:41. */
use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \model\network\FluxDBO as FluxDBO;
use \model\Endpoint as Endpoint;
use \model\EndpointDBO as EndpointDBO;
/* {useStatements} */

class FluxTest extends PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
		test_initializeDatabase(true);
		test_importTestData( array( "Endpoint", "Rss", "Flux" ) );
    }

    public static function tearDownAfterClass()
    {
    }

    protected function setUp()
    {
    	$this->model = Model::Named('Flux');
    	$this->assertNotNull( $this->model, "Could not find 'Flux' model" );
    }

    protected function tearDown()
    {
    }

/*	 Test functions */

	/**
	 * @covers	createObject
	 * 			T_FUNCTION T_PUBLIC createObject ( $values)
	Flux::name = 'name';
	Flux::flux_hash = 'flux_hash';
	Flux::flux_error = 'flux_error';
	Flux::src_endpoint = 'src_endpoint';
	Flux::src_guid = 'src_guid';
	Flux::src_status = 'src_status';
	Flux::src_pub_date = 'src_pub_date';
	Flux::dest_endpoint = 'dest_endpoint';
	Flux::dest_guid = 'dest_guid';
	Flux::dest_status = 'dest_status';
	Flux::dest_submission = 'dest_submission';
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-17 09:02:41.
	 */
	public function testCreateObject()
	{
		$endpoint = Model::Named('Endpoint')->objectForId( 4 );

		$values = array(
			Flux::name => 'Pacific Rim - Tales From The Drift 004 (2016) (Digital) (BlackManta-Empire)',
			Flux::flux_hash => 'f940b4f0087141a65981c8d59c88daf8d0153b84d1156f991f8d6b256dfbf3e1',
			Flux::flux_error => 0,
			"source_endpoint" => $endpoint,
			Flux::src_guid => 'https://www.usenet-crawler.com/details/f3104528eb83af96c6b092a0dd69ef79',
			Flux::src_url => 'https://www.usenet-crawler.com/getnzb/f3104528eb83af96c6b092a0dd69ef79.nzb&i=254160&r=2ab307f516c7da431deddd1b1d8ee536',
			Flux::src_status => 'Downloaded',
			Flux::src_pub_date => 1463010713,
			"destination_endpoint" => $endpoint,
			Flux::dest_guid => 'SABnzbd_nzo_xxAthD',
			Flux::dest_status => null,
			Flux::dest_submission => 1465876959
		);
		list($fluxDBO, $errors) = $this->model->createObject($values);
		$this->assertNull( $errors, "Failed to create new record" );
		$this->assertTrue( $fluxDBO != false, "Failed to create new record" );
		$this->assertEquals( $endpoint, $fluxDBO->source_endpoint(), "Source endpoint not set" );
		$this->assertEquals( $endpoint, $fluxDBO->destination_endpoint(), "destination endpoint not set" );
	}

	/**
	 * @covers	updateObject
	 * 			T_FUNCTION T_PUBLIC updateObject ( DataObject $object = null, $values)
	 * @depends testCreateObject
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-17 09:02:41.
	 */
	public function testUpdateObject()
	{
		$endpoint = Model::Named('Endpoint')->objectForId( 4 );
		$src_guid = 'https://www.usenet-crawler.com/details/f3104528eb83af96c6b092a0dd69ef79';

		$fluxDBO = $this->model->objectForSourceEndpointGUID( $endpoint, $src_guid );
		$this->assertTrue( $fluxDBO != false, "Failed to fetch record" );
		$this->assertEquals( $endpoint, $fluxDBO->source_endpoint(), "Source endpoint not set" );
		$this->assertNull( $fluxDBO->dest_status, "dest_status should be null" );

		$this->model->updateObject( $fluxDBO, array( Flux::dest_status => "Completed" ));
		$fluxDBO = $this->model->refreshObject( $fluxDBO );
		$this->assertTrue( $fluxDBO != false, "Failed to fetch record" );
		$this->assertEquals( "Completed", $fluxDBO->dest_status, "dest_status should be set" );
	}


/*  Test functions */


	/**
	 * @covers	objectForSourceEndpointGUID
	 * 			T_FUNCTION T_PUBLIC objectForSourceEndpointGUID ( $src_endpoint, $src_guid)
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-17 09:39:59.
	 */
	public function testObjectForSourceEndpointGUID()
	{
		$endpoint = Model::Named('Endpoint')->objectForId( 4 );
		$src_guid = 'https://www.usenet-crawler.com/details/f3104528eb83af96c6b092a0dd69ef79';

		$fluxDBO = $this->model->objectForSourceEndpointGUID( $endpoint, $src_guid );
		$this->assertTrue( $fluxDBO != false, "Failed to fetch record" );
		$this->assertEquals( $endpoint, $fluxDBO->source_endpoint(), "Source endpoint not set" );
	}

	/**
	 * @covers	objectForDestinationEndpointGUID
	 * 			T_FUNCTION T_PUBLIC objectForDestinationEndpointGUID ( $dest_endpoint, $dest_guid)
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-17 09:39:59.
	 */
	public function testObjectForDestinationEndpointGUID()
	{
		$endpoint = Model::Named('Endpoint')->objectForId( 4 );
		$dest_guid = 'SABnzbd_nzo_xxAthD';

		$fluxDBO = $this->model->objectForDestinationEndpointGUID( $endpoint, $dest_guid );
		$this->assertTrue( $fluxDBO != false, "Failed to fetch record" );
		$this->assertEquals( $endpoint, $fluxDBO->source_endpoint(), "Source endpoint not set" );
	}

	/**
	 * @covers	deleteAllForSource_endpoint
	 * 			T_FUNCTION T_PUBLIC deleteAllForSource_endpoint ( EndpointDBO $obj)
	 * @depends	testObjectForSourceEndpointGUID
	 * @depends testObjectForDestinationEndpointGUID
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-17 09:39:59.
	 */
	public function testDeleteAllForSource_endpoint()
	{
		$endpoint = Model::Named('Endpoint')->objectForId( 4 );
		$this->assertTrue( $endpoint != false, "Failed to find endpoint (4)" );

		$count = $this->model->countForFK( Flux::src_endpoint, $endpoint );
		$this->assertEquals( 2, $count, "Should be 2 flux records to delete" );

		$this->model->deleteAllForSource_endpoint( $endpoint );
		$count = $this->model->countForFK( Flux::src_endpoint, $endpoint );
		$this->assertEquals( 0, $count, "Should be 0 flux records left" );
	}

	/**
	 * @covers	deleteAllForDestination_endpoint
	 * 			T_FUNCTION T_PUBLIC deleteAllForDestination_endpoint ( EndpointDBO $obj)
	 * @depends	testObjectForSourceEndpointGUID
	 * @depends testObjectForDestinationEndpointGUID
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-06-17 09:39:59.
	 */
	public function testDeleteAllForDestination_endpoint()
	{
		$endpoint = Model::Named('Endpoint')->objectForId( 3 );
		$this->assertTrue( $endpoint != false, "Failed to find endpoint (3)" );

		$count = $this->model->countForFK( Flux::dest_endpoint, $endpoint );
		$this->assertEquals( 9, $count, "Should be 9 flux records to delete" );

		$this->model->deleteAllForDestination_endpoint( $endpoint );
		$count = $this->model->countForFK( Flux::dest_endpoint, $endpoint );
		$this->assertEquals( 0, $count, "Should be 0 flux records left" );
	}

/* {functions} */
}
