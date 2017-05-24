<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \model\media\Publication_Artist as Publication_Artist;

/* import related objects */
use \model\media\Publication as Publication;
use \model\media\PublicationDBO as PublicationDBO;
use \model\media\Artist as Artist;
use \model\media\ArtistDBO as ArtistDBO;
use \model\media\Artist_Role as Artist_Role;
use \model\media\Artist_RoleDBO as Artist_RoleDBO;

abstract class _Publication_ArtistDBO extends DataObject
{
	public $publication_id;
	public $artist_id;
	public $role_code;


	public function pkValue()
	{
		return $this->{Publication_Artist::id};
	}

	public function modelName()
	{
		return "Publication_Artist";
	}

	public function dboName()
	{
		return "\model\media\Publication_ArtistDBO";
	}


	// to-one relationship
	public function publication()
	{
		if ( isset( $this->publication_id ) ) {
			$model = Model::Named('Publication');
			return $model->objectForId($this->publication_id);
		}
		return false;
	}

	public function setPublication(PublicationDBO $obj = null)
	{
		if ( isset($obj, $obj->id) && (isset($this->publication_id) == false || $obj->id != $this->publication_id) ) {
			parent::storeChange( Publication_Artist::publication_id, $obj->id );
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
			parent::storeChange( Publication_Artist::artist_id, $obj->id );
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
			parent::storeChange( Publication_Artist::role_code, $obj->code );
			$this->saveChanges();
		}
	}


	/** Attributes */

}

?>
