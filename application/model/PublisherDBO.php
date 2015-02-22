<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;
use \Config as Config;

use model\Endpoint as Endpoint;
use model\Publisher as Publisher;

class PublisherDBO extends DataObject implements \Image_Interface
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

	public function hasIcons()
	{
		return ($this->smallIconPath() != null) && ($this->largeIconPath() != null);
	}

	public function saveSmallIcon( $url )
	{
		if ( isset($url) ) {
			$filename = downloadImage($url, $this->fullpath(), "SmallIcon" );
			if ( empty($filename) == false ) {
				$this->small_icon_name = $filename;
				if ( $this->model()->updateObject( $this, array( Publisher::small_icon_name => $filename )) ) {
					return $this->model()->refreshObject($this);
				}
			}
		}
		return false;
	}

	public function saveLargeIcon( $url )
	{
		if ( isset($url) ) {
			$filename = downloadImage($url, $this->fullpath(), "LargeIcon" );
			if ( empty($filename) == false ) {
				$this->large_icon_name = $filename;
				if ( $this->model()->updateObject( $this, array( Publisher::large_icon_name => $filename )) ) {
					return $this->model()->refreshObject($this);
				}
			}
		}
		return false;
	}

	public function smallIconPath($path = null)
	{
		if (isset($this->{Publisher::small_icon_name}) && strlen($this->{Publisher::small_icon_name}) > 0) {
			$working = appendPath($this->fullpath(), $this->{Publisher::small_icon_name});
			if ( file_exists($working) == true && is_file($working) == true)
			{
				return $working;
			}
		}
		return $path;
	}

	public function largeIconPath($path = null)
	{
		if (isset($this->{Publisher::large_icon_name}) && strlen($this->{Publisher::large_icon_name}) > 0) {
			$working = appendPath($this->fullpath(), $this->{Publisher::large_icon_name});
			if ( file_exists($working) == true && is_file($working) == true)
			{
				return $working;
			}
		}
		return $path;
	}

	public function fullpath() {
		return appendPath(Config::GetMedia(), $this->path);
	}

	public function externalEndpoint()
	{
		if ( isset( $this->xsource) ) {
			$ep_model = Model::Named('Endpoint');
			$points = $ep_model->allForTypeCode($this->xsource);
			if ( is_array($points) && count($points) > 0) {
				return $points[0];
			}
		}
		return null;
	}
}
