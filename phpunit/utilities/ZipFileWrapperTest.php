<?php
/**
 * DO NOT EDIT CUSTOM MARKERS
 * Generated from Class.tpl by PhpTestClassGenerator.php on 2016-05-16 21:44:53.
 */

namespace utilities;

use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;
/* * Generated from UseStatements.tpl by PhpTestClassGenerator.php on 2016-05-16 21:44:53. */
use \ZipArchive as ZipArchive;
use \Logger as Logger;
use \Cache as Cache;
use \Config as Config;
use \ClassNotFoundException as ClassNotFoundException;
/* {useStatements} */

class ZipFileWrapperTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

/*	 Test functions */

	/**
	 * @covers	createWrapper
	 * 			T_FUNCTION T_STATIC T_PUBLIC createWrapper ( $sourcePath = null, $destinationPath = null)
	 * @todo	Implement testCreateWrapper().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:44:53.
	 */
	public function testCreateWrapper()
	{
		$src = appendPath( Config::GetRepository(), "testCreateWrapper" );
		$dest = appendPath( Config::GetRepository(), "testCreateWrapper.zip" );

		is_file($src) == false || destroy_dir($src) || die( "unable to delete $src" );
		safe_mkdir( $src ) || die( "Unable to create $temp" );
		for ( $i = 0; $i < 5; $i++ ) {
			$content = test_RandomWords( 50 );
			$f = appendPath( $src, "TestFile_" . $i . ".txt" );
			file_put_contents($f, $content) || die( "Failed to write file $f" );
		}

		$wrapper = FileWrapper::createWrapperForSource($src,$dest);
		$this->assertTrue( $wrapper != false, "Failed to create wrapper" );
		$this->assertTrue( file_exists($dest), "Destination not created $dest" );
		unlink( $dest ) || die( "unable to delete old $dest" );
		destroy_dir($src) || die( "unable to delete old $src" );
	}

	/**
	 * @covers	testWrapper
	 * 			T_FUNCTION T_PUBLIC testWrapper ( $errorCode)
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:44:53.
	 */
	public function testTestWrapper()
	{
		$filename = 'First_Love_Wikimedia.cbz';
		$zipFile = test_mediaSamplesFile($filename);
		$wrapper = FileWrapper::force( $zipFile, 'zip' );
		$test = $wrapper->testWrapper( $errorcode );
		$this->assertNull( $test, "Wrapper failed test " . var_export($test, true) );
		$this->assertEquals( 0, $errorcode, "Error code set to error value" );
	}

	/**
	 * @covers	testWrapper
	 * 			T_FUNCTION T_PUBLIC testWrapper ( $errorCode)
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:44:53.
	 */
	public function testTestWrapper_corrupt()
	{
		$filename = 'corrupt.cbz';
		$zipFile = test_mediaSamplesFile($filename);
		$wrapper = FileWrapper::force( $zipFile, 'zip' );
		$test = $wrapper->testWrapper( $errorcode );
		$this->assertNotNull( $test, "Wrapper failed test " . var_export($test, true) );
		$this->assertEquals( 19, $errorcode, "Error code set to wrong error value" );
	}

	/**
	 * @covers	wrapperContents
	 * 			T_FUNCTION T_PUBLIC wrapperContents ( )
	 * @todo	Implement testWrapperContents().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:44:53.
	 */
	public function testWrapperContents()
	{
		$filename = 'First_Love_Wikimedia.cbz';
		$zipFile = test_mediaSamplesFile($filename);
		$wrapper = FileWrapper::force( $zipFile, 'zip' );
		$contentList = $wrapper->wrapperContents( );
		$this->assertNotNull( $contentList, "Wrapper content list " . var_export($contentList, true) );
		$this->assertCount( 5, $contentList, "Content has wrong number of items" );
	}

	/**
	 * @covers	wrappedDataForName
	 * 			T_FUNCTION T_PUBLIC wrappedDataForName ( $name)
	 * @todo	Implement testWrappedDataForName().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:44:53.
	 */
	public function testWrappedDataForName()
	{
		$filename = 'First_Love_Wikimedia.cbz';
		$zipFile = test_mediaSamplesFile($filename);
		$wrapper = FileWrapper::force( $zipFile, 'zip' );
		$contentList = $wrapper->wrapperContents( );
		$this->assertNotNull( $contentList, "Wrapper content list " . var_export($contentList, true) );
		$this->assertCount( 5, $contentList, "Content has wrong number of items" );

		$item = $contentList[3];
		$data = $wrapper->wrappedDataForName( $item );
		$this->assertNotNull( $data, "Wrapper data for " . var_export($item, true) );
	}

	/**
	 * @covers	wrappedThumbnailForName
	 * 			T_FUNCTION T_PUBLIC wrappedThumbnailForName ( $name, $width = null, $height = null)
	 * @todo	Implement testWrappedThumbnailForName().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:44:53.
	 */
	public function testWrappedThumbnailForName()
	{
		$filename = 'First_Love_Wikimedia.cbz';
		$zipFile = test_mediaSamplesFile($filename);
		$wrapper = FileWrapper::force( $zipFile, 'zip' );
		$contentList = $wrapper->wrapperContents( );
		$this->assertNotNull( $contentList, "Wrapper content list " . var_export($contentList, true) );
		$this->assertCount( 5, $contentList, "Content has wrong number of items" );

		$item = $contentList[3];
		$dest = appendPath( Config::GetRepository(), basename($item) );
		file_exists($dest) == false || unlink($dest) || die( "Failed to delete old $dest");

		$data = $wrapper->wrappedThumbnailForName( $item );
		$this->assertNotNull( $data, "Wrapper data for " . var_export($item, true) );
		$this->assertTrue( file_put_contents($dest, $data) > 0, "failed to write thumbnail to $dest" );
		unlink( $dest ) || die( "unable to delete old $dest" );
	}

	/**
	 * @covers	unwrapToDirectory
	 * 			T_FUNCTION T_PUBLIC unwrapToDirectory ( $dest = null)
	 * @todo	Implement testUnwrapToDirectory().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 21:44:53.
	 */
	public function testUnwrapToDirectory()
	{
		$filename = 'First_Love_Wikimedia.cbz';
		$zipFile = test_mediaSamplesFile($filename);
		$wrapper = FileWrapper::force( $zipFile, 'zip' );
		$contentList = $wrapper->wrapperContents( );
		$this->assertNotNull( $contentList, "Wrapper content list " . var_export($contentList, true) );
		$this->assertCount( 5, $contentList, "Content has wrong number of items" );

		$dest = appendPath( Config::GetRepository(), "unwrapToDirectory" );
		is_file($dest) == false || destroy_dir($dest) || die( "unable to delete old $dest" );
		safe_mkdir( $dest ) || die( "Unable to create $dest" );

		$success = $wrapper->unwrapToDirectory( $dest );
		$this->assertTrue( $success, "unwrap failed to $dest" );
		destroy_dir($dest) || die( "unable to delete old $dest" );
	}


/* {functions} */
}
