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
use \model\Endpoint as Endpoint;
use \model\EndpointDBO as EndpointDBO;
use \model\pull_list\Pull_List_Item as Pull_List_Item;
use \model\pull_list\Pull_List_ItemDBO as Pull_List_ItemDBO;
use \model\pull_list\Pull_List_Exclusion as Pull_List_Exclusion;
use \model\pull_list\Pull_List_ExclusionDBO as Pull_List_ExclusionDBO;
use \model\pull_list\Pull_List_Expansion as Pull_List_Expansion;
use \model\pull_list\Pull_List_ExpansionDBO as Pull_List_ExpansionDBO;

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
	const id = 'id';
	const name = 'name';
	const etag = 'etag';
	const created = 'created';
	const published = 'published';
	const endpoint_id = 'endpoint_id';

	public function tableName() { return Pull_List::TABLE; }
	public function tablePK() { return Pull_List::id; }

	public function sortOrder()
	{
		return array(
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


	public function allForEndpoint($obj)
	{
		return $this->allObjectsForFK(Pull_List::endpoint_id, $obj, $this->sortOrder(), 50);
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
				case "pull_list_exclusion":
					return array( Pull_List::endpoint_id, "endpoint_id"  );
					break;
				case "pull_list_expansion":
					return array( Pull_List::endpoint_id, "endpoint_id"  );
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
					$values[Pull_List::endpoint_id] = $local_endpoint->id;
				}
				else if ( is_integer( $local_endpoint) ) {
					$params[Pull_List::endpoint_id] = $local_endpoint;
				}
			}
			if ( isset($values['exclusions']) ) {
				$local_exclusions = $values['exclusions'];
				if ( $local_exclusions instanceof Pull_List_ExclusionDBO) {
					$values[Pull_List::endpoint_id] = $local_exclusions->endpoint_id;
				}
				else if ( is_integer( $local_exclusions) ) {
					$params[Pull_List::endpoint_id] = $local_exclusions;
				}
			}
			if ( isset($values['expansions']) ) {
				$local_expansions = $values['expansions'];
				if ( $local_expansions instanceof Pull_List_ExpansionDBO) {
					$values[Pull_List::endpoint_id] = $local_expansions->endpoint_id;
				}
				else if ( is_integer( $local_expansions) ) {
					$params[Pull_List::endpoint_id] = $local_expansions;
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
			if ( isset($values['exclusions']) ) {
				$local_exclusions = $values['exclusions'];
				if ( $local_exclusions instanceof Pull_List_ExclusionDBO) {
					$values[Pull_List::endpoint_id] = $local_exclusions->endpoint_id;
				}
				else if ( is_integer( $local_exclusions) ) {
					$params[Pull_List::endpoint_id] = $values['exclusions'];
				}
			}
			if ( isset($values['expansions']) ) {
				$local_expansions = $values['expansions'];
				if ( $local_expansions instanceof Pull_List_ExpansionDBO) {
					$values[Pull_List::endpoint_id] = $local_expansions->endpoint_id;
				}
				else if ( is_integer( $local_expansions) ) {
					$params[Pull_List::endpoint_id] = $values['expansions'];
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
			// does not own Endpoint
			$pull_list_item_model = Model::Named('Pull_List_Item');
			if ( $pull_list_item_model->deleteAllForKeyValue(Pull_List_Item::pull_list_id, $this->id) == false ) {
				return false;
			}
			// does not own Pull_List_Exclusion
			// does not own Pull_List_Expansion
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
	 *	Named fetches
	 */

	/** Set attributes */
	public function setName( Pull_ListDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Pull_List::name => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setEtag( Pull_ListDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Pull_List::etag => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setCreated( Pull_ListDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Pull_List::created => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setPublished( Pull_ListDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Pull_List::published => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}

	public function setEndpoint_id( Pull_ListDBO $object = null, $value = null)
	{
		if ( is_null($object) === false ) {
			if ($this->updateObject( $object, array(Pull_List::endpoint_id => $value)) ) {
				return $this->refreshObject($userObj);
			}
		}
		return false;
	}


	/** Validation */
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
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Pull_List::endpoint_id,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
}

?>
