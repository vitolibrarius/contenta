<?php
/**
 * DO NOT EDIT CUSTOM MARKERS
 * Generated from Class.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
 */

namespace processor;

use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;
/* * Generated from UseStatements.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01. */
use \Processor as Processor;
use \Config as Config;
use \Logger as Logger;
use \Model as Model;
use \Localized as Localized;
use \Metadata as Metadata;
use \SQL as SQL;
use \db\Qualifier as Qualifier;
use \model\Job_Type as Job_Type;
use \model\Job_Running as Job_Running;
use \model\Job as Job;
use \model\Story_Arc as Story_Arc;
use \model\Publication as Publication;
use \model\Series as Series;
use \model\Endpoint_Type as Endpoint_Type;
use \model\Endpoint as Endpoint;
use \connectors\NewznabConnector as NewznabConnector;
use \processor\FluxImporter as FluxImporter;
use \exceptions\EndpointConnectionException as EndpointConnectionException;
/* {useStatements} */

class NewznabSearchProcessorTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

/*	 Test functions */

	/**
	 * @covers	batchWantedPublications
	 * 			T_FUNCTION T_PUBLIC batchWantedPublications ( $page, $page_size)
	 * @todo	Implement testBatchWantedPublications().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testBatchWantedPublications()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	isAcceptableMatch
	 * 			T_FUNCTION T_STATIC T_PUBLIC isAcceptableMatch ( $publication, $name, $issue, $year)
	 * @todo	Implement testIsAcceptableMatch().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testIsAcceptableMatch()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	processRss
	 * 			T_FUNCTION T_PRIVATE processRss ( $publication, $fluxImporter)
	 * @todo	Implement testProcessRss().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testProcessRss()
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
