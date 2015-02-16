<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;
use \Config as Config;

class PublisherDBO extends DataObject
{
	public $name;
	public $created;
	public $updated;
	public $path;
	public $small_icon_name;
	public $large_icon_name;
	public $xurl;
	public $xsource;
	public $xid;
	public $xupdated;

	public function displayName() {
		return $this->name;
	}

	public function smallIconPath($path = null)
	{
		if (isset($this->{PublisherModel::small_icon_name}) && strlen($this->{PublisherModel::small_icon_name}) > 0) {
			$working = appendPath($this->fullpath(), $this->{PublisherModel::small_icon_name});
			if ( file_exists($working) == true && is_file($working) == true)
			{
				return $working;
			}
		}
		return $path;
	}

	public function largeIconPath($path = null)
	{
		if (isset($this->{PublisherModel::large_icon_name}) && strlen($this->{PublisherModel::large_icon_name}) > 0) {
			$working = appendPath($this->fullpath(), $this->{PublisherModel::large_icon_name});
			if ( file_exists($working) == true && is_file($working) == true)
			{
				return $working;
			}
		}
		return $path;
	}

	public function fullpath() {
		if ( isset($this->path) == false || strlen($this->path) == 0) {
			$model = loadModel('Publisher');
			$this->path = makeUniqueDirectory( Config::GetMedia(), $this->name );
			$model->updateObject($this, array( PublisherModel::TABLE => array(PublisherModel::path => $this->path)));
		}
		$fullpath = appendPath(Config::GetMedia(), $this->path);
		is_dir($fullpath) || mkdir($fullpath, 0755, true) || die('Failed to create directory ' . $fullpath);
		return $fullpath;
	}
}
