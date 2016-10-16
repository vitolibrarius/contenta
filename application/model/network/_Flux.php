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
use \model\network\Endpoint as Endpoint;
use \model\network\EndpointDBO as EndpointDBO;

/** Generated class, do not edit.
 */
abstract class _Flux extends Model
{
	const TABLE = 'flux';

	// attribute keys
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

	// relationship keys
	const source_endpoint = 'source_endpoint';
	const destination_endpoint = 'destination_endpoint';

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

	public function allAttributes()
	{
		return array(
			Flux::created,
			Flux::name,
			Flux::flux_hash,
			Flux::flux_error,
			Flux::src_guid,
			Flux::src_url,
			Flux::src_status,
			Flux::src_pub_date,
			Flux::dest_guid,
			Flux::dest_status,
			Flux::dest_submission
		);
	}

	public function allForeignKeys()
	{
		return array(Flux::src_endpoint,
			Flux::dest_endpoint);
	}

	public function allRelationshipNames()
	{
		return array(
			Flux::source_endpoint,
			Flux::destination_endpoint
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




	public function objectForSrc_guid($value)
	{
		return $this->singleObjectForKeyValue(Flux::src_guid, $value);
	}


	public function allForSrc_url($value)
	{
		return $this->allObjectsForKeyValue(Flux::src_url, $value);
	}


	public function allForSrc_status($value)
	{
		return $this->allObjectsForKeyValue(Flux::src_status, $value);
	}




	public function objectForDest_guid($value)
	{
		return $this->singleObjectForKeyValue(Flux::dest_guid, $value);
	}


	public function allForDest_status($value)
	{
		return $this->allObjectsForKeyValue(Flux::dest_status, $value);
	}




	/**
	 * Simple relationship fetches
	 */
	public function allForSource_endpoint($obj)
	{
		return $this->allObjectsForFK(Flux::src_endpoint, $obj, $this->sortOrder(), 50);
	}

	public function countForSource_endpoint($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Flux::src_endpoint, $obj );
		}
		return false;
	}
	public function allForDestination_endpoint($obj)
	{
		return $this->allObjectsForFK(Flux::dest_endpoint, $obj, $this->sortOrder(), 50);
	}

	public function countForDestination_endpoint($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Flux::dest_endpoint, $obj );
		}
		return false;
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

			// default values for attributes
			if ( isset($values['created']) == false ) {
				$default_created = $this->attributeDefaultValue( null, null, Flux::created);
				if ( is_null( $default_created ) == false ) {
					$values['created'] = $default_created;
				}
			}
			if ( isset($values['name']) == false ) {
				$default_name = $this->attributeDefaultValue( null, null, Flux::name);
				if ( is_null( $default_name ) == false ) {
					$values['name'] = $default_name;
				}
			}
			if ( isset($values['flux_hash']) == false ) {
				$default_flux_hash = $this->attributeDefaultValue( null, null, Flux::flux_hash);
				if ( is_null( $default_flux_hash ) == false ) {
					$values['flux_hash'] = $default_flux_hash;
				}
			}
			if ( isset($values['flux_error']) == false ) {
				$default_flux_error = $this->attributeDefaultValue( null, null, Flux::flux_error);
				if ( is_null( $default_flux_error ) == false ) {
					$values['flux_error'] = $default_flux_error;
				}
			}
			if ( isset($values['src_guid']) == false ) {
				$default_src_guid = $this->attributeDefaultValue( null, null, Flux::src_guid);
				if ( is_null( $default_src_guid ) == false ) {
					$values['src_guid'] = $default_src_guid;
				}
			}
			if ( isset($values['src_url']) == false ) {
				$default_src_url = $this->attributeDefaultValue( null, null, Flux::src_url);
				if ( is_null( $default_src_url ) == false ) {
					$values['src_url'] = $default_src_url;
				}
			}
			if ( isset($values['src_status']) == false ) {
				$default_src_status = $this->attributeDefaultValue( null, null, Flux::src_status);
				if ( is_null( $default_src_status ) == false ) {
					$values['src_status'] = $default_src_status;
				}
			}
			if ( isset($values['src_pub_date']) == false ) {
				$default_src_pub_date = $this->attributeDefaultValue( null, null, Flux::src_pub_date);
				if ( is_null( $default_src_pub_date ) == false ) {
					$values['src_pub_date'] = $default_src_pub_date;
				}
			}
			if ( isset($values['dest_guid']) == false ) {
				$default_dest_guid = $this->attributeDefaultValue( null, null, Flux::dest_guid);
				if ( is_null( $default_dest_guid ) == false ) {
					$values['dest_guid'] = $default_dest_guid;
				}
			}
			if ( isset($values['dest_status']) == false ) {
				$default_dest_status = $this->attributeDefaultValue( null, null, Flux::dest_status);
				if ( is_null( $default_dest_status ) == false ) {
					$values['dest_status'] = $default_dest_status;
				}
			}
			if ( isset($values['dest_submission']) == false ) {
				$default_dest_submission = $this->attributeDefaultValue( null, null, Flux::dest_submission);
				if ( is_null( $default_dest_submission ) == false ) {
					$values['dest_submission'] = $default_dest_submission;
				}
			}

			// default conversion for relationships
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
	 * Named fetches
	 */

	/**
	 * Attribute editing
	 */
	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Flux::name
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesMap() {
		return array(
			Flux::created => Model::DATE_TYPE,
			Flux::name => Model::TEXT_TYPE,
			Flux::flux_hash => Model::TEXT_TYPE,
			Flux::flux_error => Model::FLAG_TYPE,
			Flux::src_endpoint => Model::TO_ONE_TYPE,
			Flux::src_guid => Model::TEXT_TYPE,
			Flux::src_url => Model::TEXTAREA_TYPE,
			Flux::src_status => Model::TEXT_TYPE,
			Flux::src_pub_date => Model::DATE_TYPE,
			Flux::dest_endpoint => Model::TO_ONE_TYPE,
			Flux::dest_guid => Model::TEXT_TYPE,
			Flux::dest_status => Model::TEXT_TYPE,
			Flux::dest_submission => Model::DATE_TYPE
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
				case Flux::src_endpoint:
					$endpoint_model = Model::Named('Endpoint');
					$fkObject = $endpoint_model->objectForId( $value );
					break;
				case Flux::dest_endpoint:
					$endpoint_model = Model::Named('Endpoint');
					$fkObject = $endpoint_model->objectForId( $value );
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

		return null;
	}
	function validate_src_guid($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// make sure Src_guid is unique
		$existing = $this->objectForSrc_guid($value);
		if ( $existing != false && ( is_null($object) || $existing->id != $object->id)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Flux::src_guid,
				"UNIQUE_FIELD_VALUE"
			);
		}
		return null;
	}
	function validate_src_url($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
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

		return null;
	}
	function validate_dest_guid($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// make sure Dest_guid is unique
		$existing = $this->objectForDest_guid($value);
		if ( $existing != false && ( is_null($object) || $existing->id != $object->id)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Flux::dest_guid,
				"UNIQUE_FIELD_VALUE"
			);
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
