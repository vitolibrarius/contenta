<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;

use \model\media\Media as Media;

/* import related objects */
use \model\media\Media_Type as Media_Type;
use \model\media\Media_TypeDBO as Media_TypeDBO;
use \model\media\Publication as Publication;
use \model\media\PublicationDBO as PublicationDBO;

abstract class _MediaDBO extends DataObject
{
	public $publication_id;
	public $type_id;
	public $filename;
	public $original_filename;
	public $checksum;
	public $created;
	public $size;


	public function formattedDateTime_created() { return $this->formattedDate( Media::created, "M d, Y H:i" ); }
	public function formattedDate_created() {return $this->formattedDate( Media::created, "M d, Y" ); }


	// to-one relationship
	public function mediaType()
	{
		if ( isset( $this->type_id ) ) {
			$model = Model::Named('Media_Type');
			return $model->objectForId($this->type_id);
		}
		return false;
	}

	public function setMediaType(Media_TypeDBO $obj = null)
	{
		if ( isset($obj, $obj->id) && (isset($this->type_id) == false || $obj->id != $this->type_id) ) {
			parent::storeChange( Media::type_id, $obj->id );
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
			parent::storeChange( Media::publication_id, $obj->id );
			$this->saveChanges();
		}
	}


	/** Attributes */
	public function filename()
	{
		return parent::changedValue( Media::filename, $this->filename );
	}

	public function setFilename( $value = null)
	{
		parent::storeChange( Media::filename, $value );
	}

	public function original_filename()
	{
		return parent::changedValue( Media::original_filename, $this->original_filename );
	}

	public function setOriginal_filename( $value = null)
	{
		parent::storeChange( Media::original_filename, $value );
	}

	public function checksum()
	{
		return parent::changedValue( Media::checksum, $this->checksum );
	}

	public function setChecksum( $value = null)
	{
		parent::storeChange( Media::checksum, $value );
	}

	public function size()
	{
		return parent::changedValue( Media::size, $this->size );
	}

	public function setSize( $value = null)
	{
		parent::storeChange( Media::size, $value );
	}


}

?>
