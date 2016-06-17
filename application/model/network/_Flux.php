<?php

namespace model\network;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\network\FluxDBO as FluxDBO;

/* import related objects */
use \model\Endpoint as Endpoint;
use \model\EndpointDBO as EndpointDBO;

/** Sample Creation script */
		/** FLUX */
/*
		$sql = "CREATE TABLE IF NOT EXISTS flux ( "
			. Flux::id . " INTEGER PRIMARY KEY, "
			. Flux::created . " INTEGER, "
			. Flux::name . " TEXT, "
			. Flux::flux_hash . " TEXT, "
			. Flux::flux_error . " INTEGER, "
			. Flux::src_endpoint . " INTEGER, "
			. Flux::src_guid . " TEXT, "
			. Flux::src_url . " TEXT, "
			. Flux::src_status . " TEXT, "
			. Flux::src_pub_date . " INTEGER, "
			. Flux::dest_endpoint . " INTEGER, "
			. Flux::dest_guid . " TEXT, "
			. Flux::dest_status . " TEXT, "
			. Flux::dest_submission . " INTEGER, "
			. "FOREIGN KEY (". Flux::src_endpoint .") REFERENCES " . Endpoint::TABLE . "(" . Endpoint::id . ")"
			. "FOREIGN KEY (". Flux::dest_endpoint .") REFERENCES " . Endpoint::TABLE . "(" . Endpoint::id . "),"
		. ")";
		$this->sqlite_execute( "flux", $sql, "Create table flux" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS flux_src_endpointsrc_guid on flux (src_endpoint,src_guid)';
		$this->sqlite_execute( "flux", $sql, "Index on flux (src_endpoint,src_guid)" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS flux_dest_endpointdest_guid on flux (dest_endpoint,dest_guid)';
		$this->sqlite_execute( "flux", $sql, "Index on flux (dest_endpoint,dest_guid)" );
		$sql = 'CREATE  INDEX IF NOT EXISTS flux_flux_hash on flux (flux_hash)';
		$this->sqlite_execute( "flux", $sql, "Index on flux (flux_hash)" );
*/
abstract class _Flux extends Model
{
	const TABLE = 'flux';
	const id = 'id';
	const created = 'created';
	const name = 'name';
	const flux_hash = 'flux_hash';
	const flux_error = 'flux_error';
	const src_endpoint = 'src_endpoint';
	const src_guid = 'src_guid';
	const src_url = 'src_url';
	const src_status = 'src_status';
	const src_pub_date = 'src_pub_date';
	const dest_endpoint = 'dest_endpoint';
	const dest_guid = 'dest_guid';
	const dest_status = 'dest_status';
	const dest_submission = 'dest_submission';

	public function tableName() { return Flux::TABLE; }
	public function tablePK() { return Flux::id; }

	public function sortOrder()
	{
		return array(
			array( 'desc' => Flux::created)
		);
	}

	public function allColumnNames()
	{
		return array(
			Flux::id,
			Flux::created,
			Flux::name,
			Flux::flux_hash,
			Flux::flux_error,
			Flux::src_endpoint,
			Flux::src_guid,
			Flux::src_url,
			Flux::src_status,
			Flux::src_pub_date,
			Flux::dest_endpoint,
			Flux::dest_guid,
			Flux::dest_status,
			Flux::dest_submission
		);
	}

	/**
	 *	Simple fetches
	 */


	public function allForName($value)
	{
		return $this->allObjectsForKeyValue(Flux::name, $value);
	}


	public function allForFlux_hash($value)
	{
		return $this->allObjectsForKeyValue(Flux::flux_hash, $value);
	}




	public function allForSrc_guid($value)
	{
		return $this->allObjectsForKeyValue(Flux::src_guid, $value);
	}


	public function allForSrc_url($value)
	{
		return $this->allObjectsForKeyValue(Flux::src_url, $value);
	}


	public function allForSrc_status($value)
	{
		return $this->allObjectsForKeyValue(Flux::src_status, $value);
	}




	public function allForDest_guid($value)
	{
		return $this->allObjectsForKeyValue(Flux::dest_guid, $value);
	}


	public function allForDest_status($value)
	{
		return $this->allObjectsForKeyValue(Flux::dest_status, $value);
	}




