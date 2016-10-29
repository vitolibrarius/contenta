<?php

namespace http;

use \Logger as Logger;

use \http\HttpGet as HttpGet;
use \http\HttpPost as HttpPost;

class PageParams
{
	const PAGE_SHOWN	= '_page_';
	const PAGE_SIZE		= '_size_';
	const PAGE_COUNT	= '_pages_';
	const QUERY_SIZE	= '_query_size_';

	var $pageName;
	var $params = array();

	function __construct($page)
	{
		if ( isset($page) == false ) {
			throw new \Exception( "Page params require a key name" );
		}

		$this->pageName = (string)$page;
	}

	public function queryResults( $total = 0, $pageSize = \SQL::SQL_DEFAULT_LIMIT )
	{
		if ( intval($pageSize) != \SQL::SQL_DEFAULT_LIMIT ) {
			$this->setValueForKey(PageParams::PAGE_SIZE, $pageSize);
		}
		$this->setValueForKey(PageParams::PAGE_SHOWN, 0);
		$this->setValueForKey(PageParams::QUERY_SIZE, intval($total));
		$this->setValueForKey(PageParams::PAGE_COUNT, ceil(intval($total) / $this->pageSize()));
	}

	public function querySize()
	{
		return $this->valueForKey(PageParams::QUERY_SIZE, 0);
	}

	public function pageSize()
	{
		return $this->valueForKey(PageParams::PAGE_SIZE, \SQL::SQL_DEFAULT_LIMIT);
	}

	public function setPageSize($newsize = 0)
	{
		if (intval($newsize) < 0) {
			$this->setValueForKey(PageParams::PAGE_SIZE, \SQL::SQL_DEFAULT_LIMIT);
		}
		else {
			$this->setValueForKey(PageParams::PAGE_SIZE, intval($newsize));
		}
	}

	public function pageShown()
	{
		return $this->valueForKey(PageParams::PAGE_SHOWN, 0);
	}

	public function setPageShown($newPage = 0)
	{
		$this->setValueForKey(PageParams::PAGE_SHOWN, $newPage);
	}

	public function pageCount()
	{
		return $this->valueForKey(PageParams::PAGE_COUNT, 0);
	}

	public function clear( $key = null )
	{
		if ( isset( $key ) && strlen($key) > 0) {
			$this->params = array_setValueForKeypath($key, null, $this->params);
		}
		else {
			$this->params = array();
		}
	}

	public function setValueForKey( $key, $value )
	{
		if ( isset( $key ) && strlen($key) > 0) {
			$this->params = array_setValueForKeypath($key, $value, $this->params);
		}
	}

	public function valueForKey( $key, $default = null )
	{
		$v = null;
		if ( isset( $key ) && strlen($key) > 0) {
			$v = array_valueForKeypath($key, $this->params);
		}

		return (is_null( $v ) ? $default : $v);
	}

	public function updateParametersFromGET( array $keys = array() )
	{
		$newValues = (empty($this->params));
		$results = array();
		if ( is_array($keys) && count($keys) > 0) {
			foreach( $keys as $k ) {
				$v = HttpGet::get($k, null);
				if ( is_null($v) || empty($v) ) {
					if ( $this->valueForKey($k, null) != null ) {
						$newValues = true;
					}
					// clear value if it exists
					$this->clear( $k );
				}
				else {
					$old = $this->valueForKey($k, null);
					if ( $old == null || $old != $v ) {
						$newValues = true;
					}

					$this->setValueForKey( $k, $v );
					$results[$k] = $v;
				}
			}
		}
		else {
			$this->clear();
		}
		return array($newValues, $results);
	}

	public function updateParametersFromPOST( array $keys = array() )
	{
		$newValues = false;
		$results = array();
		if ( is_array($keys) && count($keys) > 0) {
			foreach( $keys as $k ) {
				$v = HttpPost::get($k, null);
				if ( is_null($v) || empty($v) ) {
					if ( $this->valueForKey($k, null) != null ) {
						$newValues = true;
					}
					// clear value if it exists
					$this->clear( $k );
				}
				else {
					$old = $this->valueForKey($k, null);
					if ( $old == null || $old != $v ) {
						$newValues = true;
					}

					$this->setValueForKey( $k, $v );
					$results[$k] = $v;
				}
			}
		}
		else {
			$this->clear();
		}
		return array($newValues, $results);
	}
}
