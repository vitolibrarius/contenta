<?php

namespace webdav;

use \Model as Model;
use \Logger as Logger;

interface DavFile_Interface {
	function hrefName();
	function delete();
	function put();
	function get();
	function getName();
	function getSize();
	function getLastModified();

	function propertyValue($xmlProp);
}
