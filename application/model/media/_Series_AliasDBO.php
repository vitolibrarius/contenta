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


	// to-one relationship
	public function series()
	{
		if ( isset( $this->series_id ) ) {
			$model = Model::Named('Series');
			return $model->objectForId($this->series_id);
		}
		return false;
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
