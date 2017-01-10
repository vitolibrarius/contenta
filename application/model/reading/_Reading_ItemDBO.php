<?php

namespace model\reading;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \model\reading\Reading_Item as Reading_Item;

/* import related objects */
use \model\user\Users as Users;
use \model\user\UsersDBO as UsersDBO;
use \model\media\Publication as Publication;
use \model\media\PublicationDBO as PublicationDBO;

abstract class _Reading_ItemDBO extends DataObject
{
	public $user_id;
	public $publication_id;
	public $created;
	public $read_date;
	public $mislabeled;


	public function pkValue()
	{
		return $this->{Reading_Item::id};
	}

	public function modelName()
	{
		return "Reading_Item";
	}

	public function dboName()
	{
		return "\model\reading\Reading_ItemDBO";
	}

	public function formattedDateTime_created() { return $this->formattedDate( Reading_Item::created, "M d, Y H:i" ); }
	public function formattedDate_created() {return $this->formattedDate( Reading_Item::created, "M d, Y" ); }

	public function formattedDateTime_read_date() { return $this->formattedDate( Reading_Item::read_date, "M d, Y H:i" ); }
	public function formattedDate_read_date() {return $this->formattedDate( Reading_Item::read_date, "M d, Y" ); }

	public function isMislabeled() {
		return (isset($this->mislabeled) && $this->mislabeled == Model::TERTIARY_TRUE);
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
			parent::storeChange( Reading_Item::user_id, $obj->id );
			$this->saveChanges();
		}
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
			parent::storeChange( Reading_Item::publication_id, $obj->id );
			$this->saveChanges();
		}
	}


	/** Attributes */
	public function read_date()
	{
		return parent::changedValue( Reading_Item::read_date, $this->read_date );
	}

	public function setRead_date( $value = null)
	{
		parent::storeChange( Reading_Item::read_date, $value );
	}

	public function mislabeled()
	{
		return parent::changedValue( Reading_Item::mislabeled, $this->mislabeled );
	}

	public function setMislabeled( $value = null)
	{
		parent::storeChange( Reading_Item::mislabeled, $value );
	}


}

?>
