<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\media\User_Series as User_Series;

/* import related objects */
use \model\user\Users as Users;
use \model\user\UsersDBO as UsersDBO;
use \model\media\Series as Series;
use \model\media\SeriesDBO as SeriesDBO;

abstract class _User_SeriesDBO extends DataObject
{
	public $user_id;
	public $series_id;
	public $favorite;
	public $read;
	public $mislabeled;


	public function pkValue()
	{
		return $this->{User_Series::id};
	}

	public function isFavorite() {
		return (isset($this->favorite) && $this->favorite == Model::TERTIARY_TRUE);
	}

	public function isRead() {
		return (isset($this->read) && $this->read == Model::TERTIARY_TRUE);
	}

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
			parent::storeChange( User_Series::user_id, $obj->id );
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
			parent::storeChange( User_Series::series_id, $obj->id );
			$this->saveChanges();
		}
	}


	/** Attributes */
	public function favorite()
	{
		return parent::changedValue( User_Series::favorite, $this->favorite );
	}

	public function setFavorite( $value = null)
	{
		parent::storeChange( User_Series::favorite, $value );
	}

	public function read()
	{
		return parent::changedValue( User_Series::read, $this->read );
	}

	public function setRead( $value = null)
	{
		parent::storeChange( User_Series::read, $value );
	}

	public function mislabeled()
	{
		return parent::changedValue( User_Series::mislabeled, $this->mislabeled );
	}

	public function setMislabeled( $value = null)
	{
		parent::storeChange( User_Series::mislabeled, $value );
	}


}

?>