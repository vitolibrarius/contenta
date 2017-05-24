<?php

namespace model\media;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\media\ArtistDBO as ArtistDBO;

/* import related objects */
use \model\media\Artist_Alias as Artist_Alias;
use \model\media\Artist_AliasDBO as Artist_AliasDBO;
use \model\media\Publication_Artists as Publication_Artists;
use \model\media\Publication_ArtistsDBO as Publication_ArtistsDBO;
use \model\media\Series_Artists as Series_Artists;
use \model\media\Series_ArtistsDBO as Series_ArtistsDBO;
use \model\media\Story_Arc_Artist as Story_Arc_Artist;
use \model\media\Story_Arc_ArtistDBO as Story_Arc_ArtistDBO;

/** Generated class, do not edit.
 */
abstract class _Artist extends Model
{
	const TABLE = 'artist';

	// attribute keys
	const id = 'id';
	const created = 'created';
	const name = 'name';
	const desc = 'desc';
	const gender = 'gender';
	const birth_date = 'birth_date';
	const death_date = 'death_date';
	const pub_wanted = 'pub_wanted';
	const xurl = 'xurl';
	const xsource = 'xsource';
	const xid = 'xid';
	const xupdated = 'xupdated';

	// relationship keys
	const aliases = 'aliases';
	const publication_artists = 'publication_artists';
	const series_artists = 'series_artists';
	const story_arc_artists = 'story_arc_artists';

	public function modelName()
	{
		return "Artist";
	}

	public function dboName()
	{
		return '\model\media\ArtistDBO';
	}

