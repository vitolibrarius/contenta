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
use \model\media\Publication_Characters as Publication_Characters;
use \model\media\Publication_CharactersDBO as Publication_CharactersDBO;

/** Sample Creation script */
		/** PUBLICATION */
/*
		$sql = "CREATE TABLE IF NOT EXISTS publication ( "
			. Publication::id . " INTEGER PRIMARY KEY, "
			. Publication::series_id . " INTEGER, "
			. Publication::created . " INTEGER, "
			. Publication::name . " TEXT, "
			. Publication::desc . " TEXT, "
			. Publication::pub_date . " INTEGER, "
			. Publication::issue_num . " TEXT, "
			. Publication::media_count . " INTEGER, "
			. Publication::xurl . " TEXT, "
			. Publication::xsource . " TEXT, "
			. Publication::xid . " TEXT, "
			. Publication::xupdated . " INTEGER, "
			. "FOREIGN KEY (". Publication::series_id .") REFERENCES " . Series::TABLE . "(" . Series::id . ")"
		. ")";
		$this->sqlite_execute( "publication", $sql, "Create table publication" );

		$sql = 'CREATE  INDEX IF NOT EXISTS publication_name on publication (name)';
		$this->sqlite_execute( "publication", $sql, "Index on publication (name)" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS publication_xidxsource on publication (xid,xsource)';
		$this->sqlite_execute( "publication", $sql, "Index on publication (xid,xsource)" );
*/
abstract class _Publication extends Model
{
	const TABLE = 'publication';
	const id = 'id';
	const series_id = 'series_id';
	const created = 'created';
	const name = 'name';
	const desc = 'desc';
	const pub_date = 'pub_date';
	const issue_num = 'issue_num';
	const media_count = 'media_count';
	const xurl = 'xurl';
	const xsource = 'xsource';
	const xid = 'xid';
	const xupdated = 'xupdated';

	public function tableName() { return Publication::TABLE; }
	public function tablePK() { return Publication::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Publication::issue_num),
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
			Publication::media_count,
			Publication::xurl,
			Publication::xsource,
			Publication::xid,
			Publication::xupdated
		);
	}

	/**
	 *	Simple fetches
	 */



	public function allForName($value)
	{
		return $this->allObjectsForKeyValue(Publication::name, $value);
	}


	public function allForDesc($value)
	{
		return $this->allObjectsForKeyValue(Publication::desc, $value);
	}



	public function allForIssue_num($value)
	{
		return $this->allObjectsForKeyValue(Publication::issue_num, $value);
	}


	public function allForMedia_count($value)
	{
		return $this->allObjectsForKeyValue(Publication::media_count, $value);
	}

	public function allForXurl($value)
	{
		return $this->allObjectsForKeyValue(Publication::xurl, $value);
	}


	public function allForXsource($value)
	{
		return $this->allObjectsForKeyValue(Publication::xsource, $value);
	}


	public function allForXid($value)
	{
		return $this->allObjectsForKeyValue(Publication::xid, $value);
	}




	public function allForSeries($obj)
	{
		return $this->allObjectsForFK(Publication::series_id, $obj, $this->sortOrder(), 50);
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
				case "publication_characters":
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
			if ( $media_model->deleteAllForKeyValue(Media::publication_id, $this->id) == false ) {
				return false;
			}
			$story_arc_publication_model = Model::Named('Story_Arc_Publication');
			if ( $story_arc_publication_model->deleteAllForKeyValue(Story_Arc_Publication::publication_id, $this->id) == false ) {
				return false;
			}
			$publication_characters_model = Model::Named('Publication_Characters');
			if ( $publication_characters_model->deleteAllForKeyValue(Publication_Characters::publication_id, $this->id) == false ) {
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
			Publication::media_count => Model::INT_TYPE,
			Publication::xurl => Model::TEXTAREA_TYPE,
			Publication::xsource => Model::TEXT_TYPE,
			Publication::xid => Model::TEXT_TYPE,
			Publication::xupdated => Model::DATE_TYPE
		);
	}

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) === false || is_null($object) == true) {
			switch ($attr) {
				case Publication::issue_num:
					return 0;
				case Publication::media_count:
					return 0;
			}
		}
		return parent::attributeDefaultValue($object, $type, $attr);
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
}

?>
