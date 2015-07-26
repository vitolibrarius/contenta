<?php

namespace model;

use \Session as Session;
use \DataObject as DataObject;
use \Model as Model;
use \Localized as Localized;
use \Logger as Logger;

use model\Publication as Publication;
use model\PublicationDBO as PublicationDBO;
use model\Media_Type as Media_Type;
use model\Media_TypeDBO as Media_TypeDBO;

use db\Qualifier as Qualifier;

class Media extends Model
{
	const TABLE =			'media';
	const id =				'id';
	const publication_id =	'publication_id';
	const type_id =			'type_id';
	const filename =		'filename';
	const original_filename =	'original_filename';
	const checksum =		'checksum';
	const size =			'size';
	const created =			'created';

	public function tableName() { return Media::TABLE; }
	public function tablePK() { return Media::id; }
	public function sortOrder() { return array(Media::type_id, Media::filename); }

	public function allColumnNames()
	{
		return array(
			Media::id, Media::publication_id, Media::type_id,
			Media::filename, Media::original_filename, Media::checksum, Media::size, Media::created
		);
	}

	public function mostRecent( $limit = 20 )
	{
		return $this->allObjects( array(
			array("desc" => Media::created)
			), $limit);
	}

	public function allForPublication(model\PublicationDBO $obj = null)
	{
		return $this->allObjectsForFK(Media::publication_id, $obj );
	}

	public function mediaForChecksum($checksum)
	{
		return $this->singleObject( Qualifier::Equals( Media::checksum, $checksum));
	}

	private function createFilenameForPublication( PublicationDBO $publication = null, Media_TypeDBO $type = null)
	{
		if ( is_null($publication) ) {
			Logger::logError('Publication is a required parameter');
		}

		if ( is_null($type) ) {
			Logger::logError('Media_Type is a required parameter');
		}

		$filename_comp = array();

		$series = $publication->series();
		if ( $series != false ) {
			$filename_comp[] = sanitize_filename($series->{Series::name}, 100, false, false);
		}

		$filename_comp[] = $publication->{Publication::issue_num};

		$coverDate = $publication->publishedYear();
		if (isset($coverDate) && $coverDate > 0) {
			$filename_comp[] = $coverDate;
		}

		return implode(' - ', $filename_comp) . "." . $type->code;
	}

	public function create( PublicationDBO $publication = null, Media_TypeDBO $type = null, $original_file = null, $checksum = '', $size = 0 )
	{
		if ( is_null($publication) ) {
			Logger::logError('Publication is a required parameter');
		}

		if ( is_null($type) ) {
			Logger::logError('Media_Type is a required parameter');
		}

		$existing = $this->mediaForChecksum($checksum);
		if ( $existing == false ) {
			$filename = $this->createFilenameForPublication( $publication, $type);

			$params = array(
				Media::created => time(),
				Media::publication_id => $publication->id,
				Media::type_id => $type->id,
				Media::filename => $filename,
				Media::original_filename => $original_file,
				Media::checksum => $checksum,
				Media::size =>$size
			);

			list( $obj, $errorList ) = $this->createObject($params);
			if ( is_array($errorList) ) {
				return $errorList;
			}
			return $obj;
		}
		else {
			Logger::logError('Media already exists for checksum ' . $checksum);
		}
		return false;
	}

	public function deleteAllForPublication($obj)
	{
		$success = true;
		if ( $obj != false )
		{
			$array = $this->allForPublication($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new exceptions\DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForPublication($obj);
			}
		}
		return $success;
	}

	/* EditableModelInterface */
	function validate_filename($object = null, $value)
	{
		if (empty($value))
		{
			return Localized::ModelValidation($this->tableName(), Media::filename, "FIELD_EMPTY");
		}
		else if (strlen($value) > 256 )
		{
			return Localized::ModelValidation($this->tableName(), Media::filename, "FIELD_TOO_LONG" );
		}
		return null;
	}

	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Media::filename,
				Media::publication_id,
				Media::type_id
			);
		}
		return parent::attributesMandatory($object);
	}

	public function validateForSave($object = null, array &$values = array())
	{
		if ( is_null($object) && isset($values, $values[Media::filename]) == false) {
			$typeId = (isset($values[Media::type_id]) ? $values[Media::type_id] : null);
			$type = Model::Named('Media_Type')->objectForId($typeId);

			$pubId = (isset($values[Media::publication_id]) ? $values[Media::publication_id] : null);
			$publication = Model::Named('Publication')->objectForId($pubId);
			if ( $publication != false && $type != false) {
				$values[Media::filename] = $this->createFilenameForPublication( $publication, $type );
			}
		}

		return parent::validateForSave($object, $values);
	}

}
