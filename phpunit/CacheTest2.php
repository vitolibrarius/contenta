<?php

class CacheTest2 extends PHPUnit_Framework_TestCase
{
	public function testCacheKey()
	{
		$key = Cache::MakeKey("Vito", "Librarius", "Contenta");
		$this->assertEquals( $key, "Vito/Librarius/Contenta" );
	}
}
