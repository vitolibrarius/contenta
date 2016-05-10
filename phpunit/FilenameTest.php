<?php

class FilenameTest extends PHPUnit_Framework_TestCase
{
    public function additionProvider()
    {
		$metadata = test_metadataFor("Filename.json");
		return $metadata->getMeta( "/" );
    }

    /**
     * @dataProvider additionProvider
     */
    public function testFilename( $clean, $extension, $issue, $name, $source, $volume, $year )
    {
    	$this->assertNotEmpty( $source );
    	$this->assertNotEmpty( $clean );
    	$this->assertNotEmpty( $name );

		$mediaFilename = new utilities\MediaFilename($source);
		$meta = $mediaFilename->updateFileMetaData(null);
		$this->assertEquals( $meta["clean"], $clean );
		$this->assertEquals( $meta["name"], $name );

		if ( isset( $meta["extension"]) ) {
			$this->assertEquals( $meta["extension"], $extension );
		}
		else {
			$this->assertNull( $extension );
		}

		if ( isset( $meta["issue"]) ) {
			$this->assertEquals( $meta["issue"], $issue );
		}
		else {
			$this->assertNull( $issue );
		}

		if ( isset( $meta["volume"]) ) {
			$this->assertEquals( $meta["volume"], $volume );
		}
		else {
			$this->assertNull( $volume );
		}

		if ( isset( $meta["year"]) ) {
			$this->assertEquals( $meta["year"], $year );
		}
		else {
			$this->assertNull( $year );
		}
    }
}

?>
