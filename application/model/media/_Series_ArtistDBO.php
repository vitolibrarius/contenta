<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \model\media\Series_Artist as Series_Artist;

/* import related objects */
use \model\media\Series as Series;
use \model\media\SeriesDBO as SeriesDBO;
use \model\media\Artist as Artist;
use \model\media\ArtistDBO as ArtistDBO;
use \model\media\Artist_Role as Artist_Role;
use \model\media\Artist_RoleDBO as Artist_RoleDBO;

abstract class _Series_ArtistDBO extends DataObject
{
	public $series_id;
	public $artist_id;
	public $role_code;


	public function pkValue()
	{
		return $this->{Series_Artist::id};
	}

	public function modelName()
	{
		return "Series_Artist";
	}

	public function dboName()
	{
		return "\model\media\Series_ArtistDBO";
	}


	// to-one relationship
	public function series()
	{
		if ( isset( $this->series_id ) ) {
			$model = Model::Named('Series');
			return $model->objectForId($this->series_id);
		}
		return false;
	}

	public function setSeries(SeriesDBO $obj = null)
	{
		if ( isset($obj, $obj->id) && (isset($this->series_id) == false || $obj->id != $this->series_id) ) {
			parent::storeChange( Series_Artist::series_id, $obj->id );
			$this->saveChanges();
		}
	}

	// to-one relationship
	public function artist()
	{
		if ( isset( $this->artist_id ) ) {
			$model = Model::Named('Artist');
			return $model->objectForId($this->artist_id);
		}
		return false;
	}

	public function setArtist(ArtistDBO $obj = null)
	{
		if ( isset($obj, $obj->id) && (isset($this->artist_id) == false || $obj->id != $this->artist_id) ) {
			parent::storeChange( Series_Artist::artist_id, $obj->id );
			$this->saveChanges();
		}
	}

	// to-one relationship
	public function artist_role()
	{
		if ( isset( $this->role_code ) ) {
			$model = Model::Named('Artist_Role');
			return $model->objectForCode($this->role_code);
		}
		return false;
	}

	public function setArtist_role(Artist_RoleDBO $obj = null)
	{
		if ( isset($obj, $obj->code) && (isset($this->role_code) == false || $obj->code != $this->role_code) ) {
			parent::storeChange( Series_Artist::role_code, $obj->code );
			$this->saveChanges();
		}
	}


	/** Attributes */

}

?>
