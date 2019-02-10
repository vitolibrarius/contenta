<?php

namespace model\media;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\media\BookDBO as BookDBO;

/* import related objects */
use \model\media\Media_Type as Media_Type;
use \model\media\Media_TypeDBO as Media_TypeDBO;

/** Generated class, do not edit.
 */
abstract class _Book extends Model
{
	const TABLE = 'book';

	// attribute keys
	const id = 'id';
	const type_code = 'type_code';
	const filename = 'filename';
	const original_filename = 'original_filename';
	const checksum = 'checksum';
	const created = 'created';
	const size = 'size';
	const name = 'name';
	const author = 'author';
	const desc = 'desc';
	const pub_date = 'pub_date';
	const pub_order = 'pub_order';

	// relationship keys
	const mediaType = 'mediaType';

	public function modelName()
	{
		return "Book";
	}

	public function dboName()
	{
		return '\model\media\BookDBO';
	}

	public function tableName() { return Book::TABLE; }
	public function tablePK() { return Book::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Book::name)
		);
	}

	public function allColumnNames()
	{
		return array(
			Book::id,
			Book::type_code,
			Book::filename,
			Book::original_filename,
			Book::checksum,
			Book::created,
			Book::size,
			Book::name,
			Book::author,
			Book::desc,
			Book::pub_date,
			Book::pub_order
		);
	}

	public function allAttributes()
	{
		return array(
			Book::filename,
			Book::original_filename,
			Book::checksum,
			Book::created,
			Book::size,
			Book::name,
			Book::author,
			Book::desc,
			Book::pub_date,
			Book::pub_order
		);
	}

	public function allForeignKeys()
	{
		return array(Book::type_code);
	}

	public function allRelationshipNames()
	{
		return array(
			Book::mediaType
		);
	}

	public function attributes()
	{
		return array(
			Book::filename => array('length' => 4096,'type' => 'TEXT'),
			Book::original_filename => array('length' => 4096,'type' => 'TEXT'),
			Book::checksum => array('length' => 256,'type' => 'TEXT'),
			Book::created => array('type' => 'DATE'),
			Book::size => array('type' => 'INTEGER'),
			Book::name => array('length' => 256,'type' => 'TEXT'),
			Book::author => array('length' => 256,'type' => 'TEXT'),
			Book::desc => array('length' => 4096,'type' => 'TEXT'),
			Book::pub_date => array('type' => 'DATE'),
			Book::pub_order => array('type' => 'INTEGER')
		);
	}

	public function relationships()
	{
		return array(
			Book::mediaType => array(
				'destination' => 'Media_Type',
				'ownsDestination' => false,
				'isMandatory' => true,
				'isToMany' => false,
				'joins' => array( 'type_code' => 'code')
			)
		);
	}

	public function searchQualifiers( array $query )
	{
		$qualifiers = array();
		if ( is_array($query) ) {
			foreach( $query as $attr => $value ) {
				switch ($attr) {
			// Book::id == INTEGER

			// Book::type_code == TEXT
				case Book::type_code:
					if (strlen($value) > 0) {
						$qualifiers[Book::type_code] = Qualifier::Equals( Book::type_code, $value );
					}
					break;

			// Book::filename == TEXT
				case Book::filename:
					if (strlen($value) > 0) {
						$qualifiers[Book::filename] = Qualifier::Equals( Book::filename, $value );
					}
					break;

			// Book::original_filename == TEXT
				case Book::original_filename:
					if (strlen($value) > 0) {
						$qualifiers[Book::original_filename] = Qualifier::Equals( Book::original_filename, $value );
					}
					break;

			// Book::checksum == TEXT
				case Book::checksum:
					if (strlen($value) > 0) {
						$qualifiers[Book::checksum] = Qualifier::Equals( Book::checksum, $value );
					}
					break;

			// Book::created == DATE

			// Book::size == INTEGER
				case Book::size:
					if ( intval($value) > 0 ) {
						$qualifiers[Book::size] = Qualifier::Equals( Book::size, intval($value) );
					}
					break;

			// Book::name == TEXT
				case Book::name:
					if (strlen($value) > 0) {
						$qualifiers[Book::name] = Qualifier::Equals( Book::name, $value );
					}
					break;

			// Book::author == TEXT
				case Book::author:
					if (strlen($value) > 0) {
						$qualifiers[Book::author] = Qualifier::Equals( Book::author, $value );
					}
					break;

			// Book::desc == TEXT
				case Book::desc:
					if (strlen($value) > 0) {
						$qualifiers[Book::desc] = Qualifier::Equals( Book::desc, $value );
					}
					break;

			// Book::pub_date == DATE

			// Book::pub_order == INTEGER
				case Book::pub_order:
					if ( intval($value) > 0 ) {
						$qualifiers[Book::pub_order] = Qualifier::Equals( Book::pub_order, intval($value) );
					}
					break;

				default:
					/* no type specified for Book::pub_order */
					break;
				}
			}
		}
		return $qualifiers;
	}

	/**
	 *	Simple fetches
	 */

	public function allForType_code($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Book::type_code, $value, null, $limit);
	}


	public function allForFilename($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Book::filename, $value, null, $limit);
	}


	public function allForOriginal_filename($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Book::original_filename, $value, null, $limit);
	}


	public function objectForChecksum($value)
	{
		return $this->singleObjectForKeyValue(Book::checksum, $value);
	}



	public function allForSize($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Book::size, $value, null, $limit);
	}

	public function allForName($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Book::name, $value, null, $limit);
	}


	public function allForAuthor($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Book::author, $value, null, $limit);
	}


	public function allForDesc($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Book::desc, $value, null, $limit);
	}



	public function allForPub_order($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Book::pub_order, $value, null, $limit);
	}


	/**
	 * Simple relationship fetches
	 */
	public function allForMediaType($obj, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForFK(Book::type_code, $obj, $this->sortOrder(), $limit);
	}

	public function countForMediaType($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Book::type_code, $obj );
		}
		return false;
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "media_type":
					return array( Book::type_code, "code"  );
					break;
				default:
					break;
			}
		}
		return parent::joinAttributes( $joinModel );
	}

	/**
	 *	Create/Update functions
	 */
	public function createObject( array $values = array() )
	{
		if ( isset($values) ) {

			// default values for attributes
			if ( isset($values['filename']) == false ) {
				$default_filename = $this->attributeDefaultValue( null, null, Book::filename);
				if ( is_null( $default_filename ) == false ) {
					$values['filename'] = $default_filename;
				}
			}
			if ( isset($values['original_filename']) == false ) {
				$default_original_filename = $this->attributeDefaultValue( null, null, Book::original_filename);
				if ( is_null( $default_original_filename ) == false ) {
					$values['original_filename'] = $default_original_filename;
				}
			}
			if ( isset($values['checksum']) == false ) {
				$default_checksum = $this->attributeDefaultValue( null, null, Book::checksum);
				if ( is_null( $default_checksum ) == false ) {
					$values['checksum'] = $default_checksum;
				}
			}
			if ( isset($values['created']) == false ) {
				$default_created = $this->attributeDefaultValue( null, null, Book::created);
				if ( is_null( $default_created ) == false ) {
					$values['created'] = $default_created;
				}
			}
			if ( isset($values['size']) == false ) {
				$default_size = $this->attributeDefaultValue( null, null, Book::size);
				if ( is_null( $default_size ) == false ) {
					$values['size'] = $default_size;
				}
			}
			if ( isset($values['name']) == false ) {
				$default_name = $this->attributeDefaultValue( null, null, Book::name);
				if ( is_null( $default_name ) == false ) {
					$values['name'] = $default_name;
				}
			}
			if ( isset($values['author']) == false ) {
				$default_author = $this->attributeDefaultValue( null, null, Book::author);
				if ( is_null( $default_author ) == false ) {
					$values['author'] = $default_author;
				}
			}
			if ( isset($values['desc']) == false ) {
				$default_desc = $this->attributeDefaultValue( null, null, Book::desc);
				if ( is_null( $default_desc ) == false ) {
					$values['desc'] = $default_desc;
				}
			}
			if ( isset($values['pub_date']) == false ) {
				$default_pub_date = $this->attributeDefaultValue( null, null, Book::pub_date);
				if ( is_null( $default_pub_date ) == false ) {
					$values['pub_date'] = $default_pub_date;
				}
			}
			if ( isset($values['pub_order']) == false ) {
				$default_pub_order = $this->attributeDefaultValue( null, null, Book::pub_order);
				if ( is_null( $default_pub_order ) == false ) {
					$values['pub_order'] = $default_pub_order;
				}
			}

			// default conversion for relationships
			if ( isset($values['mediaType']) ) {
				$local_mediaType = $values['mediaType'];
				if ( $local_mediaType instanceof Media_TypeDBO) {
					$values[Book::type_code] = $local_mediaType->code;
				}
				else if ( is_string( $local_mediaType) ) {
					$params[Book::type_code] = $local_mediaType;
				}
			}
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Book ) {
			if ( isset($values['mediaType']) ) {
				$local_mediaType = $values['mediaType'];
				if ( $local_mediaType instanceof Media_TypeDBO) {
					$values[Book::type_code] = $local_mediaType->code;
				}
				else if ( is_string( $local_mediaType) ) {
					$params[Book::type_code] = $values['mediaType'];
				}
			}
		}

		return parent::updateObject($object, $values);
	}

	/**
	 *	Delete functions
	 */
	public function deleteObject( DataObject $object = null)
	{
		if ( $object instanceof BookDBO )
		{
			// does not own mediaType Media_Type
			return parent::deleteObject($object);
		}

		return false;
	}

	public function deleteAllForMediaType(Media_TypeDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForMediaType($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForMediaType($obj);
			}
		}
		return $success;
	}

	/**
	 * Named fetches
	 */

	/**
	 * Attribute editing
	 */
	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Book::filename,
				Book::name,
				Book::author
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesMap() {
		return array(
			Book::type_code => Model::TO_ONE_TYPE,
			Book::filename => Model::TEXTAREA_TYPE,
			Book::original_filename => Model::TEXTAREA_TYPE,
			Book::checksum => Model::TEXT_TYPE,
			Book::created => Model::DATE_TYPE,
			Book::size => Model::INT_TYPE,
			Book::name => Model::TEXT_TYPE,
			Book::author => Model::TEXT_TYPE,
			Book::desc => Model::TEXTAREA_TYPE,
			Book::pub_date => Model::DATE_TYPE,
			Book::pub_order => Model::INT_TYPE
		);
	}

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) === false || is_null($object) == true) {
			switch ($attr) {
				case Book::pub_order:
					return 0;
			}
		}
		return parent::attributeDefaultValue($object, $type, $attr);
	}

	/*
	 * return the foreign key object
	 */
	public function attributeObject($object = null, $type = null, $attr, $value)
	{
		$fkObject = false;
		if ( isset( $attr ) ) {
			switch ( $attr ) {
				case Book::type_code:
					$media_type_model = Model::Named('Media_Type');
					$fkObject = $media_type_model->objectForId( $value );
					break;
				default:
					break;
			}
		}
		return $fkObject;
	}

	/**
	 * Validation
	 */
	function validate_type_code($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Book::type_code,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_filename($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Book::filename,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_original_filename($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_checksum($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// make sure Checksum is unique
		$existing = $this->objectForChecksum($value);
		if ( $existing != false && ( is_null($object) || $existing->id != $object->id)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Book::checksum,
				"UNIQUE_FIELD_VALUE"
			);
		}
		return null;
	}
	function validate_created($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// created date is not changeable
		if ( isset($object, $object->created) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Book::created,
				"IMMUTABLE"
			);
		}
		return null;
	}
	function validate_size($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Book::size,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
	function validate_name($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Book::name,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_author($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Book::author,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_desc($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_pub_date($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_pub_order($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Book::pub_order,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
}

?>
