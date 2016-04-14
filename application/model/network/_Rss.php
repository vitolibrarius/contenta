<?php

namespace model\network;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \model\network\RssDBO as RssDBO;

/* import related objects */
use \model\Endpoint as Endpoint;
use \model\EndpointDBO as EndpointDBO;
use \model\Flux as Flux;
use \model\FluxDBO as FluxDBO;

/** Sample Creation script */
		/** RSS */
/*
		$sql = "CREATE TABLE IF NOT EXISTS rss ( "
			. Rss::id . " INTEGER PRIMARY KEY, "
			. Rss::endpoint_id . " INTEGER, "
			. Rss::created . " INTEGER, "
			. Rss::title . " TEXT, "
			. Rss::desc . " TEXT, "
			. Rss::pub_date . " INTEGER, "
			. Rss::guid . " TEXT, "
			. Rss::clean_name . " TEXT, "
			. Rss::clean_issue . " TEXT, "
			. Rss::clean_year . " INTEGER, "
			. Rss::enclosure_url . " TEXT, "
			. Rss::enclosure_length . " INTEGER, "
			. Rss::enclosure_mime . " TEXT, "
			. Rss::enclosure_hash . " TEXT, "
			. Rss::enclosure_password . " INTEGER, "
			. "FOREIGN KEY (". Rss::endpoint_id .") REFERENCES " . Endpoint::TABLE . "(" . Endpoint::id . ")"
		. ")";
		$this->sqlite_execute( "rss", $sql, "Create table rss" );

		$sql = 'CREATE  INDEX IF NOT EXISTS rss_clean_nameclean_issueclean_year on rss (clean_name,clean_issue,clean_year)';
		$this->sqlite_execute( "rss", $sql, "Index on rss (clean_name,clean_issue,clean_year)" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS rss_guid on rss (guid)';
		$this->sqlite_execute( "rss", $sql, "Index on rss (guid)" );
*/
abstract class _Rss extends Model
{
	const TABLE = 'rss';
	const id = 'id';
	const endpoint_id = 'endpoint_id';
	const created = 'created';
	const title = 'title';
	const desc = 'desc';
	const pub_date = 'pub_date';
	const guid = 'guid';
	const clean_name = 'clean_name';
	const clean_issue = 'clean_issue';
	const clean_year = 'clean_year';
	const enclosure_url = 'enclosure_url';
	const enclosure_length = 'enclosure_length';
	const enclosure_mime = 'enclosure_mime';
	const enclosure_hash = 'enclosure_hash';
	const enclosure_password = 'enclosure_password';

	public function tableName() { return Rss::TABLE; }
	public function tablePK() { return Rss::id; }

	public function sortOrder()
	{
		return array(
			array( 'desc' => Rss::created)
		);
	}

	public function allColumnNames()
	{
		return array(
			Rss::id,
			Rss::endpoint_id,
			Rss::created,
			Rss::title,
			Rss::desc,
			Rss::pub_date,
			Rss::guid,
			Rss::clean_name,
			Rss::clean_issue,
			Rss::clean_year,
			Rss::enclosure_url,
			Rss::enclosure_length,
			Rss::enclosure_mime,
			Rss::enclosure_hash,
			Rss::enclosure_password
		);
	}

	/**
	 *	Simple fetches
	 */
	public function allForTitle($value)
	{
		return $this->allObjectsForKeyValue(Rss::title, $value);
	}

	public function allForDesc($value)
	{
		return $this->allObjectsForKeyValue(Rss::desc, $value);
	}

	public function objectForGuid($value)
	{
		return $this->singleObjectForKeyValue(Rss::guid, $value);
	}

	public function allForClean_name($value)
	{
		return $this->allObjectsForKeyValue(Rss::clean_name, $value);
	}

	public function allForClean_issue($value)
	{
		return $this->allObjectsForKeyValue(Rss::clean_issue, $value);
	}

	public function allForEnclosure_url($value)
	{
		return $this->allObjectsForKeyValue(Rss::enclosure_url, $value);
	}

	public function allForEnclosure_mime($value)
	{
		return $this->allObjectsForKeyValue(Rss::enclosure_mime, $value);
	}

	public function allForEnclosure_hash($value)
	{
		return $this->allObjectsForKeyValue(Rss::enclosure_hash, $value);
	}


