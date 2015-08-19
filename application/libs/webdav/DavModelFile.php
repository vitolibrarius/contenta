<?php

namespace webdav;

use \Model as Model;
use \DataObject as DataObject;
use \Logger as Logger;

class DavModelFile implements DavFile_Interface
{
	var $dbo;
	var $isRoot;

	public function __construct(DataObject $dbo = null, $rootNode = false)
	{
		$this->dbo = $dbo;
		$this->isRoot = $rootNode;
	}

	function media()
	{
		if ( isset( $this->dbo ) && method_exists($this->dbo, "media") ) {
			$mediaArray = $this->dbo->media();
			return (count($mediaArray) > 0 ? $mediaArray[0] : null );
		}
		return null;
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
		if ( $this->isRoot ) {
			return '';
		}

        return strtr($this->getName(), array(
        	" "=>"%20",
        	"%"=>"%25",
        	"&"=>"%26",
        	"<"=>"%3C",
        	">"=>"%3E",
        	)
        );
//         return ($this->isRoot ? '' : urlencode($this->getName()));
	}

	function getName()
	{
		$media = $this->media();
		if ( $media != null ) {
			// we are a publication
			$typeCode = $media->mediaType()->code;
			return $this->dbo->id
				. "_" . sanitize($this->dbo->searchString()) . "." . $typeCode;
		}

        if (isset($this->dbo) == false ) {
        	return "";
        }

        return $this->dbo->id . "_" . sanitize($this->dbo->displayName());
	}

	function getSize()
	{
		$media = $this->media();
		if ( $media != null ) {
			return $media->size;
		}
        return 0;
	}

	function getLastModified()
	{
        return (isset($this->dbo, $this->dbo->created) ? $this->dbo->created : time());
	}

	function etag()
	{
		if ( isset( $this->dbo ) && method_exists($this->dbo, "media") ) {
			$mediaArray = $this->dbo->media();
			if (count($mediaArray) > 0) {
				$media = $mediaArray[0];
				return $media->checksum;
			}
			return $this->dbo->id;
		}
        return 0;
	}

	function propertyValue($propName)
	{
		$pname = strtolower($propName);
		switch( $pname ) {
			case "creationdate":
				return $this->getLastModified();
				break;
			case "displayname":
				return $this->getName();
				break;
			case "getcontentlength":
				return $this->getSize();
				break;
			case "getlastmodified":
				return $this->getLastModified();
				break;
			case "resourcetype":
				return "";
				break;
			case "getetag":
				return $this->etag();
				break;
			case "getcontenttype":
				return "application/x-nzb";
				break;
		}

		return null;
	}
}
