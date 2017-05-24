<?php

namespace model\media;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\media\PublicationDBO as PublicationDBO;

/* import related objects */
use \model\media\Series as Series;
use \model\media\SeriesDBO as SeriesDBO;
use \model\media\Media as Media;
use \model\media\MediaDBO as MediaDBO;
use \model\media\Story_Arc_Publication as Story_Arc_Publication;
use \model\media\Story_Arc_PublicationDBO as Story_Arc_PublicationDBO;
use \model\reading\Reading_Item as Reading_Item;
use \model\reading\Reading_ItemDBO as Reading_ItemDBO;
use \model\media\Publication_Character as Publication_Character;
use \model\media\Publication_CharacterDBO as Publication_CharacterDBO;
use \model\media\Publication_Artist as Publication_Artist;
use \model\media\Publication_ArtistDBO as Publication_ArtistDBO;

/** Generated class, do not edit.
 */
abstract class _Publication extends Model
{
	const TABLE = 'publication';

	// attribute keys
	const id = 'id';
	const series_id = 'series_id';
	const created = 'created';
	const name = 'name';
	const desc = 'desc';
	const pub_date = 'pub_date';
	const issue_num = 'issue_num';
	const issue_order = 'issue_order';
	const media_count = 'media_count';
	const xurl = 'xurl';
	const xsource = 'xsource';
	const xid = 'xid';
	const xupdated = 'xupdated';
	const search_date = 'search_date';

	// relationship keys
	const series = 'series';
	const media = 'media';
	const story_arc_publication = 'story_arc_publication';
	const reading_items = 'reading_items';
	const publication_characters = 'publication_characters';
	const publication_artists = 'publication_artists';

	public function modelName()
	{
		return "Publication";
	}

	public function dboName()
	{
		return '\model\media\PublicationDBO';
	}