	public function allForEndpoint($obj)
	{
		return $this->allObjectsForFK(Rss::endpoint_id, $obj, $this->sortOrder(), 50);
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "endpoint":
					return array( Rss::endpoint_id, "id"  );
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
	public function base_create( $endpoint, $title, $desc, $pub_date, $guid, $clean_name, $clean_issue, $clean_year, $enclosure_url, $enclosure_length, $enclosure_mime, $enclosure_hash, $enclosure_password)
	{
		$obj = false;
		if ( isset($endpoint, $title, $pub_date, $guid, $clean_name, $enclosure_url) ) {
			$params = array(
				Rss::created => time(),
				Rss::title => (isset($title) ? $title : null),
				Rss::desc => (isset($desc) ? $desc : null),
				Rss::pub_date => (isset($pub_date) ? $pub_date : time()),
				Rss::guid => (isset($guid) ? $guid : null),
				Rss::clean_name => (isset($clean_name) ? $clean_name : null),
				Rss::clean_issue => (isset($clean_issue) ? $clean_issue : null),
				Rss::clean_year => (isset($clean_year) ? $clean_year : null),
				Rss::enclosure_url => (isset($enclosure_url) ? $enclosure_url : null),
				Rss::enclosure_length => (isset($enclosure_length) ? $enclosure_length : null),
				Rss::enclosure_mime => (isset($enclosure_mime) ? $enclosure_mime : null),
				Rss::enclosure_hash => (isset($enclosure_hash) ? $enclosure_hash : null),
				Rss::enclosure_password => (isset($enclosure_password) ? $enclosure_password : Model::TERTIARY_TRUE),
			);

			if ( isset($endpoint) ) {
				if ( $endpoint instanceof EndpointDBO) {
					$params[Rss::endpoint_id] = $endpoint->id;
				}
				else if (  is_integer($endpoint) ) {
					$params[Rss::endpoint_id] = $endpoint;
				}
			}

			list( $obj, $errorList ) = $this->createObject($params);
			if ( is_array($errorList) ) {
				return $errorList;
			}
		}
		return $obj;
	}

	public function base_update( RssDBO $obj,
		$endpoint, $title, $desc, $pub_date, $guid, $clean_name, $clean_issue, $clean_year, $enclosure_url, $enclosure_length, $enclosure_mime, $enclosure_hash, $enclosure_password)
	{
		if ( isset( $obj ) && is_null($obj) == false ) {
			$updates = array();

			if (isset($title) && (isset($obj->title) == false || $title != $obj->title)) {
				$updates[Rss::title] = $title;
			}
			if (isset($desc) && (isset($obj->desc) == false || $desc != $obj->desc)) {
				$updates[Rss::desc] = $desc;
			}
			if (isset($pub_date) && (isset($obj->pub_date) == false || $pub_date != $obj->pub_date)) {
				$updates[Rss::pub_date] = $pub_date;
			}
			if (isset($guid) && (isset($obj->guid) == false || $guid != $obj->guid)) {
				$updates[Rss::guid] = $guid;
			}
			if (isset($clean_name) && (isset($obj->clean_name) == false || $clean_name != $obj->clean_name)) {
				$updates[Rss::clean_name] = $clean_name;
			}
			if (isset($clean_issue) && (isset($obj->clean_issue) == false || $clean_issue != $obj->clean_issue)) {
				$updates[Rss::clean_issue] = $clean_issue;
			}
			if (isset($clean_year) && (isset($obj->clean_year) == false || $clean_year != $obj->clean_year)) {
				$updates[Rss::clean_year] = $clean_year;
			}
			if (isset($enclosure_url) && (isset($obj->enclosure_url) == false || $enclosure_url != $obj->enclosure_url)) {
				$updates[Rss::enclosure_url] = $enclosure_url;
			}
			if (isset($enclosure_length) && (isset($obj->enclosure_length) == false || $enclosure_length != $obj->enclosure_length)) {
				$updates[Rss::enclosure_length] = $enclosure_length;
			}
			if (isset($enclosure_mime) && (isset($obj->enclosure_mime) == false || $enclosure_mime != $obj->enclosure_mime)) {
				$updates[Rss::enclosure_mime] = $enclosure_mime;
			}
			if (isset($enclosure_hash) && (isset($obj->enclosure_hash) == false || $enclosure_hash != $obj->enclosure_hash)) {
				$updates[Rss::enclosure_hash] = $enclosure_hash;
			}
			if (isset($enclosure_password) && (isset($obj->enclosure_password) == false || $enclosure_password != $obj->enclosure_password)) {
				$updates[Rss::enclosure_password] = $enclosure_password;
			}

			if ( isset($endpoint) ) {
				if ( $endpoint instanceof EndpointDBO) {
					$updates[Rss::endpoint_id] = $endpoint->id;
				}
				else if (  is_integer($endpoint) ) {
					$updates[Rss::endpoint_id] = $endpoint;
				}
			}

			if ( count($updates) > 0 ) {
				list($obj, $errorList) = $this->updateObject( $obj, $updates );
				if ( is_array($errorList) ) {
					return $errorList;
				}
			}
		}
		return $obj;
	}

	/**
	 *	Delete functions
	 */
	public function deleteObject( DataObject $object = null)
	{
		if ( $object instanceof Rss )
		{
			// does not own Endpoint
			// does not own Flux
			return parent::deleteObject($object);
		}

		return false;
	}

	public function deleteAllForEndpoint(EndpointDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForEndpoint($obj);
			foreach ($array as $key => $value) {
				if ($this->deleteObject($value) == false) {
					$success = false;
					break;
				}
			}
		}
		return $success;
	}

	/**
	 *	Named fetches
	 */
	public function objectsForNameIssueYear( $name, $issue, $year )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		if ( isset($name)) {
			$qualifiers[] = Qualifier::Like( 'clean_name', $name, SQL::SQL_LIKE_BOTH);
		}
		if ( isset($issue)) {
			$qualifiers[] = Qualifier::Equals( 'clean_issue', $issue);
		}
		if ( isset($year)) {
			$qualifiers[] = Qualifier::Equals( 'clean_year', $year);
		}

		if ( count($qualifiers) > 0 ) {
			$select->where( Qualifier::Combine( 'AND', $qualifiers ));
		}

		$result = $select->fetchAll();
		return $result;
	}

	public function objectForEndpointGUID( $endpoint, $guid )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		$qualifiers[] = Qualifier::FK( 'endpoint_id', $endpoint);
		$qualifiers[] = Qualifier::Equals( 'guid', $guid);

		if ( count($qualifiers) > 0 ) {
			$select->where( Qualifier::Combine( 'AND', $qualifiers ));
		}

		$result = $select->fetchAll();
		return $result;
	}

}

?>
