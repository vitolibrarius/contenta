<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;

class characterDBO extends DataObject
{
	public $publisher_id;
	public $name;
	public $desc;
	public $realname;
	public $gender;
	public $popularity;
	public $created;
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

	public function smallIconPath($path = null)
	{
		if (isset($this->{Character::small_icon_name}) && strlen($this->{Character::small_icon_name}) > 0) {
			$working = $this->mediaPath( $this->{Publisher::small_icon_name} );
			if ( file_exists($working) == true && is_file($working) == true)
			{
				return $working;
			}
		}
		return $path;
	}

	public function largeIconPath($path = null)
	{
		if (isset($this->{Character::large_icon_name}) && strlen($this->{Character::large_icon_name}) > 0) {
			$working = $this->mediaPath( $this->{Publisher::large_icon_name} );
			if ( file_exists($working) == true && is_file($working) == true)
			{
				return $working;
			}
		}
		return $path;
	}

	public function publisherName() {
		$publisherObj = $this->publisher();
		if ( $publisherObj != false ) {
			return $publisherObj->displayName();
		}
		return 'Unknown';
	}

	public function publisher() {
		if ( isset($this->publisher_id) ) {
			$model = Model::Named('Publisher');
			return $model->objectForId($this->publisher_id);
		}
		return false;
	}

	public function aliases() {
		$char_model = Model::Named('Character_Alias');
		return $char_model->allForCharacter($this);
	}

	public function addAlias($name = null) {
		if ( isset($name) ) {
			$alias_model = Model::Named('Character_Alias');
			return $alias_model->create($this, $name);
		}
		return false;
	}

	public function updatePopularity() {
		return Model::Named('Character')->updatePopularity($this);
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
