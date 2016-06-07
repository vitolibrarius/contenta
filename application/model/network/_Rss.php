<?php

namespace model\network;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
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
				case "flux":
					return array( Rss::guid, "src_guid"  );
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
			if ( isset($values['endpoint']) ) {
				$local_endpoint = $values['endpoint'];
				if ( $local_endpoint instanceof EndpointDBO) {
					$values[Rss::endpoint_id] = $local_endpoint->id;
				}
				else if ( is_integer( $local_endpoint) ) {
					$params[Rss::endpoint_id] = $local_endpoint;
				}
			}
			if ( isset($values['flux']) ) {
				$local_flux = $values['flux'];
				if ( $local_flux instanceof FluxDBO) {
					$values[Rss::guid] = $local_flux->src_guid;
				}
				else if ( is_string( $local_flux) ) {
					$params[Rss::guid] = $local_flux;
				}
			}
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Rss ) {
			if ( isset($values['endpoint']) ) {
				$local_endpoint = $values['endpoint'];
				if ( $local_endpoint instanceof EndpointDBO) {
					$values[Rss::endpoint_id] = $local_endpoint->id;
				}
				else if ( is_integer( $local_endpoint) ) {
					$params[Rss::endpoint_id] = $values['endpoint'];
				}
			}
			if ( isset($values['flux']) ) {
				$local_flux = $values['flux'];
				if ( $local_flux instanceof FluxDBO) {
					$values[Rss::guid] = $local_flux->src_guid;
				}
				else if ( is_string( $local_flux) ) {
					$params[Rss::guid] = $values['flux'];
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


	/** Set attributes */
	public function setEndpoint_id( RssDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Rss::endpoint_id => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setCreated( RssDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Rss::created => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setTitle( RssDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Rss::title => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setDesc( RssDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Rss::desc => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setPub_date( RssDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Rss::pub_date => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setGuid( RssDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Rss::guid => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setClean_name( RssDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Rss::clean_name => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setClean_issue( RssDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Rss::clean_issue => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setClean_year( RssDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Rss::clean_year => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setEnclosure_url( RssDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Rss::enclosure_url => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setEnclosure_length( RssDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Rss::enclosure_length => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setEnclosure_mime( RssDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Rss::enclosure_mime => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setEnclosure_hash( RssDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Rss::enclosure_hash => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setEnclosure_password( RssDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Rss::enclosure_password => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}


	/** Validation */
	function validate_endpoint_id($object = null, $value)
	{
		if (isset($object->endpoint_id) === false && empty($value) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Rss::endpoint_id,
				"FIELD_EMPTY"
			);
		}
		return null;
	}
	function validate_created($object = null, $value)
	{
		if ( isset($object, $object->created) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Rss::created,
				"IMMUTABLE"
			);
		}
		return null;
	}
	function validate_title($object = null, $value)
	{
		$value = trim($value);
		if (empty($value)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Rss::title,
				"FIELD_EMPTY"
			);
		}
		return null;
	}
	function validate_desc($object = null, $value)
	{
		$value = trim($value);
		return null;
	}
	function validate_pub_date($object = null, $value)
	{
		if (empty($value)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Rss::pub_date,
				"FIELD_EMPTY"
			);
		}
		return null;
	}
	function validate_guid($object = null, $value)
	{
		$value = trim($value);
		if (isset($object->guid) === false && empty($value) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Rss::guid,
				"FIELD_EMPTY"
			);
		}
		return null;
	}
	function validate_clean_name($object = null, $value)
	{
		$value = trim($value);
		if (empty($value)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Rss::clean_name,
				"FIELD_EMPTY"
			);
		}
		return null;
	}
	function validate_clean_issue($object = null, $value)
	{
		$value = trim($value);
		return null;
	}
	function validate_clean_year($object = null, $value)
	{
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Rss::clean_year,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
	function validate_enclosure_url($object = null, $value)
	{
		$value = trim($value);
		if (empty($value)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Rss::enclosure_url,
				"FIELD_EMPTY"
			);
		}
		if ( filter_var($value, FILTER_VALIDATE_URL) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Rss::enclosure_url,
				"FILTER_VALIDATE_URL"
			);
		}
		return null;
	}
	function validate_enclosure_length($object = null, $value)
	{
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Rss::enclosure_length,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
	function validate_enclosure_mime($object = null, $value)
	{
		$value = trim($value);
		return null;
	}
	function validate_enclosure_hash($object = null, $value)
	{
		$value = trim($value);
		return null;
	}
	function validate_enclosure_password($object = null, $value)
	{
		if ( is_null($value) ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Rss::enclosure_password,
				"FIELD_EMPTY"
			);
		}

		// Returns TRUE for "1", "true", "on" and "yes"
		// Returns FALSE for "0", "false", "off" and "no"
		// Returns NULL otherwise.
		$v = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
		if (is_null($v)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Rss::enclosure_password,
				"FILTER_VALIDATE_BOOLEAN"
			);
		}
		return null;
	}
}

?>
