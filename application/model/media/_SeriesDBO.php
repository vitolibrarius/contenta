<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\media\Series as Series;

/* import related objects */
use \model\media\Series_Alias as Series_Alias;
use \model\media\Series_AliasDBO as Series_AliasDBO;
use \model\media\Publisher as Publisher;
use \model\media\PublisherDBO as PublisherDBO;
use \model\media\Publication as Publication;
use \model\media\PublicationDBO as PublicationDBO;
use \model\media\Series_Characters as Series_Characters;
use \model\media\Series_CharactersDBO as Series_CharactersDBO;
use \model\media\Story_Arc_Series as Story_Arc_Series;
use \model\media\Story_Arc_SeriesDBO as Story_Arc_SeriesDBO;
use \model\media\User_Series as User_Series;
use \model\media\User_SeriesDBO as User_SeriesDBO;

abstract class _SeriesDBO extends DataObject
{
	public $publisher_id;
	public $parent_id;
	public $created;
	public $name;
	public $search_name;
	public $desc;
	public $start_year;
	public $issue_count;
	public $pub_active;
	public $pub_wanted;
	public $pub_available;
	public $pub_cycle;
	public $pub_count;
	public $xurl;
	public $xsource;
	public $xid;
	public $xupdated;

	public function displayName()
	{
		return $this->name;
	}

	public function formattedDateTime_created() { return $this->formattedDate( Series::created, "M d, Y H:i" ); }
	public function formattedDate_created() {return $this->formattedDate( Series::created, "M d, Y" ); }

	public function isPub_active() {
		return (isset($this->pub_active) && $this->pub_active == Model::TERTIARY_TRUE);
	}

	public function isPub_wanted() {
		return (isset($this->pub_wanted) && $this->pub_wanted == Model::TERTIARY_TRUE);
	}

	public function formattedDateTime_xupdated() { return $this->formattedDate( Series::xupdated, "M d, Y H:i" ); }
	public function formattedDate_xupdated() {return $this->formattedDate( Series::xupdated, "M d, Y" ); }


	// to-many relationship
	public function aliases()
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Series_Alias');
			return $model->allObjectsForKeyValue( Series_Alias::series_id, $this->id);
		}

		return false;
	}

	// to-one relationship
	public function publisher()
	{
		if ( isset( $this->publisher_id ) ) {
			$model = Model::Named('Publisher');
			return $model->objectForId($this->publisher_id);
		}
		return false;
	}

	public function setPublisher(PublisherDBO $obj = null)
	{
		if ( isset($obj, $obj->id) && (isset($this->publisher_id) == false || $obj->id != $this->publisher_id) ) {
			parent::storeChange( Series::publisher_id, $obj->id );
			$this->saveChanges();
		}
	}

	// to-many relationship
	public function publications()
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Publication');
			return $model->allObjectsForKeyValue( Publication::series_id, $this->id);
		}

		return false;
	}

	// to-many relationship
	public function series_characters()
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Series_Characters');
			return $model->allObjectsForKeyValue( Series_Characters::series_id, $this->id);
		}

		return false;
	}

	// to-many relationship
	public function story_arc_series()
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Story_Arc_Series');
			return $model->allObjectsForKeyValue( Story_Arc_Series::series_id, $this->id);
		}

		return false;
	}

	// to-many relationship
	public function user_series()
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('User_Series');
			return $model->allObjectsForKeyValue( User_Series::series_id, $this->id);
		}

		return false;
	}


	/** Attributes */
	public function parent_id()
	{
		return parent::changedValue( Series::parent_id, $this->parent_id );
	}

	public function setParent_id( $value = null)
	{
		parent::storeChange( Series::parent_id, $value );
	}

	public function name()
	{
		return parent::changedValue( Series::name, $this->name );
	}

	public function setName( $value = null)
	{
		parent::storeChange( Series::name, $value );
	}

	public function search_name()
	{
		return parent::changedValue( Series::search_name, $this->search_name );
	}

	public function setSearch_name( $value = null)
	{
		parent::storeChange( Series::search_name, $value );
	}

	public function desc()
	{
		return parent::changedValue( Series::desc, $this->desc );
	}

	public function setDesc( $value = null)
	{
		parent::storeChange( Series::desc, $value );
	}

	public function start_year()
	{
		return parent::changedValue( Series::start_year, $this->start_year );
	}

	public function setStart_year( $value = null)
	{
		parent::storeChange( Series::start_year, $value );
	}

	public function issue_count()
	{
		return parent::changedValue( Series::issue_count, $this->issue_count );
	}

	public function setIssue_count( $value = null)
	{
		parent::storeChange( Series::issue_count, $value );
	}

	public function pub_active()
	{
		return parent::changedValue( Series::pub_active, $this->pub_active );
	}

	public function setPub_active( $value = null)
	{
		parent::storeChange( Series::pub_active, $value );
	}

	public function pub_wanted()
	{
		return parent::changedValue( Series::pub_wanted, $this->pub_wanted );
	}

	public function setPub_wanted( $value = null)
	{
		parent::storeChange( Series::pub_wanted, $value );
	}

	public function pub_available()
	{
		return parent::changedValue( Series::pub_available, $this->pub_available );
	}

	public function setPub_available( $value = null)
	{
		parent::storeChange( Series::pub_available, $value );
	}

	public function pub_cycle()
	{
		return parent::changedValue( Series::pub_cycle, $this->pub_cycle );
	}

	public function setPub_cycle( $value = null)
	{
		parent::storeChange( Series::pub_cycle, $value );
	}

	public function pub_count()
	{
		return parent::changedValue( Series::pub_count, $this->pub_count );
	}

	public function setPub_count( $value = null)
	{
		parent::storeChange( Series::pub_count, $value );
	}

	public function xurl()
	{
		return parent::changedValue( Series::xurl, $this->xurl );
	}

	public function setXurl( $value = null)
	{
		parent::storeChange( Series::xurl, $value );
	}

	public function xsource()
	{
		return parent::changedValue( Series::xsource, $this->xsource );
	}

	public function setXsource( $value = null)
	{
		parent::storeChange( Series::xsource, $value );
	}

	public function xid()
	{
		return parent::changedValue( Series::xid, $this->xid );
	}

	public function setXid( $value = null)
	{
		parent::storeChange( Series::xid, $value );
	}

	public function xupdated()
	{
		return parent::changedValue( Series::xupdated, $this->xupdated );
	}

	public function setXupdated( $value = null)
	{
		parent::storeChange( Series::xupdated, $value );
	}


}

?>
