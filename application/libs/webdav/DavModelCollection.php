<?php

namespace webdav;

use \Model as Model;
use \DataObject as DataObject;
use \Logger as Logger;

class DavModelCollection  extends DavModelFile implements DavCollection_Interface
{
	public function __construct(DataObject $dbo = null, $rootNode = false)
	{
		parent::__construct($dbo, $rootNode);
	}

	function getSize()
	{
        return 0;
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
		$table = (isset($this->dbo) ? $this->dbo->tableName() : "");
		$children = array();
		switch( $table ) {
			case 'publisher':
				foreach( $this->dbo->series() as $series ) {
					$children[] = new DavModelCollection($series);
				}
				break;
			case 'series':
				foreach( $this->dbo->activePublications() as $pub ) {
					$children[] = new DavModelFile($pub);
				}
				break;
			default:
				break;
		}
		return $children;
	}

	function getChild($name)
	{
		$children = array_filterForKeyValue($this->getChildren(), array( "getName" => $name ));
		return (count($children) > 0 ? $children[0] : null);
	}

	function propertyValue($propName)
	{
		$pname = strtolower($propName);
		switch( $pname ) {
			case "creationdate":
				return $this->getLastModified();
				break;
			case "displayname":
				return htmlspecialchars($this->getName());
				break;
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
// 		Logger::logWarning( "propertyValue($propName)" );

		return null;
	}
}

