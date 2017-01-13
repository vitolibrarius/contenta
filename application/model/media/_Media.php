<?php

namespace model\media;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\media\MediaDBO as MediaDBO;

/* import related objects */
use \model\media\Media_Type as Media_Type;
use \model\media\Media_TypeDBO as Media_TypeDBO;
use \model\media\Publication as Publication;
use \model\media\PublicationDBO as PublicationDBO;

/** Generated class, do not edit.
 */
abstract class _Media extends Model
{
	const TABLE = 'media';

	// attribute keys
	const id = 'id';
	const publication_id = 'publication_id';
	const type_code = 'type_code';
	const filename = 'filename';
	const original_filename = 'original_filename';
	const checksum = 'checksum';
	const created = 'created';
	const size = 'size';

	// relationship keys
	const mediaType = 'mediaType';
	const publication = 'publication';

	public function modelName()
	{
		return "Media";
	}

	public function dboName()
	{
		return '\model\media\MediaDBO';
	}

	public function tableName() { return Media::TABLE; }
	public function tablePK() { return Media::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Media::filename)
		);
	}

	public function allColumnNames()
	{
		return array(
			Media::id,
			Media::publication_id,
			Media::type_code,
			Media::filename,
			Media::original_filename,
			Media::checksum,
			Media::created,
			Media::size
		);
	}

	public function allAttributes()
	{
		return array(
			Media::filename,
			Media::original_filename,
			Media::checksum,
			Media::created,
			Media::size
		);
	}

	public function allForeignKeys()
	{
		return array(Media::type_code,
			Media::publication_id);
	}

	public function allRelationshipNames()
	{
		return array(
			Media::mediaType,
			Media::publication
		);
	}

	public function attributes()
	{
		return array(
			Media::filename => array('length' => 4096,'type' => 'TEXT'),
			Media::original_filename => array('length' => 4096,'type' => 'TEXT'),
			Media::checksum => array('length' => 256,'type' => 'TEXT'),
			Media::created => array('type' => 'DATE'),
			Media::size => array('type' => 'INTEGER')
		);
	}

	public function relationships()
	{
		return array(
			Media::mediaType => array(
				'destination' => 'Media_Type',
				'ownsDestination' => false,
				'isMandatory' => true,
				'isToMany' => false,
				'joins' => array( 'type_code' => 'code')
			),
			Media::publication => array(
				'destination' => 'Publication',
				'ownsDestination' => false,
				'isMandatory' => true,
				'isToMany' => false,
				'joins' => array( 'publication_id' => 'id')
			)
		);
	}

	public function searchQualifiers( array $query )
	{
		$qualifiers = array();
		if ( is_array($query) ) {
			foreach( $query as $attr => $value ) {
				switch ($attr) {
			// Media::id == INTEGER

			// Media::publication_id == INTEGER
				case Media::publication_id:
					if ( intval($value) > 0 ) {
						$qualifiers[Media::publication_id] = Qualifier::Equals( Media::publication_id, intval($value) );
					}
					break;

			// Media::type_code == INTEGER
				case Media::type_code:
					if ( intval($value) > 0 ) {
						$qualifiers[Media::type_code] = Qualifier::Equals( Media::type_code, intval($value) );
					}
					break;

			// Media::filename == TEXT
				case Media::filename:
					if (strlen($value) > 0) {
						$qualifiers[Media::filename] = Qualifier::Equals( Media::filename, $value );
					}
					break;

			// Media::original_filename == TEXT
				case Media::original_filename:
					if (strlen($value) > 0) {
						$qualifiers[Media::original_filename] = Qualifier::Equals( Media::original_filename, $value );
					}
					break;

			// Media::checksum == TEXT
				case Media::checksum:
					if (strlen($value) > 0) {
						$qualifiers[Media::checksum] = Qualifier::Equals( Media::checksum, $value );
					}
					break;

			// Media::created == DATE

			// Media::size == INTEGER
				case Media::size:
					if ( intval($value) > 0 ) {
						$qualifiers[Media::size] = Qualifier::Equals( Media::size, intval($value) );
					}
					break;

				default:
					/* no type specified for Media::size */
					break;
				}
			}
		}
		return $qualifiers;
	}

	/**
	 *	Simple fetches
	 */



	public function allForFilename($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Media::filename, $value, null, $limit);
	}


	public function allForOriginal_filename($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Media::original_filename, $value, null, $limit);
	}


	public function objectForChecksum($value)
	{
		return $this->singleObjectForKeyValue(Media::checksum, $value);
	}



	public function allForSize($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Media::size, $value, null, $limit);
	}


	/**
	 * Simple relationship fetches
	 */
	public function allForMediaType($obj, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForFK(Media::type_code, $obj, $this->sortOrder(), $limit);
	}

	public function countForMediaType($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Media::type_code, $obj );
		}
		return false;
	}
	public function allForPublication($obj, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForFK(Media::publication_id, $obj, $this->sortOrder(), $limit);
	}

	public function countForPublication($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Media::publication_id, $obj );
		}
		return false;
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "media_type":
					return array( Media::type_code, "code"  );
					break;
				case "publication":
					return array( Media::publication_id, "id"  );
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
				$default_filename = $this->attributeDefaultValue( null, null, Media::filename);
				if ( is_null( $default_filename ) == false ) {
					$values['filename'] = $default_filename;
				}
			}
			if ( isset($values['original_filename']) == false ) {
				$default_original_filename = $this->attributeDefaultValue( null, null, Media::original_filename);
				if ( is_null( $default_original_filename ) == false ) {
					$values['original_filename'] = $default_original_filename;
				}
			}
			if ( isset($values['checksum']) == false ) {
				$default_checksum = $this->attributeDefaultValue( null, null, Media::checksum);
				if ( is_null( $default_checksum ) == false ) {
					$values['checksum'] = $default_checksum;
				}
			}
			if ( isset($values['created']) == false ) {
				$default_created = $this->attributeDefaultValue( null, null, Media::created);
				if ( is_null( $default_created ) == false ) {
					$values['created'] = $default_created;
				}
			}
			if ( isset($values['size']) == false ) {
				$default_size = $this->attributeDefaultValue( null, null, Media::size);
				if ( is_null( $default_size ) == false ) {
					$values['size'] = $default_size;
				}
			}

			// default conversion for relationships
			if ( isset($values['mediaType']) ) {
				$local_mediaType = $values['mediaType'];
				if ( $local_mediaType instanceof Media_TypeDBO) {
					$values[Media::type_code] = $local_mediaType->code;
				}
				else if ( is_integer( $local_mediaType) ) {
					$params[Media::type_code] = $local_mediaType;
				}
			}
			if ( isset($values['publication']) ) {
				$local_publication = $values['publication'];
				if ( $local_publication instanceof PublicationDBO) {
					$values[Media::publication_id] = $local_publication->id;
				}
				else if ( is_integer( $local_publication) ) {
					$params[Media::publication_id] = $local_publication;
				}
			}
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Media ) {
			if ( isset($values['mediaType']) ) {
				$local_mediaType = $values['mediaType'];
				if ( $local_mediaType instanceof Media_TypeDBO) {
					$values[Media::type_code] = $local_mediaType->code;
				}
				else if ( is_integer( $local_mediaType) ) {
					$params[Media::type_code] = $values['mediaType'];
				}
			}
			if ( isset($values['publication']) ) {
				$local_publication = $values['publication'];
				if ( $local_publication instanceof PublicationDBO) {
					$values[Media::publication_id] = $local_publication->id;
				}
				else if ( is_integer( $local_publication) ) {
					$params[Media::publication_id] = $values['publication'];
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
		if ( $object instanceof MediaDBO )
		{
			// does not own mediaType Media_Type
			// does not own publication Publication
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
	public function deleteAllForPublication(PublicationDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForPublication($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForPublication($obj);
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
				Media::filename
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesMap() {
		return array(
			Media::publication_id => Model::TO_ONE_TYPE,
			Media::type_code => Model::TO_ONE_TYPE,
			Media::filename => Model::TEXTAREA_TYPE,
			Media::original_filename => Model::TEXTAREA_TYPE,
			Media::checksum => Model::TEXT_TYPE,
			Media::created => Model::DATE_TYPE,
			Media::size => Model::INT_TYPE
		);
	}

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) === false || is_null($object) == true) {
			switch ($attr) {
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
				case Media::type_code:
					$media_type_model = Model::Named('Media_Type');
					$fkObject = $media_type_model->objectForId( $value );
					break;
				case Media::publication_id:
					$publication_model = Model::Named('Publication');
					$fkObject = $publication_model->objectForId( $value );
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
	function validate_publication_id($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Media::publication_id,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_type_code($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Media::type_code,
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
				Media::filename,
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
				Media::checksum,
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
				Media::created,
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
				Media::size,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
}

?>
