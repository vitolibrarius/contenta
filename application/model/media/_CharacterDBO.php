<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \model\media\Character as Character;

/* import related objects */
use \model\media\Character_Alias as Character_Alias;
use \model\media\Character_AliasDBO as Character_AliasDBO;
use \model\media\Publisher as Publisher;
use \model\media\PublisherDBO as PublisherDBO;
use \model\media\Publication_Character as Publication_Character;
use \model\media\Publication_CharacterDBO as Publication_CharacterDBO;
use \model\media\Series_Character as Series_Character;
use \model\media\Series_CharacterDBO as Series_CharacterDBO;
use \model\media\Story_Arc_Character as Story_Arc_Character;
use \model\media\Story_Arc_CharacterDBO as Story_Arc_CharacterDBO;

abstract class _CharacterDBO extends DataObject
{
	public $publisher_id;
	public $created;
	public $name;
	public $realname;
	public $desc;
	public $popularity;
	public $gender;
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
		return $this->{Character::id};
	}

	public function modelName()
	{
		return "Character";
	}

	public function dboName()
	{
		return "\model\media\CharacterDBO";
	}

	public function formattedDateTime_created() { return $this->formattedDate( Character::created, "M d, Y H:i" ); }
	public function formattedDate_created() {return $this->formattedDate( Character::created, "M d, Y" ); }

	public function formattedDateTime_xupdated() { return $this->formattedDate( Character::xupdated, "M d, Y H:i" ); }
	public function formattedDate_xupdated() {return $this->formattedDate( Character::xupdated, "M d, Y" ); }


	// to-many relationship
	public function aliases($limit = SQL::SQL_DEFAULT_LIMIT)
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Character_Alias');
			return $model->allObjectsForKeyValue(
				Character_Alias::character_id,
				$this->id,
				null,
				$limit
			);
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
			parent::storeChange( Character::publisher_id, $obj->id );
			$this->saveChanges();
		}
	}

	// to-many relationship
	public function publication_characters($limit = SQL::SQL_DEFAULT_LIMIT)
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Publication_Character');
			return $model->allObjectsForKeyValue(
				Publication_Character::character_id,
				$this->id,
				null,
				$limit
			);
		}

		return false;
	}

	// to-many relationship
	public function series_characters($limit = SQL::SQL_DEFAULT_LIMIT)
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Series_Character');
			return $model->allObjectsForKeyValue(
				Series_Character::character_id,
				$this->id,
				null,
				$limit
			);
		}

		return false;
	}

	// to-many relationship
	public function story_arc_characters($limit = SQL::SQL_DEFAULT_LIMIT)
	{
		if ( isset( $this->id ) ) {
			$model = Model::Named('Story_Arc_Character');
			return $model->allObjectsForKeyValue(
				Story_Arc_Character::character_id,
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
		return parent::changedValue( Character::name, $this->name );
	}

	public function setName( $value = null)
	{
		parent::storeChange( Character::name, $value );
	}

	public function realname()
	{
		return parent::changedValue( Character::realname, $this->realname );
	}

	public function setRealname( $value = null)
	{
		parent::storeChange( Character::realname, $value );
	}

	public function desc()
	{
		return parent::changedValue( Character::desc, $this->desc );
	}

	public function setDesc( $value = null)
	{
		parent::storeChange( Character::desc, $value );
	}

	public function popularity()
	{
		return parent::changedValue( Character::popularity, $this->popularity );
	}

	public function setPopularity( $value = null)
	{
		parent::storeChange( Character::popularity, $value );
	}

	public function gender()
	{
		return parent::changedValue( Character::gender, $this->gender );
	}

	public function setGender( $value = null)
	{
		parent::storeChange( Character::gender, $value );
	}

	public function xurl()
	{
		return parent::changedValue( Character::xurl, $this->xurl );
	}

	public function setXurl( $value = null)
	{
		parent::storeChange( Character::xurl, $value );
	}

	public function xsource()
	{
		return parent::changedValue( Character::xsource, $this->xsource );
	}

	public function setXsource( $value = null)
	{
		parent::storeChange( Character::xsource, $value );
	}

	public function xid()
	{
		return parent::changedValue( Character::xid, $this->xid );
	}

	public function setXid( $value = null)
	{
		parent::storeChange( Character::xid, $value );
	}

	public function xupdated()
	{
		return parent::changedValue( Character::xupdated, $this->xupdated );
	}

	public function setXupdated( $value = null)
	{
		parent::storeChange( Character::xupdated, $value );
	}


}

?>
