<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \model\media\Artist_Alias as Artist_Alias;

/* import related objects */
use \model\media\Artist as Artist;
use \model\media\ArtistDBO as ArtistDBO;

abstract class _Artist_AliasDBO extends DataObject
{
	public $name;
	public $artist_id;

	public function displayName()
	{
		return $this->name;
	}

	public function pkValue()
	{
		return $this->{Artist_Alias::id};
	}

	public function modelName()
	{
		return "Artist_Alias";
	}

	public function dboName()
	{
		return "\model\media\Artist_AliasDBO";
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
			parent::storeChange( Artist_Alias::artist_id, $obj->id );
			$this->saveChanges();
		}
	}


	/** Attributes */
	public function name()
	{
		return parent::changedValue( Artist_Alias::name, $this->name );
	}

	public function setName( $value = null)
	{
		parent::storeChange( Artist_Alias::name, $value );
	}


}

?>
