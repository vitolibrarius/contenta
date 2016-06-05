<?php
/**
 * DO NOT EDIT CUSTOM MARKERS
 * Generated from Class.tpl by PhpTestClassGenerator.php on 2016-05-16 20:30:12.
 */



use \PHPUnit_Framework_TestCase as PHPUnit_Framework_TestCase;
/* {useStatements} */

class CommonTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

    public function sanitationFilenames()
    {
		$metadata = test_jsonResource("SanitationFilenames.json");
		return $metadata->getMeta( "/" );
    }

    public function sanitationStrings()
    {
		$metadata = test_jsonResource("SanitationStrings.json");
		return $metadata->getMeta( "/" );
    }

    public function normalizationStrings()
    {
		$metadata = test_jsonResource("NormalizedStrings.json");
		return $metadata->getMeta( "/" );
    }

/*	 Test functions */

	/**
	 * @covers	callerClassAndMethod
	 * 			T_FUNCTION callerClassAndMethod ( $currentFunction = '')
	 * @todo	Implement testCallerClassAndMethod().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:30:12.
	 */
	public function testCallerClassAndMethod()
	{
		$caller = callerClassAndMethod();
		$this->assertEquals( 'CommonTest', $caller['class'] );
		$this->assertEquals( 'testCallerClassAndMethod', $caller['function'] );

		$caller = callerClassAndMethod('testCallerClassAndMethod');
		$this->assertEquals( 'ReflectionMethod', $caller['class'] );
		$this->assertEquals( 'invokeArgs', $caller['function'] );
	}

	/**
	 * @covers	split_lines
	 * 			T_FUNCTION split_lines ( $str)
	 * @todo	Implement testSplit_lines().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:30:12.
	 */
	public function testSplit_lines()
	{
		$lines = split_lines( null );
		$this->assertCount( 0, $lines );

		$lines = split_lines( "one line no breaks" );
		$this->assertCount( 1, $lines );

		$text = "one" .  PHP_EOL . "two\rthree\rfour\n\rfive";
		$lines = split_lines( $text );
		$this->assertCount( 5, $lines );
	}

	/**
	 * @covers	get_short_class
	 * 			T_FUNCTION get_short_class ( $obj)
	 * @todo	Implement testGet_short_class().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:30:12.
	 */
	public function testGet_short_class()
	{
		$this->assertEquals( 'CommonTest', get_short_class($this) );
	}

	/**
	 * @covers	startsWith
	 * 			T_FUNCTION startsWith ( $needle, $haystack)
	 * @todo	Implement testStartsWith().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:30:12.
	 */
	public function testStartsWith()
	{
		$this->assertFalse( startsWith(null, "Vitolibrarious"));
		$this->assertFalse( startsWith("vito", null));
		$this->assertFalse( startsWith(null, null));
		$this->assertFalse( startsWith("vito", "Vitolibrarious"));

		$this->assertTrue( startsWith("vito", "vitolibrarious"));
	}

	/**
	 * @covers	endsWith
	 * 			T_FUNCTION endsWith ( $needle, $haystack)
	 * @todo	Implement testEndsWith().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:30:12.
	 */
	public function testEndsWith()
	{
		$this->assertFalse( endsWith(null, "Vitolibrarious"));
		$this->assertFalse( endsWith("vito", null));
		$this->assertFalse( endsWith(null, null));
		$this->assertFalse( endsWith("librarious", "VitoLibrarious"));

		$this->assertTrue( endsWith("librarious", "vitolibrarious"));
	}

	/**
	 * @covers	contains
	 * 			T_FUNCTION contains ( $haystack, $needle)
	 * @todo	Implement testContains().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:30:12.
	 */
	public function testContains()
	{
		$this->assertFalse( contains(null, "Vitolibrarious"));
		$this->assertFalse( contains("vito", null));
		$this->assertFalse( contains(null, null));
		$this->assertFalse( contains("lib", "VitoLibrarious"));

		$this->assertTrue( contains("lib", "vitolibrarious"));
		$this->assertTrue( contains("vito", "vitolibrarious"));
		$this->assertTrue( contains("rious", "vitolibrarious"));
	}

	/**
	 * @covers	zipFileList
	 * 			T_FUNCTION zipFileList ( $path)
	 * @todo	Implement testZipFileList().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:30:12.
	 */
	public function testZipFileList()
	{
		$zipfile = test_mediaSamplesFile( null );
		$filelist = zipFileList($zipfile);
		$this->assertFalse( is_array($filelist) );

		$zipfile = test_mediaSamplesFile( 'not a file' );
		$filelist = zipFileList($zipfile);
		$this->assertFalse( is_array($filelist) );

		$zipfile = test_mediaSamplesFile( "Space_Wikimedia.cbz" );
		$filelist = zipFileList($zipfile);
		$this->assertTrue( is_array($filelist) );
		$this->assertCount( 4, $filelist );
	}

	/**
	 * @covers	classNames
	 * 			T_FUNCTION classNames ( $file)
	 * @todo	Implement testClassNames().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:30:12.
	 */
	public function testClassNames()
	{
		$classes = classNames( null );
		$this->assertFalse( is_array($classes) );

		$classes = classNames( "/not/a/real/path" );
		$this->assertFalse( is_array($classes) );

		$zipfile = test_mediaSamplesFile( "Space_Wikimedia.cbz" );
		$classes = classNames( $zipfile );
		$this->assertTrue( is_array($classes) );
		$this->assertCount( 0, $classes );

		$classes = classNames( __FILE__ );
		$this->assertTrue( is_array($classes) );
		$this->assertCount( 1, $classes );
	}

	/**
	 * @covers	uuid
	 * 			T_FUNCTION uuid ( )
	 * @todo	Implement testUuid().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:30:12.
	 */
	public function testUuid()
	{
		$uuid = uuid();
		$this->assertTrue( is_string($uuid) );
		$this->assertEquals( 36, strlen($uuid));
	}

	/**
	 * @covers	uuidShort
	 * 			T_FUNCTION uuidShort ( )
	 * @todo	Implement testUuidShort().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:30:12.
	 */
	public function testUuidShort()
	{
		$uuid = uuidShort();
		$this->assertTrue( is_string($uuid) );
		$this->assertEquals( 32, strlen($uuid));
	}

	/**
	 * @covers	sanitize_filename
	 * 			T_FUNCTION sanitize_filename ( $string, $maxLength, $force_lowercase = true, $anal = false)
     * @dataProvider sanitationFilenames
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:30:12.
	 */
	public function testSanitize_filename( $teststring, $tests )
	{
		foreach( $tests as $type => $expected ) {
			list( $name, $length, $lowercase, $anal ) = explode(":", $type);

			// Returns TRUE for "1", "true", "on" and "yes"
			// Returns FALSE for "0", "false", "off" and "no"
			// Returns NULL otherwise.
			$lowercase = filter_var($lowercase, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
			$anal = filter_var($anal, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

			$sane = sanitize_filename( $teststring, intval($length), $lowercase, $anal);
			$this->assertTrue( strlen($sane) <= $length, "String length" );
			$this->assertEquals( $expected, $sane, "Using rules $type = " . intval($length)
				.":". var_export($lowercase, true) .":". var_export($anal, true) );
		}
	}

	/**
	 * @covers	sanitize
	 * 			T_FUNCTION sanitize ( $string, $force_lowercase = true, $anal = false)
     * @dataProvider sanitationStrings
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:30:12.
	 */
	public function testSanitize( $teststring, $tests)
	{
		foreach( $tests as $type => $expected ) {
			list( $name, $lowercase, $anal ) = explode(":", $type);

			// Returns TRUE for "1", "true", "on" and "yes"
			// Returns FALSE for "0", "false", "off" and "no"
			// Returns NULL otherwise.
			$lowercase = filter_var($lowercase, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
			$anal = filter_var($anal, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

			$sane = sanitize( $teststring, $lowercase, $anal);
			$this->assertEquals( $expected, $sane, "Using rule $type ("
				. var_export($lowercase, true) .":". var_export($anal, true) . ")" );
		}
	}

	/**
	 * @covers	normalize
	 * 			T_FUNCTION normalize ( $string)
     * @dataProvider normalizationStrings
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:30:12.
	 */
	public function testNormalize($teststring, $normal, $normal_search)
	{
		$new_normal = normalize( $teststring );
		$this->assertEquals( $new_normal, $normal );
	}

	/**
	 * @covers	normalizeSearchString
	 * 			T_FUNCTION normalizeSearchString ( $string = null)
     * @dataProvider normalizationStrings
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:30:12.
	 */
	public function testNormalizeSearchString($teststring, $normal, $normal_search)
	{
		$new_normal = normalizeSearchString( $teststring );
		$this->assertEquals( $new_normal, $normal_search );
	}

	/**
	 * @covers	words
	 * 			T_FUNCTION words ( $string)
	 * @todo	Implement testWords().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:30:12.
	 */
	public function testWords()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	lines
	 * 			T_FUNCTION lines ( $string)
	 * @todo	Implement testLines().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:30:12.
	 */
	public function testLines()
	{
		$lines = lines();
		$this->assertNull( $lines, "null" );

		$lines = lines('');
		$this->assertNull( $lines, "empty string" );

		$lines = lines("\n\n\n\n");
		$this->assertNotNull( $lines, "newlines only" );
		$this->assertCount( 0, $lines, "newlines only, no results" );

		$lines = lines("\n\r\n\r\r\n\n");
		$this->assertNotNull( $lines, "newlines only" );
		$this->assertCount( 0, $lines, "newlines only, no results" );

		$lines = lines("the\nquick brown fox junmped\n\n\nover the lazy dog");
		$this->assertNotNull( $lines, "newlines only" );
		$this->assertCount( 3, $lines, "newlines only, no results" );
	}

	/**
	 * @covers	convertToBytes
	 * 			T_FUNCTION convertToBytes ( $val)
	 * @todo	Implement testConvertToBytes().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:30:12.
	 */
	public function testConvertToBytes()
	{
		$bytes = convertToBytes();
		$this->assertEquals( 0,  $bytes, "null" );

		$bytes = convertToBytes('');
		$this->assertEquals( 0, $bytes, "null" );

		$bytes = convertToBytes('123');
		$this->assertEquals( 123, $bytes, "null" );

		$bytes = convertToBytes('1048576');
		$this->assertEquals( 1048576, $bytes, "null" );

		$bytes = convertToBytes('10M');
		$this->assertEquals( 10485760, $bytes, "null" );
	}

	/**
	 * @covers	formatSizeUnits
	 * 			T_FUNCTION formatSizeUnits ( $value)
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:30:12.
	 */
	public function testFormatSizeUnits()
	{
		$bytes = formatSizeUnits();
		$this->assertEquals( "0 bytes",  $bytes, "null" );

		$bytes = formatSizeUnits('');
		$this->assertEquals( "0 bytes", $bytes, "null" );

		$bytes = formatSizeUnits('123');
		$this->assertEquals( "123 bytes", $bytes, "null" );

		$bytes = formatSizeUnits('1048576');
		$this->assertEquals( "1.00 MB", $bytes, "null" );

		$bytes = formatSizeUnits('10M');
		$this->assertEquals( "10.00 MB", $bytes, "null" );
	}

	/**
	 * @covers	formattedTimeElapsed
	 * 			T_FUNCTION formattedTimeElapsed ( $diff)
	 * @todo	Implement testFormattedTimeElapsed().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:30:12.
	 */
	public function testFormattedTimeElapsed()
	{
		$bytes = formattedTimeElapsed();
		$this->assertEquals( "less than one second",  $bytes, "null" );

		$bytes = formattedTimeElapsed('');
		$this->assertEquals( "less than one second", $bytes, "null" );

		$bytes = formattedTimeElapsed('123');
		$this->assertEquals( "2 minutes", $bytes, "null" );

		$bytes = formattedTimeElapsed('1048576');
		$this->assertEquals( "1 week", $bytes, "null" );

		$bytes = formattedTimeElapsed('104857653');
		$this->assertEquals( "3 years", $bytes, "null" );
	}

	/**
	 * @covers	resize_image
	 * 			T_FUNCTION resize_image ( $sourcefile, $xmax, $ymax)
	 * @todo	Implement testResize_image().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:30:12.
	 */
	public function testResize_image()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	Zip
	 * 			T_FUNCTION Zip ( $source, $destination)
	 * @todo	Implement testZip().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:30:12.
	 */
	public function testZip()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	currentVersionNumber
	 * 			T_FUNCTION currentVersionNumber ( )
	 * @todo	Implement testCurrentVersionNumber().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:30:12.
	 */
	public function testCurrentVersionNumber()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	currentVersionHash
	 * 			T_FUNCTION currentVersionHash ( )
	 * @todo	Implement testCurrentVersionHash().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:30:12.
	 */
	public function testCurrentVersionHash()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	currentRemoteHash
	 * 			T_FUNCTION currentRemoteHash ( )
	 * @todo	Implement testCurrentRemoteHash().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:30:12.
	 */
	public function testCurrentRemoteHash()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}

	/**
	 * @covers	currentChangeLog
	 * 			T_FUNCTION currentChangeLog ( )
	 * @todo	Implement testCurrentChangeLog().
	 * Generated from Function.tpl by PhpTestClassGenerator.php on 2016-05-16 20:30:12.
	 */
	public function testCurrentChangeLog()
	{
		$this->markTestIncomplete('This test has not been implemented yet.');
	}


/* {functions} */
}
