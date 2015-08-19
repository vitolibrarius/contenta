<?php

namespace webdav;

use webdav\DavFile_Interface as DavFile_Interface;

interface DavCollection_Interface extends DavFile_Interface {
	function createFile($name,$data);
	function createDirectory($name);
	function getChildren();
	function getChild($name);
}
