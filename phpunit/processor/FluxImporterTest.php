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
use \Model as Model;
use \Exception as Exception;
use \model\Users as Users;
use \model\Publisher as Publisher;
use \model\Character as Character;
use \model\Series as Series;
use \model\Publication as Publication;
use \model\PublicationDBO as PublicationDBO;
use \model\Endpoint as Endpoint;
use \model\Endpoint_Type as Endpoint_Type;
use \model\EndpointDBO as EndpointDBO;
use \model\Story_Arc as Story_Arc;
use \model\Story_Arc_Character as Story_Arc_Character;
use \model\Story_Arc_Series as Story_Arc_Series;
use \model\Flux as Flux;
use \model\FluxDBO as FluxDBO;
use \model\RssDBO as RssDBO;
use \connectors\NewznabConnector as NewznabConnector;
use \processor\NewznabSearchProcessor as NewznabSearchProcessor;
/* {useStatements} */

class FluxImporterTest extends PHPUnit_Framework_TestCase
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
	 * @covers	importFluxRSS
	 * 			T_FUNCTION T_PUBLIC importFluxRSS ( RssDBO $rss = null)
	 * @todo	Implement testImportFluxRSS().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testImportFluxRSS()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	importFluxValues
	 * 			T_FUNCTION T_PUBLIC importFluxValues ( EndpointDBO $endpoint = null, $name = null, $guid = null, $publishedDate = null, $url = null)
	 * @todo	Implement testImportFluxValues().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testImportFluxValues()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	importFlux
	 * 			T_FUNCTION T_PUBLIC importFlux ( FluxDBO $flux = null)
	 * @todo	Implement testImportFlux().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testImportFlux()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	findPostingsForPublication
	 * 			T_FUNCTION T_PUBLIC findPostingsForPublication ( PublicationDBO $publication, NewznabConnector $nzbSearch)
	 * @todo	Implement testFindPostingsForPublication().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testFindPostingsForPublication()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	downloadForFlux
	 * 			T_FUNCTION T_PRIVATE downloadForFlux ( FluxDBO $flux)
	 * @todo	Implement testDownloadForFlux().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testDownloadForFlux()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	processData
	 * 			T_FUNCTION T_PUBLIC processData ( )
	 * @todo	Implement testProcessData().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testProcessData()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}


/* {functions} */
}
