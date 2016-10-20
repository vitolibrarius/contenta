<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\media\Series_Alias as Series_Alias;

/* import related objects */
use \model\media\Series as Series;
use \model\media\SeriesDBO as SeriesDBO;

abstract class _Series_AliasDBO extends DataObject
{
	public $name;
	public $series_id;

	public function displayName()
	{
		return $this->name;
	}

	public function pkValue()
	{
		return $this->{Series_Alias::id};
	}

	public function modelName()
	{
		return "Series_Alias";
	}

	public function dboName()
	{
		return "\model\media\Series_AliasDBO";
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
			parent::storeChange( Series_Alias::series_id, $obj->id );
			$this->saveChanges();
		}
	}


	/** Attributes */
	public function name()
	{
		return parent::changedValue( Series_Alias::name, $this->name );
	}

	public function setName( $value = null)
	{
		parent::storeChange( Series_Alias::name, $value );
	}


}

?>
