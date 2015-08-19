<?php

namespace webdav;

use \Model as Model;
use \Logger as Logger;

class DavNavCollection implements DavCollection_Interface
{
	var $name;

	public function __construct($name, $id = 0)
	{
		$this->name = $name;
	}

	function delete()
	{
        throw new \Exception("Not permitted");
	}

	function put()
	{
        throw new \Exception("Not permitted");
	}

	function get()
	{
        throw new \Exception("Not permitted");
	}

	function hrefName()
	{
        return urlencode($this->name);
	}

	function getName()
	{
        return $this->name;
	}

	function getSize()
	{
        return 0;
	}

	function getLastModified()
	{
        return time();
	}


	function createFile($name,$data)
	{
        throw new \Exception("Not permitted");
	}

	function createDirectory($name)
	{
        throw new \Exception("Not permitted");
	}

	function getChildren()
	{
        throw new \Exception("Not permitted");
	}

	function getChild($name)
	{
        throw new \Exception("Not permitted");
	}

	function propertyValue($propName)
	{
		$pname = strtolower($propName);
		switch( $pname ) {
			case "getcontentlength":
				return $this->getSize();
				break;
			case "getlastmodified":
				return $this->getLastModified();
				break;
			case "resourcetype":
				return "collection";
				break;
		}

		return null;
	}
}

