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
use \Metadata as Metadata;
use \processor\ImportManager as ImportManager;
use \utilities\FileWrapper as FileWrapper;
use \utilities\MediaFilename as MediaFilename;
use \connectors\ComicVineConnector as ComicVineConnector;
use \exceptions\ImportMediaException as ImportMediaException;
use \model\Endpoint_Type as Endpoint_Type;
use \model\PublicationDBO as PublicationDBO;
/* {useStatements} */

class UploadImportTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

/*	 Test functions */

	/**
	 * @covers	renameMedia
	 * 			T_FUNCTION T_PRIVATE renameMedia ( $newFilename = null)
	 * @todo	Implement testRenameMedia().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testRenameMedia()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	setIsAutomatedImport
	 * 			T_FUNCTION T_PUBLIC setIsAutomatedImport ( $yesNo = true)
	 * @todo	Implement testSetIsAutomatedImport().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testSetIsAutomatedImport()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	isAutomatedImport
	 * 			T_FUNCTION T_PUBLIC isAutomatedImport ( )
	 * @todo	Implement testIsAutomatedImport().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testIsAutomatedImport()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	importFilePath
	 * 			T_FUNCTION T_PUBLIC importFilePath ( )
	 * @todo	Implement testImportFilePath().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testImportFilePath()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	sourceFilename
	 * 			T_FUNCTION T_PUBLIC sourceFilename ( )
	 * @todo	Implement testSourceFilename().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testSourceFilename()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	sourceMetaData
	 * 			T_FUNCTION T_PUBLIC sourceMetaData ( )
	 * @todo	Implement testSourceMetaData().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testSourceMetaData()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	searchMetaData
	 * 			T_FUNCTION T_PUBLIC searchMetaData ( )
	 * @todo	Implement testSearchMetaData().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testSearchMetaData()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	statusMetaData
	 * 			T_FUNCTION T_PUBLIC statusMetaData ( )
	 * @todo	Implement testStatusMetaData().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testStatusMetaData()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	setStatusMetaData
	 * 			T_FUNCTION T_PUBLIC setStatusMetaData ( $status = null)
	 * @todo	Implement testSetStatusMetaData().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testSetStatusMetaData()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	sourceFileExtension
	 * 			T_FUNCTION T_PUBLIC sourceFileExtension ( )
	 * @todo	Implement testSourceFileExtension().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testSourceFileExtension()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	sourceFileWrapper
	 * 			T_FUNCTION T_PUBLIC sourceFileWrapper ( )
	 * @todo	Implement testSourceFileWrapper().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testSourceFileWrapper()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	generateThumbnails
	 * 			T_FUNCTION T_PUBLIC generateThumbnails ( )
	 * @todo	Implement testGenerateThumbnails().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testGenerateThumbnails()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	setMediaForImport
	 * 			T_FUNCTION T_PUBLIC setMediaForImport ( $path = null, $filename = null)
	 * @todo	Implement testSetMediaForImport().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testSetMediaForImport()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	resetSearchCriteria
	 * 			T_FUNCTION T_PUBLIC resetSearchCriteria ( )
	 * @todo	Implement testResetSearchCriteria().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testResetSearchCriteria()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	setSearchCriteria
	 * 			T_FUNCTION setSearchCriteria ( $seriesname = null, $issue = null, $year = null)
	 * @todo	Implement testSetSearchCriteria().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testSetSearchCriteria()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	processSearch
	 * 			T_FUNCTION T_PUBLIC processSearch ( )
	 * @todo	Implement testProcessSearch().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testProcessSearch()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	selectMatchingIssue
	 * 			T_FUNCTION T_PUBLIC selectMatchingIssue ( $issueId = null)
	 * @todo	Implement testSelectMatchingIssue().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testSelectMatchingIssue()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	selectMatchingPublication
	 * 			T_FUNCTION T_PUBLIC selectMatchingPublication ( PublicationDBO $publication = null)
	 * @todo	Implement testSelectMatchingPublication().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testSelectMatchingPublication()
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

	/**
	 * @covers	convert_cbr
	 * 			T_FUNCTION T_PUBLIC convert_cbr ( )
	 * @todo	Implement testConvert_cbr().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testConvert_cbr()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	hasResultsMetadata
	 * 			T_FUNCTION T_PUBLIC hasResultsMetadata ( )
	 * @todo	Implement testHasResultsMetadata().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testHasResultsMetadata()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	issueMetadata
	 * 			T_FUNCTION T_PUBLIC issueMetadata ( )
	 * @todo	Implement testIssueMetadata().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testIssueMetadata()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	volumeMetadata
	 * 			T_FUNCTION T_PUBLIC volumeMetadata ( )
	 * @todo	Implement testVolumeMetadata().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:46:01.
	 */
	public function testVolumeMetadata()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}


/* {functions} */
}
