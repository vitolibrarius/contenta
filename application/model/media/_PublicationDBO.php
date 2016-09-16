<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\media\Publication as Publication;

/* import related objects */
use \model\media\Series as Series;
use \model\media\SeriesDBO as SeriesDBO;
use \model\media\Media as Media;
use \model\media\MediaDBO as MediaDBO;
use \model\media\Story_Arc_Publication as Story_Arc_Publication;
use \model\media\Story_Arc_PublicationDBO as Story_Arc_PublicationDBO;
use \model\reading\Reading_Item as Reading_Item;
use \model\reading\Reading_ItemDBO as Reading_ItemDBO;
use \model\media\Publication_Character as Publication_Character;
use \model\media\Publication_CharacterDBO as Publication_CharacterDBO;

abstract class _PublicationDBO extends DataObject
{
	public $series_id;
	public $created;
	public $name;
	public $desc;
	public $pub_date;
	public $issue_num;
	public $issue_order;
	public $media_count;
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
		return $this->{Publication::id};
	}

	public function formattedDateTime_created() { return $this->formattedDate( Publication::created, "M d, Y H:i" ); }
	public function formattedDate_created() {return $this->formattedDate( Publication::created, "M d, Y" ); }

	public function formattedDateTime_pub_date() { return $this->formattedDate( Publication::pub_date, "M d, Y H:i" ); }
	public function formattedDate_pub_date() {return $this->formattedDate( Publication::pub_date, "M d, Y" ); }

	public function formattedDateTime_xupdated() { return $this->formattedDate( Publication::xupdated, "M d, Y H:i" ); }
	public function formattedDate_xupdated() {return $this->formattedDate( Publication::xupdated, "M d, Y" ); }


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
			parent::storeChange( Publication::series_id, $obj->id );
			$this->saveChanges();
		}
	}

	// to-many relationship
	public function media()
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Media');
			return $model->allObjectsForKeyValue( Media::publication_id, $this->id);
		}

		return false;
	}

	// to-many relationship
	public function story_arc_publication()
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Story_Arc_Publication');
			return $model->allObjectsForKeyValue( Story_Arc_Publication::publication_id, $this->id);
		}

		return false;
	}

	// to-many relationship
	public function reading_items()
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Reading_Item');
			return $model->allObjectsForKeyValue( Reading_Item::publication_id, $this->id);
		}

		return false;
	}

	// to-many relationship
	public function publication_characters()
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Publication_Character');
			return $model->allObjectsForKeyValue( Publication_Character::publication_id, $this->id);
		}

		return false;
	}


	/** Attributes */
	public function name()
	{
		return parent::changedValue( Publication::name, $this->name );
	}

	public function setName( $value = null)
	{
		parent::storeChange( Publication::name, $value );
	}

	public function desc()
	{
		return parent::changedValue( Publication::desc, $this->desc );
	}

	public function setDesc( $value = null)
	{
		parent::storeChange( Publication::desc, $value );
	}

	public function pub_date()
	{
		return parent::changedValue( Publication::pub_date, $this->pub_date );
	}

	public function setPub_date( $value = null)
	{
		parent::storeChange( Publication::pub_date, $value );
	}

	public function issue_num()
	{
		return parent::changedValue( Publication::issue_num, $this->issue_num );
	}

	public function setIssue_num( $value = null)
	{
		parent::storeChange( Publication::issue_num, $value );
	}

	public function issue_order()
	{
		return parent::changedValue( Publication::issue_order, $this->issue_order );
	}

	public function setIssue_order( $value = null)
	{
		parent::storeChange( Publication::issue_order, $value );
	}

	public function media_count()
	{
		return parent::changedValue( Publication::media_count, $this->media_count );
	}

	public function setMedia_count( $value = null)
	{
		parent::storeChange( Publication::media_count, $value );
	}

	public function xurl()
	{
		return parent::changedValue( Publication::xurl, $this->xurl );
	}

	public function setXurl( $value = null)
	{
		parent::storeChange( Publication::xurl, $value );
	}

	public function xsource()
	{
		return parent::changedValue( Publication::xsource, $this->xsource );
	}

	public function setXsource( $value = null)
	{
		parent::storeChange( Publication::xsource, $value );
	}

	public function xid()
	{
		return parent::changedValue( Publication::xid, $this->xid );
	}

	public function setXid( $value = null)
	{
		parent::storeChange( Publication::xid, $value );
	}

	public function xupdated()
	{
		return parent::changedValue( Publication::xupdated, $this->xupdated );
	}

	public function setXupdated( $value = null)
	{
		parent::storeChange( Publication::xupdated, $value );
	}


}

?>
