<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \model\media\Story_Arc as Story_Arc;

/* import related objects */
use \model\media\Publisher as Publisher;
use \model\media\PublisherDBO as PublisherDBO;
use \model\media\Story_Arc_Character as Story_Arc_Character;
use \model\media\Story_Arc_CharacterDBO as Story_Arc_CharacterDBO;
use \model\media\Story_Arc_Publication as Story_Arc_Publication;
use \model\media\Story_Arc_PublicationDBO as Story_Arc_PublicationDBO;
use \model\media\Story_Arc_Series as Story_Arc_Series;
use \model\media\Story_Arc_SeriesDBO as Story_Arc_SeriesDBO;
use \model\reading\Reading_Queue as Reading_Queue;
use \model\reading\Reading_QueueDBO as Reading_QueueDBO;

abstract class _Story_ArcDBO extends DataObject
{
	public $publisher_id;
	public $created;
	public $name;
	public $desc;
	public $pub_active;
	public $pub_wanted;
	public $pub_cycle;
	public $pub_available;
	public $pub_count;
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
		return $this->{Story_Arc::id};
	}

	public function modelName()
	{
		return "Story_Arc";
	}

	public function dboName()
	{
		return "\model\media\Story_ArcDBO";
	}

	public function formattedDateTime_created() { return $this->formattedDate( Story_Arc::created, "M d, Y H:i" ); }
	public function formattedDate_created() {return $this->formattedDate( Story_Arc::created, "M d, Y" ); }

	public function isPub_active() {
		return (isset($this->pub_active) && $this->pub_active == Model::TERTIARY_TRUE);
	}

	public function isPub_wanted() {
		return (isset($this->pub_wanted) && $this->pub_wanted == Model::TERTIARY_TRUE);
	}

	public function formattedDateTime_xupdated() { return $this->formattedDate( Story_Arc::xupdated, "M d, Y H:i" ); }
	public function formattedDate_xupdated() {return $this->formattedDate( Story_Arc::xupdated, "M d, Y" ); }


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
			parent::storeChange( Story_Arc::publisher_id, $obj->id );
			$this->saveChanges();
		}
	}

	// to-many relationship
	public function story_arc_characters($limit = SQL::SQL_DEFAULT_LIMIT)
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Story_Arc_Character');
			return $model->allObjectsForKeyValue(
				Story_Arc_Character::story_arc_id,
				$this->id,
				null,
				$limit
			);
		}

		return false;
	}

	// to-many relationship
	public function story_arc_publication($limit = SQL::SQL_DEFAULT_LIMIT)
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Story_Arc_Publication');
			return $model->allObjectsForKeyValue(
				Story_Arc_Publication::story_arc_id,
				$this->id,
				null,
				$limit
			);
		}

		return false;
	}

	// to-many relationship
	public function story_arc_series($limit = SQL::SQL_DEFAULT_LIMIT)
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Story_Arc_Series');
			return $model->allObjectsForKeyValue(
				Story_Arc_Series::story_arc_id,
				$this->id,
				null,
				$limit
			);
		}

		return false;
	}

	// to-many relationship
	public function reading_queues($limit = SQL::SQL_DEFAULT_LIMIT)
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Reading_Queue');
			return $model->allObjectsForKeyValue(
				Reading_Queue::story_arc_id,
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
		return parent::changedValue( Story_Arc::name, $this->name );
	}

	public function setName( $value = null)
	{
		parent::storeChange( Story_Arc::name, $value );
	}

	public function desc()
	{
		return parent::changedValue( Story_Arc::desc, $this->desc );
	}

	public function setDesc( $value = null)
	{
		parent::storeChange( Story_Arc::desc, $value );
	}

	public function pub_active()
	{
		return parent::changedValue( Story_Arc::pub_active, $this->pub_active );
	}

	public function setPub_active( $value = null)
	{
		parent::storeChange( Story_Arc::pub_active, $value );
	}

	public function pub_wanted()
	{
		return parent::changedValue( Story_Arc::pub_wanted, $this->pub_wanted );
	}

	public function setPub_wanted( $value = null)
	{
		parent::storeChange( Story_Arc::pub_wanted, $value );
	}

	public function pub_cycle()
	{
		return parent::changedValue( Story_Arc::pub_cycle, $this->pub_cycle );
	}

	public function setPub_cycle( $value = null)
	{
		parent::storeChange( Story_Arc::pub_cycle, $value );
	}

	public function pub_available()
	{
		return parent::changedValue( Story_Arc::pub_available, $this->pub_available );
	}

	public function setPub_available( $value = null)
	{
		parent::storeChange( Story_Arc::pub_available, $value );
	}

	public function pub_count()
	{
		return parent::changedValue( Story_Arc::pub_count, $this->pub_count );
	}

	public function setPub_count( $value = null)
	{
		parent::storeChange( Story_Arc::pub_count, $value );
	}

	public function xurl()
	{
		return parent::changedValue( Story_Arc::xurl, $this->xurl );
	}

	public function setXurl( $value = null)
	{
		parent::storeChange( Story_Arc::xurl, $value );
	}

	public function xsource()
	{
		return parent::changedValue( Story_Arc::xsource, $this->xsource );
	}

	public function setXsource( $value = null)
	{
		parent::storeChange( Story_Arc::xsource, $value );
	}

	public function xid()
	{
		return parent::changedValue( Story_Arc::xid, $this->xid );
	}

	public function setXid( $value = null)
	{
		parent::storeChange( Story_Arc::xid, $value );
	}

	public function xupdated()
	{
		return parent::changedValue( Story_Arc::xupdated, $this->xupdated );
	}

	public function setXupdated( $value = null)
	{
		parent::storeChange( Story_Arc::xupdated, $value );
	}


}

?>
