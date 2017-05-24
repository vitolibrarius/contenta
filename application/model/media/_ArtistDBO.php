<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \model\media\Artist as Artist;

/* import related objects */
use \model\media\Artist_Alias as Artist_Alias;
use \model\media\Artist_AliasDBO as Artist_AliasDBO;
use \model\media\Publication_Artists as Publication_Artists;
use \model\media\Publication_ArtistsDBO as Publication_ArtistsDBO;
use \model\media\Series_Artists as Series_Artists;
use \model\media\Series_ArtistsDBO as Series_ArtistsDBO;
use \model\media\Story_Arc_Artist as Story_Arc_Artist;
use \model\media\Story_Arc_ArtistDBO as Story_Arc_ArtistDBO;

abstract class _ArtistDBO extends DataObject
{
	public $created;
	public $name;
	public $desc;
	public $gender;
	public $birth_date;
	public $death_date;
	public $pub_wanted;
	public $xurl;
	public $xsource;
	public $xid;
	public $xupdated;

	public function displayName()
	{
		return $this->name;
	}

	public function pkValue()
	{
		return $this->{Artist::id};
	}

	public function modelName()
	{
		return "Artist";
	}

	public function dboName()
	{
		return "\model\media\ArtistDBO";
	}

	public function formattedDateTime_created() { return $this->formattedDate( Artist::created, "M d, Y H:i" ); }
	public function formattedDate_created() {return $this->formattedDate( Artist::created, "M d, Y" ); }

	public function formattedDateTime_birth_date() { return $this->formattedDate( Artist::birth_date, "M d, Y H:i" ); }
	public function formattedDate_birth_date() {return $this->formattedDate( Artist::birth_date, "M d, Y" ); }

	public function formattedDateTime_death_date() { return $this->formattedDate( Artist::death_date, "M d, Y H:i" ); }
	public function formattedDate_death_date() {return $this->formattedDate( Artist::death_date, "M d, Y" ); }

	public function isPub_wanted() {
		return (isset($this->pub_wanted) && $this->pub_wanted == Model::TERTIARY_TRUE);
	}

	public function formattedDateTime_xupdated() { return $this->formattedDate( Artist::xupdated, "M d, Y H:i" ); }
	public function formattedDate_xupdated() {return $this->formattedDate( Artist::xupdated, "M d, Y" ); }


	// to-many relationship
	public function aliases($limit = SQL::SQL_DEFAULT_LIMIT)
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Artist_Alias');
			return $model->allObjectsForKeyValue(
				Artist_Alias::artist_id,
				$this->id,
				null,
				$limit
			);
		}

		return false;
	}

	// to-many relationship
	public function publication_artists($limit = SQL::SQL_DEFAULT_LIMIT)
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Publication_Artists');
			return $model->allObjectsForKeyValue(
				Publication_Artists::artist_id,
				$this->id,
				null,
				$limit
			);
		}

		return false;
	}

	// to-many relationship
	public function series_artists($limit = SQL::SQL_DEFAULT_LIMIT)
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Series_Artists');
			return $model->allObjectsForKeyValue(
				Series_Artists::artist_id,
				$this->id,
				null,
				$limit
			);
		}

		return false;
	}

	// to-many relationship
	public function story_arc_artists($limit = SQL::SQL_DEFAULT_LIMIT)
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Story_Arc_Artist');
			return $model->allObjectsForKeyValue(
				Story_Arc_Artist::artist_id,
				$this->id,
				null,
				$limit
			);
		}

		return false;
	}


	/** Attributes */
	public function name()
	{
		return parent::changedValue( Artist::name, $this->name );
	}

	public function setName( $value = null)
	{
		parent::storeChange( Artist::name, $value );
	}

	public function desc()
	{
		return parent::changedValue( Artist::desc, $this->desc );
	}

	public function setDesc( $value = null)
	{
		parent::storeChange( Artist::desc, $value );
	}

	public function gender()
	{
		return parent::changedValue( Artist::gender, $this->gender );
	}

	public function setGender( $value = null)
	{
		parent::storeChange( Artist::gender, $value );
	}

	public function birth_date()
	{
		return parent::changedValue( Artist::birth_date, $this->birth_date );
	}

	public function setBirth_date( $value = null)
	{
		parent::storeChange( Artist::birth_date, $value );
	}

	public function death_date()
	{
		return parent::changedValue( Artist::death_date, $this->death_date );
	}

	public function setDeath_date( $value = null)
	{
		parent::storeChange( Artist::death_date, $value );
	}

	public function pub_wanted()
	{
		return parent::changedValue( Artist::pub_wanted, $this->pub_wanted );
	}

	public function setPub_wanted( $value = null)
	{
		parent::storeChange( Artist::pub_wanted, $value );
	}

	public function xurl()
	{
		return parent::changedValue( Artist::xurl, $this->xurl );
	}

	public function setXurl( $value = null)
	{
		parent::storeChange( Artist::xurl, $value );
	}

	public function xsource()
	{
		return parent::changedValue( Artist::xsource, $this->xsource );
	}

	public function setXsource( $value = null)
	{
		parent::storeChange( Artist::xsource, $value );
	}

	public function xid()
	{
		return parent::changedValue( Artist::xid, $this->xid );
	}

	public function setXid( $value = null)
	{
		parent::storeChange( Artist::xid, $value );
	}

	public function xupdated()
	{
		return parent::changedValue( Artist::xupdated, $this->xupdated );
	}

	public function setXupdated( $value = null)
	{
		parent::storeChange( Artist::xupdated, $value );
	}


}

?>
