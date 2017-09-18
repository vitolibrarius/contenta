<?php

namespace model\media;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\media\Publication_ArtistDBO as Publication_ArtistDBO;

/* import related objects */
use \model\media\Publication as Publication;
use \model\media\PublicationDBO as PublicationDBO;
use \model\media\Artist as Artist;
use \model\media\ArtistDBO as ArtistDBO;
use \model\media\Artist_Role as Artist_Role;
use \model\media\Artist_RoleDBO as Artist_RoleDBO;

/** Generated class, do not edit.
 */
abstract class _Publication_Artist extends Model
{
	const TABLE = 'publication_artist';

	// attribute keys
	const id = 'id';
	const publication_id = 'publication_id';
	const artist_id = 'artist_id';
	const role_code = 'role_code';

	// relationship keys
	const publication = 'publication';
	const artist = 'artist';
	const artist_role = 'artist_role';

	public function modelName()
	{
		return "Publication_Artist";
	}

	public function dboName()
	{
		return '\model\media\Publication_ArtistDBO';
	}

	public function tableName() { return Publication_Artist::TABLE; }
	public function tablePK() { return Publication_Artist::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Publication_Artist::publication_id)
		);
	}

	public function allColumnNames()
	{
		return array(
			Publication_Artist::id,
			Publication_Artist::publication_id,
			Publication_Artist::artist_id,
			Publication_Artist::role_code
		);
	}

	public function allAttributes()
	{
		return array(
		);
	}

	public function allForeignKeys()
	{
		return array(Publication_Artist::publication_id,
			Publication_Artist::artist_id,
			Publication_Artist::role_code);
	}

	public function allRelationshipNames()
	{
		return array(
			Publication_Artist::publication,
			Publication_Artist::artist,
			Publication_Artist::artist_role
		);
	}

	public function attributes()
	{
		return array(
		);
	}

	public function relationships()
	{
		return array(
			Publication_Artist::publication => array(
				'destination' => 'Publication',
				'ownsDestination' => false,
				'isMandatory' => true,
				'isToMany' => false,
				'joins' => array( 'publication_id' => 'id')
			),
			Publication_Artist::artist => array(
				'destination' => 'Artist',
				'ownsDestination' => false,
				'isMandatory' => true,
				'isToMany' => false,
				'joins' => array( 'artist_id' => 'id')
			),
			Publication_Artist::artist_role => array(
				'destination' => 'Artist_Role',
				'ownsDestination' => false,
				'isMandatory' => true,
				'isToMany' => false,
				'joins' => array( 'role_code' => 'code')
			)
		);
	}

	public function searchQualifiers( array $query )
	{
		$qualifiers = array();
		if ( is_array($query) ) {
			foreach( $query as $attr => $value ) {
				switch ($attr) {
			// Publication_Artist::id == INTEGER

			// Publication_Artist::publication_id == INTEGER
				case Publication_Artist::publication_id:
					if ( intval($value) > 0 ) {
						$qualifiers[Publication_Artist::publication_id] = Qualifier::Equals( Publication_Artist::publication_id, intval($value) );
					}
					break;

			// Publication_Artist::artist_id == INTEGER
				case Publication_Artist::artist_id:
					if ( intval($value) > 0 ) {
						$qualifiers[Publication_Artist::artist_id] = Qualifier::Equals( Publication_Artist::artist_id, intval($value) );
					}
					break;

			// Publication_Artist::role_code == TEXT
				case Publication_Artist::role_code:
					if (strlen($value) > 0) {
						$qualifiers[Publication_Artist::role_code] = Qualifier::Equals( Publication_Artist::role_code, $value );
					}
					break;

				default:
					/* no type specified for Publication_Artist::role_code */
					break;
				}
			}
		}
		return $qualifiers;
	}

	/**
	 *	Simple fetches
	 */



	public function allForRole_code($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Publication_Artist::role_code, $value, null, $limit);
	}



	/**
	 * Simple relationship fetches
	 */
	public function allForPublication($obj, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForFK(Publication_Artist::publication_id, $obj, $this->sortOrder(), $limit);
	}

	public function countForPublication($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Publication_Artist::publication_id, $obj );
		}
		return false;
	}
	public function allForArtist($obj, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForFK(Publication_Artist::artist_id, $obj, $this->sortOrder(), $limit);
	}

	public function countForArtist($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Publication_Artist::artist_id, $obj );
		}
		return false;
	}
	public function allForArtist_role($obj, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForFK(Publication_Artist::role_code, $obj, $this->sortOrder(), $limit);
	}

	public function countForArtist_role($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Publication_Artist::role_code, $obj );
		}
		return false;
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "publication":
					return array( Publication_Artist::publication_id, "id"  );
					break;
				case "artist":
					return array( Publication_Artist::artist_id, "id"  );
					break;
				case "artist_role":
					return array( Publication_Artist::role_code, "code"  );
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

			// default conversion for relationships
			if ( isset($values['publication']) ) {
				$local_publication = $values['publication'];
				if ( $local_publication instanceof PublicationDBO) {
					$values[Publication_Artist::publication_id] = $local_publication->id;
				}
				else if ( is_integer( $local_publication) ) {
					$params[Publication_Artist::publication_id] = $local_publication;
				}
			}
			if ( isset($values['artist']) ) {
				$local_artist = $values['artist'];
				if ( $local_artist instanceof ArtistDBO) {
					$values[Publication_Artist::artist_id] = $local_artist->id;
				}
				else if ( is_integer( $local_artist) ) {
					$params[Publication_Artist::artist_id] = $local_artist;
				}
			}
			if ( isset($values['artist_role']) ) {
				$local_artist_role = $values['artist_role'];
				if ( $local_artist_role instanceof Artist_RoleDBO) {
					$values[Publication_Artist::role_code] = $local_artist_role->code;
				}
				else if ( is_string( $local_artist_role) ) {
					$params[Publication_Artist::role_code] = $local_artist_role;
				}
			}
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Publication_Artist ) {
			if ( isset($values['publication']) ) {
				$local_publication = $values['publication'];
				if ( $local_publication instanceof PublicationDBO) {
					$values[Publication_Artist::publication_id] = $local_publication->id;
				}
				else if ( is_integer( $local_publication) ) {
					$params[Publication_Artist::publication_id] = $values['publication'];
				}
			}
			if ( isset($values['artist']) ) {
				$local_artist = $values['artist'];
				if ( $local_artist instanceof ArtistDBO) {
					$values[Publication_Artist::artist_id] = $local_artist->id;
				}
				else if ( is_integer( $local_artist) ) {
					$params[Publication_Artist::artist_id] = $values['artist'];
				}
			}
			if ( isset($values['artist_role']) ) {
				$local_artist_role = $values['artist_role'];
				if ( $local_artist_role instanceof Artist_RoleDBO) {
					$values[Publication_Artist::role_code] = $local_artist_role->code;
				}
				else if ( is_string( $local_artist_role) ) {
					$params[Publication_Artist::role_code] = $values['artist_role'];
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
		if ( $object instanceof Publication_ArtistDBO )
		{
			// does not own publication Publication
			// does not own artist Artist
			// does not own artist_role Artist_Role
			return parent::deleteObject($object);
		}

		return false;
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
	public function deleteAllForArtist(ArtistDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForArtist($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForArtist($obj);
			}
		}
		return $success;
	}
	public function deleteAllForArtist_role(Artist_RoleDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForArtist_role($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForArtist_role($obj);
			}
		}
		return $success;
	}

	/**
	 * Named fetches
	 */
	public function objectForPublicationArtistRole(PublicationDBO $pub,ArtistDBO $char,Artist_RoleDBO $role )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		$qualifiers[] = Qualifier::FK( 'publication_id', $pub);
		$qualifiers[] = Qualifier::FK( 'artist_id', $char);
		$qualifiers[] = Qualifier::FK( 'role_code', $role);

		if ( count($qualifiers) > 0 ) {
			$select->where( Qualifier::Combine( 'AND', $qualifiers ));
		}

		$result = $select->fetchAll();
		if ( is_array($result) ) {
			$result_size = count($result);
			if ( $result_size == 1 ) {
				return $result[0];
			}
			else if ($result_size > 1 ) {
				throw new \Exception( "objectForPublicationArtistRole expected 1 result, but fetched " . count($result) );
			}
		}

		return false;
	}

	public function objectsLikePublicationArtist(PublicationDBO $pub,ArtistDBO $char,Artist_RoleDBO $role )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		$qualifiers[] = Qualifier::FK( 'publication_id', $pub);
		$qualifiers[] = Qualifier::FK( 'artist_id', $char);
		if ( isset($role) && is_null($role) == false) {
			$qualifiers[] = Qualifier::FK( 'role_code', $role);
		}

		if ( count($qualifiers) > 0 ) {
			$select->where( Qualifier::Combine( 'AND', $qualifiers ));
		}

		$result = $select->fetchAll();
		return $result;
	}


	/**
	 * Attribute editing
	 */

	public function attributesMap() {
		return array(
			Publication_Artist::publication_id => Model::TO_ONE_TYPE,
			Publication_Artist::artist_id => Model::TO_ONE_TYPE,
			Publication_Artist::role_code => Model::TO_ONE_TYPE
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
				case Publication_Artist::publication_id:
					$publication_model = Model::Named('Publication');
					$fkObject = $publication_model->objectForId( $value );
					break;
				case Publication_Artist::artist_id:
					$artist_model = Model::Named('Artist');
					$fkObject = $artist_model->objectForId( $value );
					break;
				case Publication_Artist::role_code:
					$artist_role_model = Model::Named('Artist_Role');
					$fkObject = $artist_role_model->objectForId( $value );
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
				Publication_Artist::publication_id,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_artist_id($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Publication_Artist::artist_id,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_role_code($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Publication_Artist::role_code,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
}

?>
