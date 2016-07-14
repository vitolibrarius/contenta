<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\media\Publisher as Publisher;

/* import related objects */
use \model\media\Series as Series;
use \model\media\SeriesDBO as SeriesDBO;
use \model\media\Character as Character;
use \model\media\CharacterDBO as CharacterDBO;
use \model\media\Story_Arc as Story_Arc;
use \model\media\Story_ArcDBO as Story_ArcDBO;

abstract class _PublisherDBO extends DataObject
{
	public $name;
	public $created;
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
		return $this->{Publisher::id};
	}

	public function formattedDateTime_created() { return $this->formattedDate( Publisher::created, "M d, Y H:i" ); }
	public function formattedDate_created() {return $this->formattedDate( Publisher::created, "M d, Y" ); }

	public function formattedDateTime_xupdated() { return $this->formattedDate( Publisher::xupdated, "M d, Y H:i" ); }
	public function formattedDate_xupdated() {return $this->formattedDate( Publisher::xupdated, "M d, Y" ); }


	// to-many relationship
	public function series()
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Series');
			return $model->allObjectsForKeyValue( Series::publisher_id, $this->id);
		}

		return false;
	}

	// to-many relationship
	public function characters()
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Character');
			return $model->allObjectsForKeyValue( Character::publisher_id, $this->id);
		}

		return false;
	}

	// to-many relationship
	public function story_arcs()
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Story_Arc');
			return $model->allObjectsForKeyValue( Story_Arc::publisher_id, $this->id);
		}

		return false;
	}


	/** Attributes */
	public function name()
	{
		return parent::changedValue( Publisher::name, $this->name );
	}

	public function setName( $value = null)
	{
		parent::storeChange( Publisher::name, $value );
	}

	public function xurl()
	{
		return parent::changedValue( Publisher::xurl, $this->xurl );
	}

	public function setXurl( $value = null)
	{
		parent::storeChange( Publisher::xurl, $value );
	}

	public function xsource()
	{
		return parent::changedValue( Publisher::xsource, $this->xsource );
	}

	public function setXsource( $value = null)
	{
		parent::storeChange( Publisher::xsource, $value );
	}

	public function xid()
	{
		return parent::changedValue( Publisher::xid, $this->xid );
	}

	public function setXid( $value = null)
	{
		parent::storeChange( Publisher::xid, $value );
	}

	public function xupdated()
	{
		return parent::changedValue( Publisher::xupdated, $this->xupdated );
	}

	public function setXupdated( $value = null)
	{
		parent::storeChange( Publisher::xupdated, $value );
	}


}

?>
