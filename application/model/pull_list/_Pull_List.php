<?php

namespace model\pull_list;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\pull_list\Pull_ListDBO as Pull_ListDBO;

/* import related objects */
use \model\network\Endpoint as Endpoint;
use \model\network\EndpointDBO as EndpointDBO;
use \model\pull_list\Pull_List_Item as Pull_List_Item;
use \model\pull_list\Pull_List_ItemDBO as Pull_List_ItemDBO;

/** Sample Creation script */
		/** PULL_LIST */
/*
		$sql = "CREATE TABLE IF NOT EXISTS pull_list ( "
			. Pull_List::id . " INTEGER PRIMARY KEY, "
			. Pull_List::name . " TEXT, "
			. Pull_List::etag . " TEXT, "
			. Pull_List::created . " INTEGER, "
			. Pull_List::published . " INTEGER, "
			. Pull_List::endpoint_id . " INTEGER, "
			. "FOREIGN KEY (". Pull_List::endpoint_id .") REFERENCES " . Endpoint::TABLE . "(" . Endpoint::id . ")"
		. ")";
		$this->sqlite_execute( "pull_list", $sql, "Create table pull_list" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS pull_list_etag on pull_list (etag)';
		$this->sqlite_execute( "pull_list", $sql, "Index on pull_list (etag)" );
*/
abstract class _Pull_List extends Model
{
	const TABLE = 'pull_list';

	// attribute keys
	const id = 'id';
	const name = 'name';
	const etag = 'etag';
	const created = 'created';
	const published = 'published';
	const endpoint_id = 'endpoint_id';

	// relationship keys
	const endpoint = 'endpoint';
	const pull_list_items = 'pull_list_items';

	public function tableName() { return Pull_List::TABLE; }
	public function tablePK() { return Pull_List::id; }

	public function sortOrder()
	{
		return array(
			array( 'desc' => Pull_List::published),
			array( 'asc' => Pull_List::name)
		);
	}

	public function allColumnNames()
	{
		return array(
			Pull_List::id,
			Pull_List::name,
			Pull_List::etag,
			Pull_List::created,
			Pull_List::published,
			Pull_List::endpoint_id
		);
	}

	public function allAttributes()
	{
		return array(
			Pull_List::name,
			Pull_List::etag,
			Pull_List::created,
			Pull_List::published,
		);
	}

	public function allForeignKeys()
	{
		return array(Pull_List::endpoint_id);
	}

	public function allRelationshipNames()
	{
		return array(
			Pull_List::endpoint,
			Pull_List::pull_list_items
		);
	}

	/**
	 *	Simple fetches
	 */

	public function allForName($value)
	{
		return $this->allObjectsForKeyValue(Pull_List::name, $value);
	}

	public function allLikeName($value)
	{
		return SQL::Select( $this )
			->where( Qualifier::Like( Pull_List::name, $value, SQL::SQL_LIKE_AFTER ))
			->orderBy( $this->sortOrder() )
			->limit( 50 )
			->fetchAll();
	}

	public function objectForEtag($value)
	{
		return $this->singleObjectForKeyValue(Pull_List::etag, $value);
	}






	/**
	 * Simple relationship fetches
	 */
	public function allForEndpoint($obj)
	{
		return $this->allObjectsForFK(Pull_List::endpoint_id, $obj, $this->sortOrder(), 50);
	}

	public function countForEndpoint($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Pull_List::endpoint_id, $obj );
		}
		return false;
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "endpoint":
					return array( Pull_List::endpoint_id, "id"  );
					break;
				case "pull_list_item":
					return array( Pull_List::id, "pull_list_id"  );
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
				$default_name = $this->attributeDefaultValue( null, null, Pull_List::name);
				if ( is_null( $default_name ) == false ) {
					$values['name'] = $default_name;
				}
			}
			if ( isset($values['etag']) == false ) {
				$default_etag = $this->attributeDefaultValue( null, null, Pull_List::etag);
				if ( is_null( $default_etag ) == false ) {
					$values['etag'] = $default_etag;
				}
			}
			if ( isset($values['created']) == false ) {
				$default_created = $this->attributeDefaultValue( null, null, Pull_List::created);
				if ( is_null( $default_created ) == false ) {
					$values['created'] = $default_created;
				}
			}
			if ( isset($values['published']) == false ) {
				$default_published = $this->attributeDefaultValue( null, null, Pull_List::published);
				if ( is_null( $default_published ) == false ) {
					$values['published'] = $default_published;
				}
			}

			// default conversion for relationships
			if ( isset($values['endpoint']) ) {
				$local_endpoint = $values['endpoint'];
				if ( $local_endpoint instanceof EndpointDBO) {
					$values[Pull_List::endpoint_id] = $local_endpoint->id;
				}
				else if ( is_integer( $local_endpoint) ) {
					$params[Pull_List::endpoint_id] = $local_endpoint;
				}
			}
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Pull_List ) {
			if ( isset($values['endpoint']) ) {
				$local_endpoint = $values['endpoint'];
				if ( $local_endpoint instanceof EndpointDBO) {
					$values[Pull_List::endpoint_id] = $local_endpoint->id;
				}
				else if ( is_integer( $local_endpoint) ) {
					$params[Pull_List::endpoint_id] = $values['endpoint'];
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
		if ( $object instanceof Pull_ListDBO )
		{
			// does not own endpoint Endpoint
			$pull_list_item_model = Model::Named('Pull_List_Item');
			if ( $pull_list_item_model->deleteAllForKeyValue(Pull_List_Item::pull_list_id, $object->id) == false ) {
				return false;
			}
			return parent::deleteObject($object);
		}

		return false;
	}

	public function deleteAllForEndpoint(EndpointDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForEndpoint($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForEndpoint($obj);
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
				Pull_List::name
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesMap() {
		return array(
			Pull_List::name => Model::TEXT_TYPE,
			Pull_List::etag => Model::TEXT_TYPE,
			Pull_List::created => Model::DATE_TYPE,
			Pull_List::published => Model::DATE_TYPE,
			Pull_List::endpoint_id => Model::TO_ONE_TYPE
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
				case Pull_List::endpoint_id:
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
	function validate_name($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List::name,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_etag($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// make sure Etag is unique
		$existing = $this->objectForEtag($value);
		if ( $existing != false && ( is_null($object) || $existing->id != $object->id)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List::etag,
				"UNIQUE_FIELD_VALUE"
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
				Pull_List::created,
				"IMMUTABLE"
			);
		}
		return null;
	}
	function validate_published($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_endpoint_id($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List::endpoint_id,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
}

?>
