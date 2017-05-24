<?php

namespace model\media;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\media\Artist_AliasDBO as Artist_AliasDBO;

/* import related objects */
use \model\media\Artist as Artist;
use \model\media\ArtistDBO as ArtistDBO;

/** Generated class, do not edit.
 */
abstract class _Artist_Alias extends Model
{
	const TABLE = 'artist_alias';

	// attribute keys
	const id = 'id';
	const name = 'name';
	const artist_id = 'artist_id';

	// relationship keys
	const artist = 'artist';

	public function modelName()
	{
		return "Artist_Alias";
	}

	public function dboName()
	{
		return '\model\media\Artist_AliasDBO';
	}

	public function tableName() { return Artist_Alias::TABLE; }
	public function tablePK() { return Artist_Alias::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Artist_Alias::name)
		);
	}

	public function allColumnNames()
	{
		return array(
			Artist_Alias::id,
			Artist_Alias::name,
			Artist_Alias::artist_id
		);
	}

	public function allAttributes()
	{
		return array(
			Artist_Alias::name,
		);
	}

	public function allForeignKeys()
	{
		return array(Artist_Alias::artist_id);
	}

	public function allRelationshipNames()
	{
		return array(
			Artist_Alias::artist
		);
	}

	public function attributes()
	{
		return array(
			Artist_Alias::name => array('length' => 256,'type' => 'TEXT'),
		);
	}

	public function relationships()
	{
		return array(
			Artist_Alias::artist => array(
				'destination' => 'Artist',
				'ownsDestination' => false,
				'isMandatory' => true,
				'isToMany' => false,
				'joins' => array( 'artist_id' => 'id')
			)
		);
	}

	public function searchQualifiers( array $query )
	{
		$qualifiers = array();
		if ( is_array($query) ) {
			foreach( $query as $attr => $value ) {
				switch ($attr) {
			// Artist_Alias::id == INTEGER

			// Artist_Alias::name == TEXT
				case Artist_Alias::name:
					if (strlen($value) > 0) {
						$qualifiers[Artist_Alias::name] = Qualifier::Equals( Artist_Alias::name, $value );
					}
					break;

			// Artist_Alias::artist_id == INTEGER
				case Artist_Alias::artist_id:
					if ( intval($value) > 0 ) {
						$qualifiers[Artist_Alias::artist_id] = Qualifier::Equals( Artist_Alias::artist_id, intval($value) );
					}
					break;

				default:
					/* no type specified for Artist_Alias::artist_id */
					break;
				}
			}
		}
		return $qualifiers;
	}

	/**
	 *	Simple fetches
	 */

	public function allForName($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Artist_Alias::name, $value, null, $limit);
	}




	/**
	 * Simple relationship fetches
	 */
	public function allForArtist($obj, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForFK(Artist_Alias::artist_id, $obj, $this->sortOrder(), $limit);
	}

	public function countForArtist($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Artist_Alias::artist_id, $obj );
		}
		return false;
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "artist":
					return array( Artist_Alias::artist_id, "id"  );
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
			if ( isset($values['name']) == false ) {
				$default_name = $this->attributeDefaultValue( null, null, Artist_Alias::name);
				if ( is_null( $default_name ) == false ) {
					$values['name'] = $default_name;
				}
			}

			// default conversion for relationships
			if ( isset($values['artist']) ) {
				$local_artist = $values['artist'];
				if ( $local_artist instanceof ArtistDBO) {
					$values[Artist_Alias::artist_id] = $local_artist->id;
				}
				else if ( is_integer( $local_artist) ) {
					$params[Artist_Alias::artist_id] = $local_artist;
				}
			}
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Artist_Alias ) {
			if ( isset($values['artist']) ) {
				$local_artist = $values['artist'];
				if ( $local_artist instanceof ArtistDBO) {
					$values[Artist_Alias::artist_id] = $local_artist->id;
				}
				else if ( is_integer( $local_artist) ) {
					$params[Artist_Alias::artist_id] = $values['artist'];
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
		if ( $object instanceof Artist_AliasDBO )
		{
			// does not own artist Artist
			return parent::deleteObject($object);
		}

		return false;
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

	/**
	 * Named fetches
	 */
	public function objectForArtistAndAlias(ArtistDBO $artist, $name )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		$qualifiers[] = Qualifier::FK( 'artist_id', $artist);
		$qualifiers[] = Qualifier::Equals( 'name', $name);

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
				throw new \Exception( "objectForArtistAndAlias expected 1 result, but fetched " . count($result) );
			}
		}

		return false;
	}


	/**
	 * Attribute editing
	 */
	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Artist_Alias::name
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesMap() {
		return array(
			Artist_Alias::name => Model::TEXT_TYPE,
			Artist_Alias::artist_id => Model::TO_ONE_TYPE
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
				case Artist_Alias::artist_id:
					$artist_model = Model::Named('Artist');
					$fkObject = $artist_model->objectForId( $value );
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
	function validate_name($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Artist_Alias::name,
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
				Artist_Alias::artist_id,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
}

?>