	public function allForSource_endpoint($obj)
	{
		return $this->allObjectsForFK(Flux::src_endpoint, $obj, $this->sortOrder(), 50);
	}
	public function allForDestination_endpoint($obj)
	{
		return $this->allObjectsForFK(Flux::dest_endpoint, $obj, $this->sortOrder(), 50);
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "endpoint":
					return array( Flux::src_endpoint, "id"  );
					break;
				case "endpoint":
					return array( Flux::dest_endpoint, "id"  );
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
			if ( isset($values['source_endpoint']) ) {
				$local_source_endpoint = $values['source_endpoint'];
				if ( $local_source_endpoint instanceof EndpointDBO) {
					$values[Flux::src_endpoint] = $local_source_endpoint->id;
				}
				else if ( is_integer( $local_source_endpoint) ) {
					$params[Flux::src_endpoint] = $local_source_endpoint;
				}
			}
			if ( isset($values['destination_endpoint']) ) {
				$local_destination_endpoint = $values['destination_endpoint'];
				if ( $local_destination_endpoint instanceof EndpointDBO) {
					$values[Flux::dest_endpoint] = $local_destination_endpoint->id;
				}
				else if ( is_integer( $local_destination_endpoint) ) {
					$params[Flux::dest_endpoint] = $local_destination_endpoint;
				}
			}
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Flux ) {
			if ( isset($values['source_endpoint']) ) {
				$local_source_endpoint = $values['source_endpoint'];
				if ( $local_source_endpoint instanceof EndpointDBO) {
					$values[Flux::src_endpoint] = $local_source_endpoint->id;
				}
				else if ( is_integer( $local_source_endpoint) ) {
					$params[Flux::src_endpoint] = $values['source_endpoint'];
				}
			}
			if ( isset($values['destination_endpoint']) ) {
				$local_destination_endpoint = $values['destination_endpoint'];
				if ( $local_destination_endpoint instanceof EndpointDBO) {
					$values[Flux::dest_endpoint] = $local_destination_endpoint->id;
				}
				else if ( is_integer( $local_destination_endpoint) ) {
					$params[Flux::dest_endpoint] = $values['destination_endpoint'];
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
		if ( $object instanceof FluxDBO )
		{
			// does not own source_endpoint Endpoint
			// does not own destination_endpoint Endpoint
			return parent::deleteObject($object);
		}

		return false;
	}

	public function deleteAllForSource_endpoint(EndpointDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForSource_endpoint($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForSource_endpoint($obj);
			}
		}
		return $success;
	}
	public function deleteAllForDestination_endpoint(EndpointDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForDestination_endpoint($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForDestination_endpoint($obj);
			}
		}
		return $success;
	}

	/**
	 *	Named fetches
	 */
	public function objectForSourceEndpointGUID( $src_endpoint, $src_guid )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		$qualifiers[] = Qualifier::FK( 'src_endpoint', $src_endpoint);
		$qualifiers[] = Qualifier::Equals( 'src_guid', $src_guid);

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
				throw new \Exception( "objectForSourceEndpointGUID expected 1 result, but fetched " . count($result) );
			}
		}

		return false;
	}

	public function objectForDestinationEndpointGUID( $dest_endpoint, $dest_guid )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		$qualifiers[] = Qualifier::FK( 'dest_endpoint', $dest_endpoint);
		$qualifiers[] = Qualifier::Equals( 'dest_guid', $dest_guid);

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
				throw new \Exception( "objectForDestinationEndpointGUID expected 1 result, but fetched " . count($result) );
			}
		}

		return false;
	}



	/** Validation */
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
				Flux::created,
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
				Flux::name,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_flux_hash($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_flux_error($object = null, $value)
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
				Flux::flux_error,
				"FILTER_VALIDATE_BOOLEAN"
			);
		}
		return null;
	}
	function validate_src_endpoint($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Flux::src_endpoint,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
	function validate_src_guid($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_src_url($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// url format
		if ( filter_var($value, FILTER_VALIDATE_URL) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Flux::src_url,
				"FILTER_VALIDATE_URL"
			);
		}
		return null;
	}
	function validate_src_status($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_src_pub_date($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_dest_endpoint($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Flux::dest_endpoint,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
	function validate_dest_guid($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_dest_status($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_dest_submission($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
}

?>