	public function tableName() { return Artist::TABLE; }
	public function tablePK() { return Artist::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Artist::name)
		);
	}

	public function allColumnNames()
	{
		return array(
			Artist::id,
			Artist::created,
			Artist::name,
			Artist::desc,
			Artist::gender,
			Artist::birth_date,
			Artist::death_date,
			Artist::pub_wanted,
			Artist::xurl,
			Artist::xsource,
			Artist::xid,
			Artist::xupdated
		);
	}

	public function allAttributes()
	{
		return array(
			Artist::created,
			Artist::name,
			Artist::desc,
			Artist::gender,
			Artist::birth_date,
			Artist::death_date,
			Artist::pub_wanted,
			Artist::xurl,
			Artist::xsource,
			Artist::xid,
			Artist::xupdated
		);
	}

	public function allForeignKeys()
	{
		return array();
	}

	public function allRelationshipNames()
	{
		return array(
			Artist::aliases,
			Artist::publication_artists,
			Artist::series_artists,
			Artist::story_arc_artists
		);
	}

	public function attributes()
	{
		return array(
			Artist::created => array('type' => 'DATE'),
			Artist::name => array('length' => 256,'type' => 'TEXT'),
			Artist::desc => array('length' => 4096,'type' => 'TEXT'),
			Artist::gender => array('length' => 25,'type' => 'TEXT'),
			Artist::birth_date => array('type' => 'DATE'),
			Artist::death_date => array('type' => 'DATE'),
			Artist::pub_wanted => array('type' => 'BOOLEAN'),
			Artist::xurl => array('length' => 1024,'type' => 'TEXT'),
			Artist::xsource => array('length' => 256,'type' => 'TEXT'),
			Artist::xid => array('length' => 256,'type' => 'TEXT'),
			Artist::xupdated => array('type' => 'DATE')
		);
	}

	public function relationships()
	{
		return array(
			Artist::aliases => array(
				'destination' => 'Artist_Alias',
				'ownsDestination' => true,
				'isMandatory' => false,
				'isToMany' => true,
				'joins' => array( 'id' => 'artist_id')
			),
			Artist::publication_artists => array(
				'destination' => 'Publication_Artists',
				'ownsDestination' => true,
				'isMandatory' => false,
				'isToMany' => true,
				'joins' => array( 'id' => 'artist_id')
			),
			Artist::series_artists => array(
				'destination' => 'Series_Artists',
				'ownsDestination' => true,
				'isMandatory' => false,
				'isToMany' => true,
				'joins' => array( 'id' => 'artist_id')
			),
			Artist::story_arc_artists => array(
				'destination' => 'Story_Arc_Artist',
				'ownsDestination' => true,
				'isMandatory' => false,
				'isToMany' => true,
				'joins' => array( 'id' => 'artist_id')
			)
		);
	}

	public function searchQualifiers( array $query )
	{
		$qualifiers = array();
		if ( is_array($query) ) {
			foreach( $query as $attr => $value ) {
				switch ($attr) {
			// Artist::id == INTEGER

			// Artist::created == DATE

			// Artist::name == TEXT
				case Artist::name:
					if (strlen($value) > 0) {
						$qualifiers[Artist::name] = Qualifier::Equals( Artist::name, $value );
					}
					break;

			// Artist::desc == TEXT
				case Artist::desc:
					if (strlen($value) > 0) {
						$qualifiers[Artist::desc] = Qualifier::Equals( Artist::desc, $value );
					}
					break;

			// Artist::gender == TEXT
				case Artist::gender:
					if (strlen($value) > 0) {
						$qualifiers[Artist::gender] = Qualifier::Equals( Artist::gender, $value );
					}
					break;

			// Artist::birth_date == DATE

			// Artist::death_date == DATE

			// Artist::pub_wanted == BOOLEAN
				case Artist::pub_wanted:
					$v = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
					if (is_null($v) == false) {
						$qualifiers[Artist::pub_wanted] = Qualifier::Equals( Artist::pub_wanted, $v );
					}
					break;

			// Artist::xurl == TEXT
				case Artist::xurl:
					if (strlen($value) > 0) {
						$qualifiers[Artist::xurl] = Qualifier::Equals( Artist::xurl, $value );
					}
					break;

			// Artist::xsource == TEXT
				case Artist::xsource:
					if (strlen($value) > 0) {
						$qualifiers[Artist::xsource] = Qualifier::Equals( Artist::xsource, $value );
					}
					break;

			// Artist::xid == TEXT
				case Artist::xid:
					if (strlen($value) > 0) {
						$qualifiers[Artist::xid] = Qualifier::Equals( Artist::xid, $value );
					}
					break;

			// Artist::xupdated == DATE

				default:
					/* no type specified for Artist::xupdated */
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
		return $this->allObjectsForKeyValue(Artist::name, $value, null, $limit);
	}


	public function allForDesc($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Artist::desc, $value, null, $limit);
	}


	public function allForGender($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Artist::gender, $value, null, $limit);
	}





	public function allForXurl($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Artist::xurl, $value, null, $limit);
	}


	public function allForXsource($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Artist::xsource, $value, null, $limit);
	}


	public function allForXid($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Artist::xid, $value, null, $limit);
	}




	/**
	 * Simple relationship fetches
	 */

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "artist_alias":
					return array( Artist::id, "artist_id"  );
					break;
				case "publication_artist":
					return array( Artist::id, "artist_id"  );
					break;
				case "series_artist":
					return array( Artist::id, "artist_id"  );
					break;
				case "story_arc_artist":
					return array( Artist::id, "artist_id"  );
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
			if ( isset($values['created']) == false ) {
				$default_created = $this->attributeDefaultValue( null, null, Artist::created);
				if ( is_null( $default_created ) == false ) {
					$values['created'] = $default_created;
				}
			}
			if ( isset($values['name']) == false ) {
				$default_name = $this->attributeDefaultValue( null, null, Artist::name);
				if ( is_null( $default_name ) == false ) {
					$values['name'] = $default_name;
				}
			}
			if ( isset($values['desc']) == false ) {
				$default_desc = $this->attributeDefaultValue( null, null, Artist::desc);
				if ( is_null( $default_desc ) == false ) {
					$values['desc'] = $default_desc;
				}
			}
			if ( isset($values['gender']) == false ) {
				$default_gender = $this->attributeDefaultValue( null, null, Artist::gender);
				if ( is_null( $default_gender ) == false ) {
					$values['gender'] = $default_gender;
				}
			}
			if ( isset($values['birth_date']) == false ) {
				$default_birth_date = $this->attributeDefaultValue( null, null, Artist::birth_date);
				if ( is_null( $default_birth_date ) == false ) {
					$values['birth_date'] = $default_birth_date;
				}
			}
			if ( isset($values['death_date']) == false ) {
				$default_death_date = $this->attributeDefaultValue( null, null, Artist::death_date);
				if ( is_null( $default_death_date ) == false ) {
					$values['death_date'] = $default_death_date;
				}
			}
			if ( isset($values['pub_wanted']) == false ) {
				$default_pub_wanted = $this->attributeDefaultValue( null, null, Artist::pub_wanted);
				if ( is_null( $default_pub_wanted ) == false ) {
					$values['pub_wanted'] = $default_pub_wanted;
				}
			}
			if ( isset($values['xurl']) == false ) {
				$default_xurl = $this->attributeDefaultValue( null, null, Artist::xurl);
				if ( is_null( $default_xurl ) == false ) {
					$values['xurl'] = $default_xurl;
				}
			}
			if ( isset($values['xsource']) == false ) {
				$default_xsource = $this->attributeDefaultValue( null, null, Artist::xsource);
				if ( is_null( $default_xsource ) == false ) {
					$values['xsource'] = $default_xsource;
				}
			}
			if ( isset($values['xid']) == false ) {
				$default_xid = $this->attributeDefaultValue( null, null, Artist::xid);
				if ( is_null( $default_xid ) == false ) {
					$values['xid'] = $default_xid;
				}
			}
			if ( isset($values['xupdated']) == false ) {
				$default_xupdated = $this->attributeDefaultValue( null, null, Artist::xupdated);
				if ( is_null( $default_xupdated ) == false ) {
					$values['xupdated'] = $default_xupdated;
				}
			}

			// default conversion for relationships
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Artist ) {
		}

		return parent::updateObject($object, $values);
	}

	/**
	 *	Delete functions
	 */
	public function deleteObject( DataObject $object = null)
	{
		if ( $object instanceof ArtistDBO )
		{
			$artist_alias_model = Model::Named('Artist_Alias');
			if ( $artist_alias_model->deleteAllForKeyValue(Artist_Alias::artist_id, $object->id) == false ) {
				return false;
			}
			$publication_artist_model = Model::Named('Publication_Artists');
			if ( $publication_artist_model->deleteAllForKeyValue(Publication_Artists::artist_id, $object->id) == false ) {
				return false;
			}
			$series_artist_model = Model::Named('Series_Artists');
			if ( $series_artist_model->deleteAllForKeyValue(Series_Artists::artist_id, $object->id) == false ) {
				return false;
			}
			$story_arc_artist_model = Model::Named('Story_Arc_Artist');
			if ( $story_arc_artist_model->deleteAllForKeyValue(Story_Arc_Artist::artist_id, $object->id) == false ) {
				return false;
			}
			return parent::deleteObject($object);
		}

		return false;
	}


	/**
	 * Named fetches
	 */
	public function objectForExternal( $xid, $xsrc )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		$qualifiers[] = Qualifier::Equals( 'xid', $xid);
		$qualifiers[] = Qualifier::Equals( 'xsource', $xsrc);

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
				throw new \Exception( "objectForExternal expected 1 result, but fetched " . count($result) );
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
				Artist::name
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesMap() {
		return array(
			Artist::created => Model::DATE_TYPE,
			Artist::name => Model::TEXT_TYPE,
			Artist::desc => Model::TEXTAREA_TYPE,
			Artist::gender => Model::TEXT_TYPE,
			Artist::birth_date => Model::DATE_TYPE,
			Artist::death_date => Model::DATE_TYPE,
			Artist::pub_wanted => Model::FLAG_TYPE,
			Artist::xurl => Model::TEXTAREA_TYPE,
			Artist::xsource => Model::TEXT_TYPE,
			Artist::xid => Model::TEXT_TYPE,
			Artist::xupdated => Model::DATE_TYPE
		);
	}

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) === false || is_null($object) == true) {
			switch ($attr) {
				case Artist::gender:
					return 'unknown';
				case Artist::pub_wanted:
					return Model::TERTIARY_TRUE;
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
				default:
					break;
			}
		}
		return $fkObject;
	}

	/**
	 * Validation
	 */
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
				Artist::created,
				"IMMUTABLE"
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
				Artist::name,
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
	function validate_gender($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_birth_date($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_death_date($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_pub_wanted($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false  ) {
			return null;
		}

		// boolean

		// Returns TRUE for "1", "true", "on" and "yes"
		// Returns FALSE for "0", "false", "off" and "no"
		// Returns NULL otherwise.
		$v = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
		if (is_null($v)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Artist::pub_wanted,
				"FILTER_VALIDATE_BOOLEAN"
			);
		}
		return null;
	}
	function validate_xurl($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_xsource($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_xid($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_xupdated($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
}

?>
