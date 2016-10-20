<?php

namespace model\reading;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\reading\Reading_Queue as Reading_Queue;

/* import related objects */
use \model\user\Users as Users;
use \model\user\UsersDBO as UsersDBO;
use \model\media\Series as Series;
use \model\media\SeriesDBO as SeriesDBO;
use \model\media\Story_Arc as Story_Arc;
use \model\media\Story_ArcDBO as Story_ArcDBO;

abstract class _Reading_QueueDBO extends DataObject
{
	public $user_id;
	public $series_id;
	public $story_arc_id;
	public $created;
	public $title;
	public $favorite;
	public $pub_count;
	public $pub_read;
	public $queue_order;


	public function pkValue()
	{
		return $this->{Reading_Queue::id};
	}

	public function modelName()
	{
		return "Reading_Queue";
	}

	public function dboName()
	{
		return "\model\reading\Reading_QueueDBO";
	}

	public function formattedDateTime_created() { return $this->formattedDate( Reading_Queue::created, "M d, Y H:i" ); }
	public function formattedDate_created() {return $this->formattedDate( Reading_Queue::created, "M d, Y" ); }

	public function isFavorite() {
		return (isset($this->favorite) && $this->favorite == Model::TERTIARY_TRUE);
	}


	// to-one relationship
	public function user()
	{
		if ( isset( $this->user_id ) ) {
			$model = Model::Named('Users');
			return $model->objectForId($this->user_id);
		}
		return false;
	}

	public function setUser(UsersDBO $obj = null)
	{
		if ( isset($obj, $obj->id) && (isset($this->user_id) == false || $obj->id != $this->user_id) ) {
			parent::storeChange( Reading_Queue::user_id, $obj->id );
			$this->saveChanges();
		}
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
			parent::storeChange( Reading_Queue::series_id, $obj->id );
			$this->saveChanges();
		}
	}

	// to-one relationship
	public function story_arc()
	{
		if ( isset( $this->story_arc_id ) ) {
			$model = Model::Named('Story_Arc');
			return $model->objectForId($this->story_arc_id);
		}
		return false;
	}

	public function setStory_arc(Story_ArcDBO $obj = null)
	{
		if ( isset($obj, $obj->id) && (isset($this->story_arc_id) == false || $obj->id != $this->story_arc_id) ) {
			parent::storeChange( Reading_Queue::story_arc_id, $obj->id );
			$this->saveChanges();
		}
	}


	/** Attributes */
	public function title()
	{
		return parent::changedValue( Reading_Queue::title, $this->title );
	}

	public function setTitle( $value = null)
	{
		parent::storeChange( Reading_Queue::title, $value );
	}

	public function favorite()
	{
		return parent::changedValue( Reading_Queue::favorite, $this->favorite );
	}

	public function setFavorite( $value = null)
	{
		parent::storeChange( Reading_Queue::favorite, $value );
	}

	public function pub_count()
	{
		return parent::changedValue( Reading_Queue::pub_count, $this->pub_count );
	}

	public function setPub_count( $value = null)
	{
		parent::storeChange( Reading_Queue::pub_count, $value );
	}

	public function pub_read()
	{
		return parent::changedValue( Reading_Queue::pub_read, $this->pub_read );
	}

	public function setPub_read( $value = null)
	{
		parent::storeChange( Reading_Queue::pub_read, $value );
	}

	public function queue_order()
	{
		return parent::changedValue( Reading_Queue::queue_order, $this->queue_order );
	}

	public function setQueue_order( $value = null)
	{
		parent::storeChange( Reading_Queue::queue_order, $value );
	}


}

?>