	public function tableName() { return Publication::TABLE; }
	public function tablePK() { return Publication::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Publication::issue_order),
			array( 'asc' => Publication::pub_date)
		);
	}

	public function allColumnNames()
	{
		return array(
			Publication::id,
			Publication::series_id,
			Publication::created,
			Publication::name,
			Publication::desc,
			Publication::pub_date,
			Publication::issue_num,
			Publication::issue_order,
			Publication::media_count,
			Publication::xurl,
			Publication::xsource,
			Publication::xid,
			Publication::xupdated,
			Publication::search_date
		);
	}

	public function allAttributes()
	{
		return array(
			Publication::created,
			Publication::name,
			Publication::desc,
			Publication::pub_date,
			Publication::issue_num,
			Publication::issue_order,
			Publication::media_count,
			Publication::xurl,
			Publication::xsource,
			Publication::xid,
			Publication::xupdated,
			Publication::search_date
		);
	}

	public function allForeignKeys()
	{
		return array(Publication::series_id);
	}

	public function allRelationshipNames()
	{
		return array(
			Publication::series,
			Publication::media,
			Publication::story_arc_publication,
			Publication::reading_items,
			Publication::publication_characters,
			Publication::publication_artists
		);
	}

	public function attributes()
	{
		return array(
			Publication::created => array('type' => 'DATE'),
			Publication::name => array('length' => 256,'type' => 'TEXT'),
			Publication::desc => array('length' => 4096,'type' => 'TEXT'),
			Publication::pub_date => array('type' => 'DATE'),
			Publication::issue_num => array('length' => 256,'type' => 'TEXT'),
			Publication::issue_order => array('type' => 'INTEGER'),
			Publication::media_count => array('type' => 'INTEGER'),
			Publication::xurl => array('length' => 1024,'type' => 'TEXT'),
			Publication::xsource => array('length' => 256,'type' => 'TEXT'),
			Publication::xid => array('length' => 256,'type' => 'TEXT'),
			Publication::xupdated => array('type' => 'DATE'),
			Publication::search_date => array('type' => 'DATE')
		);
	}

	public function relationships()
	{
		return array(
			Publication::series => array(
				'destination' => 'Series',
				'ownsDestination' => false,
				'isMandatory' => true,
				'isToMany' => false,
				'joins' => array( 'series_id' => 'id')
			),
			Publication::media => array(
				'destination' => 'Media',
				'ownsDestination' => true,
				'isMandatory' => false,
				'isToMany' => true,
				'joins' => array( 'id' => 'publication_id')
			),
			Publication::story_arc_publication => array(
				'destination' => 'Story_Arc_Publication',
				'ownsDestination' => true,
				'isMandatory' => false,
				'isToMany' => true,
				'joins' => array( 'id' => 'publication_id')
			),
			Publication::reading_items => array(
				'destination' => 'Reading_Item',
				'ownsDestination' => true,
				'isMandatory' => false,
				'isToMany' => true,
				'joins' => array( 'id' => 'publication_id')
			),
			Publication::publication_characters => array(
				'destination' => 'Publication_Character',
				'ownsDestination' => true,
				'isMandatory' => false,
				'isToMany' => true,
				'joins' => array( 'id' => 'publication_id')
			),
			Publication::publication_artists => array(
				'destination' => 'Publication_Artist',
				'ownsDestination' => true,
				'isMandatory' => false,
				'isToMany' => true,
				'joins' => array( 'id' => 'publication_id')
			)
		);
	}

	public function searchQualifiers( array $query )
	{
		$qualifiers = array();
		if ( is_array($query) ) {
			foreach( $query as $attr => $value ) {
				switch ($attr) {
			// Publication::id == INTEGER

			// Publication::series_id == INTEGER
				case Publication::series_id:
					if ( intval($value) > 0 ) {
						$qualifiers[Publication::series_id] = Qualifier::Equals( Publication::series_id, intval($value) );
					}
					break;

			// Publication::created == DATE

			// Publication::name == TEXT
				case Publication::name:
					if (strlen($value) > 0) {
						$qualifiers[Publication::name] = Qualifier::Equals( Publication::name, $value );
					}
					break;

			// Publication::desc == TEXT
				case Publication::desc:
					if (strlen($value) > 0) {
						$qualifiers[Publication::desc] = Qualifier::Equals( Publication::desc, $value );
					}
					break;

			// Publication::pub_date == DATE

			// Publication::issue_num == TEXT
				case Publication::issue_num:
					if (strlen($value) > 0) {
						$qualifiers[Publication::issue_num] = Qualifier::Equals( Publication::issue_num, $value );
					}
					break;

			// Publication::issue_order == INTEGER
				case Publication::issue_order:
					if ( intval($value) > 0 ) {
						$qualifiers[Publication::issue_order] = Qualifier::Equals( Publication::issue_order, intval($value) );
					}
					break;

			// Publication::media_count == INTEGER
				case Publication::media_count:
					if ( intval($value) > 0 ) {
						$qualifiers[Publication::media_count] = Qualifier::Equals( Publication::media_count, intval($value) );
					}
					break;

			// Publication::xurl == TEXT
				case Publication::xurl:
					if (strlen($value) > 0) {
						$qualifiers[Publication::xurl] = Qualifier::Equals( Publication::xurl, $value );
					}
					break;

			// Publication::xsource == TEXT
				case Publication::xsource:
					if (strlen($value) > 0) {
						$qualifiers[Publication::xsource] = Qualifier::Equals( Publication::xsource, $value );
					}
					break;

			// Publication::xid == TEXT
				case Publication::xid:
					if (strlen($value) > 0) {
						$qualifiers[Publication::xid] = Qualifier::Equals( Publication::xid, $value );
					}
					break;

			// Publication::xupdated == DATE

			// Publication::search_date == DATE

				default:
					/* no type specified for Publication::search_date */
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
		return $this->allObjectsForKeyValue(Publication::name, $value, null, $limit);
	}


	public function allForDesc($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Publication::desc, $value, null, $limit);
	}



	public function allForIssue_num($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Publication::issue_num, $value, null, $limit);
	}


	public function allForIssue_order($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Publication::issue_order, $value, null, $limit);
	}

	public function allForMedia_count($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Publication::media_count, $value, null, $limit);
	}

	public function allForXurl($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Publication::xurl, $value, null, $limit);
	}


	public function allForXsource($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Publication::xsource, $value, null, $limit);
	}


	public function allForXid($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Publication::xid, $value, null, $limit);
	}





	/**
	 * Simple relationship fetches
	 */
	public function allForSeries($obj, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForFK(Publication::series_id, $obj, $this->sortOrder(), $limit);
	}

	public function countForSeries($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Publication::series_id, $obj );
		}
		return false;
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "series":
					return array( Publication::series_id, "id"  );
					break;
				case "media":
					return array( Publication::id, "publication_id"  );
					break;
				case "story_arc_publication":
					return array( Publication::id, "publication_id"  );
					break;
				case "reading_item":
					return array( Publication::id, "publication_id"  );
					break;
				case "publication_character":
					return array( Publication::id, "publication_id"  );
					break;
				case "publication_artist":
					return array( Publication::id, "publication_id"  );
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
				$default_created = $this->attributeDefaultValue( null, null, Publication::created);
				if ( is_null( $default_created ) == false ) {
					$values['created'] = $default_created;
				}
			}
			if ( isset($values['name']) == false ) {
				$default_name = $this->attributeDefaultValue( null, null, Publication::name);
				if ( is_null( $default_name ) == false ) {
					$values['name'] = $default_name;
				}
			}
			if ( isset($values['desc']) == false ) {
				$default_desc = $this->attributeDefaultValue( null, null, Publication::desc);
				if ( is_null( $default_desc ) == false ) {
					$values['desc'] = $default_desc;
				}
			}
			if ( isset($values['pub_date']) == false ) {
				$default_pub_date = $this->attributeDefaultValue( null, null, Publication::pub_date);
				if ( is_null( $default_pub_date ) == false ) {
					$values['pub_date'] = $default_pub_date;
				}
			}
			if ( isset($values['issue_num']) == false ) {
				$default_issue_num = $this->attributeDefaultValue( null, null, Publication::issue_num);
				if ( is_null( $default_issue_num ) == false ) {
					$values['issue_num'] = $default_issue_num;
				}
			}
			if ( isset($values['issue_order']) == false ) {
				$default_issue_order = $this->attributeDefaultValue( null, null, Publication::issue_order);
				if ( is_null( $default_issue_order ) == false ) {
					$values['issue_order'] = $default_issue_order;
				}
			}
			if ( isset($values['media_count']) == false ) {
				$default_media_count = $this->attributeDefaultValue( null, null, Publication::media_count);
				if ( is_null( $default_media_count ) == false ) {
					$values['media_count'] = $default_media_count;
				}
			}
			if ( isset($values['xurl']) == false ) {
				$default_xurl = $this->attributeDefaultValue( null, null, Publication::xurl);
				if ( is_null( $default_xurl ) == false ) {
					$values['xurl'] = $default_xurl;
				}
			}
			if ( isset($values['xsource']) == false ) {
				$default_xsource = $this->attributeDefaultValue( null, null, Publication::xsource);
				if ( is_null( $default_xsource ) == false ) {
					$values['xsource'] = $default_xsource;
				}
			}
			if ( isset($values['xid']) == false ) {
				$default_xid = $this->attributeDefaultValue( null, null, Publication::xid);
				if ( is_null( $default_xid ) == false ) {
					$values['xid'] = $default_xid;
				}
			}
			if ( isset($values['xupdated']) == false ) {
				$default_xupdated = $this->attributeDefaultValue( null, null, Publication::xupdated);
				if ( is_null( $default_xupdated ) == false ) {
					$values['xupdated'] = $default_xupdated;
				}
			}
			if ( isset($values['search_date']) == false ) {
				$default_search_date = $this->attributeDefaultValue( null, null, Publication::search_date);
				if ( is_null( $default_search_date ) == false ) {
					$values['search_date'] = $default_search_date;
				}
			}

			// default conversion for relationships
			if ( isset($values['series']) ) {
				$local_series = $values['series'];
				if ( $local_series instanceof SeriesDBO) {
					$values[Publication::series_id] = $local_series->id;
				}
				else if ( is_integer( $local_series) ) {
					$params[Publication::series_id] = $local_series;
				}
			}
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Publication ) {
			if ( isset($values['series']) ) {
				$local_series = $values['series'];
				if ( $local_series instanceof SeriesDBO) {
					$values[Publication::series_id] = $local_series->id;
				}
				else if ( is_integer( $local_series) ) {
					$params[Publication::series_id] = $values['series'];
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
		if ( $object instanceof PublicationDBO )
		{
			// does not own series Series
			$media_model = Model::Named('Media');
			if ( $media_model->deleteAllForKeyValue(Media::publication_id, $object->id) == false ) {
				return false;
			}
			$story_arc_publication_model = Model::Named('Story_Arc_Publication');
			if ( $story_arc_publication_model->deleteAllForKeyValue(Story_Arc_Publication::publication_id, $object->id) == false ) {
				return false;
			}
			$reading_item_model = Model::Named('Reading_Item');
			if ( $reading_item_model->deleteAllForKeyValue(Reading_Item::publication_id, $object->id) == false ) {
				return false;
			}
			$publication_character_model = Model::Named('Publication_Character');
			if ( $publication_character_model->deleteAllForKeyValue(Publication_Character::publication_id, $object->id) == false ) {
				return false;
			}
			$publication_artist_model = Model::Named('Publication_Artist');
			if ( $publication_artist_model->deleteAllForKeyValue(Publication_Artist::publication_id, $object->id) == false ) {
				return false;
			}
			return parent::deleteObject($object);
		}

		return false;
	}

	public function deleteAllForSeries(SeriesDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForSeries($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForSeries($obj);
			}
		}
		return $success;
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
				Publication::name
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesMap() {
		return array(
			Publication::series_id => Model::TO_ONE_TYPE,
			Publication::created => Model::DATE_TYPE,
			Publication::name => Model::TEXT_TYPE,
			Publication::desc => Model::TEXTAREA_TYPE,
			Publication::pub_date => Model::DATE_TYPE,
			Publication::issue_num => Model::TEXT_TYPE,
			Publication::issue_order => Model::INT_TYPE,
			Publication::media_count => Model::INT_TYPE,
			Publication::xurl => Model::TEXTAREA_TYPE,
			Publication::xsource => Model::TEXT_TYPE,
			Publication::xid => Model::TEXT_TYPE,
			Publication::xupdated => Model::DATE_TYPE,
			Publication::search_date => Model::DATE_TYPE
		);
	}

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) === false || is_null($object) == true) {
			switch ($attr) {
				case Publication::issue_num:
					return 0;
				case Publication::issue_order:
					return 0;
				case Publication::media_count:
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
				case Publication::series_id:
					$series_model = Model::Named('Series');
					$fkObject = $series_model->objectForId( $value );
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
	function validate_series_id($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Publication::series_id,
				"FIELD_EMPTY"
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
				Publication::created,
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
				Publication::name,
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
	function validate_issue_num($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_issue_order($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Publication::issue_order,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
	function validate_media_count($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Publication::media_count,
				"FILTER_VALIDATE_INT"
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
	function validate_search_date($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
}

?>
