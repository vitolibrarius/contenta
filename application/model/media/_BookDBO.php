<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \model\media\Book as Book;

/* import related objects */
use \model\media\Media_Type as Media_Type;
use \model\media\Media_TypeDBO as Media_TypeDBO;

abstract class _BookDBO extends DataObject
{
	public $type_code;
	public $filename;
	public $original_filename;
	public $checksum;
	public $created;
	public $size;
	public $name;
	public $author;
	public $desc;
	public $pub_date;
	public $pub_order;

	public function displayName()
	{
		return $this->name;
	}

	public function pkValue()
	{
		return $this->{Book::id};
	}

	public function modelName()
	{
		return "Book";
	}

	public function dboName()
	{
		return "\model\media\BookDBO";
	}

	public function formattedDateTime_created() { return $this->formattedDate( Book::created, "M d, Y H:i" ); }
	public function formattedDate_created() {return $this->formattedDate( Book::created, "M d, Y" ); }

	public function formattedDateTime_pub_date() { return $this->formattedDate( Book::pub_date, "M d, Y H:i" ); }
	public function formattedDate_pub_date() {return $this->formattedDate( Book::pub_date, "M d, Y" ); }


	// to-one relationship
	public function mediaType()
	{
		if ( isset( $this->type_code ) ) {
			$model = Model::Named('Media_Type');
			return $model->objectForCode($this->type_code);
		}
		return false;
	}

	public function setMediaType(Media_TypeDBO $obj = null)
	{
		if ( isset($obj, $obj->code) && (isset($this->type_code) == false || $obj->code != $this->type_code) ) {
			parent::storeChange( Book::type_code, $obj->code );
			$this->saveChanges();
		}
	}


	/** Attributes */
	public function filename()
	{
		return parent::changedValue( Book::filename, $this->filename );
	}

	public function setFilename( $value = null)
	{
		parent::storeChange( Book::filename, $value );
	}

	public function original_filename()
	{
		return parent::changedValue( Book::original_filename, $this->original_filename );
	}

	public function setOriginal_filename( $value = null)
	{
		parent::storeChange( Book::original_filename, $value );
	}

	public function checksum()
	{
		return parent::changedValue( Book::checksum, $this->checksum );
	}

	public function setChecksum( $value = null)
	{
		parent::storeChange( Book::checksum, $value );
	}

	public function size()
	{
		return parent::changedValue( Book::size, $this->size );
	}

	public function setSize( $value = null)
	{
		parent::storeChange( Book::size, $value );
	}

	public function name()
	{
		return parent::changedValue( Book::name, $this->name );
	}

	public function setName( $value = null)
	{
		parent::storeChange( Book::name, $value );
	}

	public function author()
	{
		return parent::changedValue( Book::author, $this->author );
	}

	public function setAuthor( $value = null)
	{
		parent::storeChange( Book::author, $value );
	}

	public function desc()
	{
		return parent::changedValue( Book::desc, $this->desc );
	}

	public function setDesc( $value = null)
	{
		parent::storeChange( Book::desc, $value );
	}

	public function pub_date()
	{
		return parent::changedValue( Book::pub_date, $this->pub_date );
	}

	public function setPub_date( $value = null)
	{
		parent::storeChange( Book::pub_date, $value );
	}

	public function pub_order()
	{
		return parent::changedValue( Book::pub_order, $this->pub_order );
	}

	public function setPub_order( $value = null)
	{
		parent::storeChange( Book::pub_order, $value );
	}


}

?>
