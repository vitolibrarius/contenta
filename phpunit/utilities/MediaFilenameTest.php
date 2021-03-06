<?php
/**
 * DO NOT EDIT CUSTOM MARKERS
 * Generated from Class.tpl by PhpTestClassGenerator.php on 2016-05-16 20:20:36.
 */

namespace utilities;

use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;
/* * Generated from UseStatements.tpl by PhpTestClassGenerator.php on 2016-05-16 20:20:36. */
use \Logger as Logger;
use \Cache as Cache;
use \ClassNotFoundException as ClassNotFoundException;
/* {useStatements} */

class MediaFilenameTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    	//TEST_RESOURCE_PATH
    }

    protected function tearDown()
    {
    }

//     public function additionProvider()
//     {
// 		$metadata = test_jsonResource("Filename.json");
// 		return $metadata->getMeta( "/" );
//     }

	public function testMe()
	{
		$n  = "Fills - \"The Shadow's Treasure.cbr\" (1/60) 55.4 MBytes yEnc";
		$mediaFilename = new MediaFilename($n);
		$meta = $mediaFilename->updateFileMetaData(null);

		$this->assertEquals( "The Shadow's Treasure", $meta["name"] );
		$this->assertEquals( "cbr", $meta["extension"] );
	}

	public function testFilenames()
	{
		$metadata = test_jsonResource("Filename.json");
		$data = $metadata->getMeta( "/" );
		foreach( $data as $idxHash => $expected ) {
			$this->evaluateFilename(
				$idxHash,
				(isset($expected["clean"]) ? $expected["clean"] : null),
				(isset($expected["extension"]) ? $expected["extension"] : null),
				(isset($expected["issue"]) ? $expected["issue"] : null),
				(isset($expected["name"]) ? $expected["name"] : null),
				(isset($expected["source"]) ? $expected["source"] : null),
				(isset($expected["volume"]) ? $expected["volume"] : null),
				(isset($expected["year"]) ? $expected["year"] : null)
			);
		}
	}

    /**
     */
    public function evaluateFilename($hash, $clean, $extension, $issue, $name, $source, $volume, $year )
    {
    	$this->assertNotEmpty( $source );
    	$this->assertNotEmpty( $clean );
    	$this->assertNotEmpty( $name );

		$mediaFilename = new MediaFilename($source);
		$meta = $mediaFilename->updateFileMetaData(null);
		$this->assertEquals( $clean, $meta["clean"] );
		$this->assertEquals( $name, $meta["name"] );

		if ( isset( $meta["extension"]) ) {
			$this->assertEquals( $meta["extension"], $extension, "in $hash" );
		}
		else {
			$this->assertNull( $extension, "in $hash" );
		}

		if ( isset( $meta["issue"]) ) {
			$this->assertEquals( $meta["issue"], $issue, "in $hash" );
		}
		else {
			$this->assertNull( $issue, "in $hash" );
		}

		if ( isset( $meta["volume"]) ) {
			$this->assertEquals( $meta["volume"], $volume, "in $hash" );
		}
		else {
			$this->assertNull( $volume, "in $hash" );
		}

		if ( isset( $meta["year"]) ) {
			$this->assertEquals( $meta["year"], $year, "in $hash" );
		}
		else {
			$this->assertNull( $year, "in $hash" );
		}
    }

/*	 Test functions */

	/**
	 * @covers	__construct
	 * 			T_FUNCTION T_PUBLIC __construct ( $filename, $skipExtension = false)
	 * @todo	Implement test__construct().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:20:36.
	 */
	public function test__construct()
	{
		$media_filename_example = "100 Bullets Brother Lono 07 of 8 2014 Digital Zone-Empire.cbz";
		$mediaFilename = new MediaFilename( $media_filename_example );
		$this->assertEquals( $media_filename_example, $mediaFilename->sourcename );
		$this->assertEquals( false, $mediaFilename->skipExtension );
	}

	/**
	 * @covers	parseYearFromFilename
	 * 			T_FUNCTION T_PUBLIC parseYearFromFilename ( $filename)
	 * @todo	Implement testParseYearFromFilename().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:20:36.
	 */
	public function testParseYearFromFilename()
	{
	}

	/**
	 * @covers	parseIssueFromFilename
	 * 			T_FUNCTION T_PUBLIC parseIssueFromFilename ( $filename)
	 * @todo	Implement testParseIssueFromFilename().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:20:36.
	 */
	public function testParseIssueFromFilename()
	{
	}

	/**
	 * @covers	parseVolumeFromFilename
	 * 			T_FUNCTION T_PUBLIC parseVolumeFromFilename ( $filename)
	 * @todo	Implement testParseVolumeFromFilename().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:20:36.
	 */
	public function testParseVolumeFromFilename()
	{
	}

	/**
	 * @covers	parsePublicationNameFromFilename
	 * 			T_FUNCTION T_PUBLIC parsePublicationNameFromFilename ( $filename)
	 * @todo	Implement testParsePublicationNameFromFilename().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:20:36.
	 */
	public function testParsePublicationNameFromFilename()
	{
	}

	/**
	 * @covers	parsedValues
	 * 			T_FUNCTION T_PUBLIC parsedValues ( )
	 * @todo	Implement testParsedValues().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:20:36.
	 */
	public function testParsedValues()
	{
	}

	/**
	 * @covers	updateFileMetaData
	 * 			T_FUNCTION T_PUBLIC updateFileMetaData ( $metadata = null, $override = true)
	 * @todo	Implement testUpdateFileMetaData().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:20:36.
	 */
	public function testUpdateFileMetaData()
	{
	}


/* {functions} */
}
