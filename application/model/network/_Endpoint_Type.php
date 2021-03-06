<?php

namespace model\network;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\network\Endpoint_TypeDBO as Endpoint_TypeDBO;

/* import related objects */
use \model\network\Endpoint as Endpoint;
use \model\network\EndpointDBO as EndpointDBO;
use \model\pull_list\Pull_List_Excl as Pull_List_Excl;
use \model\pull_list\Pull_List_ExclDBO as Pull_List_ExclDBO;
use \model\pull_list\Pull_List_Expansion as Pull_List_Expansion;
use \model\pull_list\Pull_List_ExpansionDBO as Pull_List_ExpansionDBO;

/** Generated class, do not edit.
 */
abstract class _Endpoint_Type extends Model
{
	const TABLE = 'endpoint_type';

	// attribute keys
	const code = 'code';
	const name = 'name';
	const comments = 'comments';
	const data_type = 'data_type';
	const site_url = 'site_url';
	const api_url = 'api_url';
	const favicon_url = 'favicon_url';
	const throttle_hits = 'throttle_hits';
	const throttle_time = 'throttle_time';

	// relationship keys
	const endpoints = 'endpoints';
	const pull_list_excls = 'pull_list_excls';
	const pull_list_expansions = 'pull_list_expansions';

	public function modelName()
	{
		return "Endpoint_Type";
	}

	public function dboName()
	{
		return '\model\network\Endpoint_TypeDBO';
	}

	public function tableName() { return Endpoint_Type::TABLE; }
	public function tablePK() { return Endpoint_Type::code; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Endpoint_Type::name)
		);
	}

	public function allColumnNames()
	{
		return array(
			Endpoint_Type::code,
			Endpoint_Type::name,
			Endpoint_Type::comments,
			Endpoint_Type::data_type,
			Endpoint_Type::site_url,
			Endpoint_Type::api_url,
			Endpoint_Type::favicon_url,
			Endpoint_Type::throttle_hits,
			Endpoint_Type::throttle_time
		);
	}

	public function allAttributes()
	{
		return array(
			Endpoint_Type::name,
			Endpoint_Type::comments,
			Endpoint_Type::data_type,
			Endpoint_Type::site_url,
			Endpoint_Type::api_url,
			Endpoint_Type::favicon_url,
			Endpoint_Type::throttle_hits,
			Endpoint_Type::throttle_time
		);
	}

	public function allForeignKeys()
	{
		return array();
	}

	public function allRelationshipNames()
	{
		return array(
			Endpoint_Type::endpoints,
			Endpoint_Type::pull_list_excls,
			Endpoint_Type::pull_list_expansions
		);
	}

	public function attributes()
	{
		return array(
			Endpoint_Type::name => array('length' => 256,'type' => 'TEXT'),
			Endpoint_Type::comments => array('length' => 4096,'type' => 'TEXT'),
			Endpoint_Type::data_type => array('length' => 256,'type' => 'TEXT'),
			Endpoint_Type::site_url => array('length' => 1024,'type' => 'TEXT'),
			Endpoint_Type::api_url => array('length' => 1024,'type' => 'TEXT'),
			Endpoint_Type::favicon_url => array('length' => 1024,'type' => 'TEXT'),
			Endpoint_Type::throttle_hits => array('type' => 'INTEGER'),
			Endpoint_Type::throttle_time => array('type' => 'INTEGER')
		);
	}

	public function relationships()
	{
		return array(
			Endpoint_Type::endpoints => array(
				'destination' => 'Endpoint',
				'ownsDestination' => true,
				'isMandatory' => false,
				'isToMany' => true,
				'joins' => array( 'code' => 'type_code')
			),
			Endpoint_Type::pull_list_excls => array(
				'destination' => 'Pull_List_Excl',
				'ownsDestination' => true,
				'isMandatory' => false,
				'isToMany' => true,
				'joins' => array( 'code' => 'endpoint_type_code')
			),
			Endpoint_Type::pull_list_expansions => array(
				'destination' => 'Pull_List_Expansion',
				'ownsDestination' => true,
				'isMandatory' => false,
				'isToMany' => true,
				'joins' => array( 'code' => 'endpoint_type_code')
			)
		);
	}

	public function searchQualifiers( array $query )
	{
		$qualifiers = array();
		if ( is_array($query) ) {
			foreach( $query as $attr => $value ) {
				switch ($attr) {
			// Endpoint_Type::code == TEXT
				case Endpoint_Type::code:
					if (strlen($value) > 0) {
						$qualifiers[Endpoint_Type::code] = Qualifier::Equals( Endpoint_Type::code, $value );
					}
					break;

			// Endpoint_Type::name == TEXT
				case Endpoint_Type::name:
					if (strlen($value) > 0) {
						$qualifiers[Endpoint_Type::name] = Qualifier::Equals( Endpoint_Type::name, $value );
					}
					break;

			// Endpoint_Type::comments == TEXT
				case Endpoint_Type::comments:
					if (strlen($value) > 0) {
						$qualifiers[Endpoint_Type::comments] = Qualifier::Equals( Endpoint_Type::comments, $value );
					}
					break;

			// Endpoint_Type::data_type == TEXT
				case Endpoint_Type::data_type:
					if (strlen($value) > 0) {
						$qualifiers[Endpoint_Type::data_type] = Qualifier::Equals( Endpoint_Type::data_type, $value );
					}
					break;

			// Endpoint_Type::site_url == TEXT
				case Endpoint_Type::site_url:
					if (strlen($value) > 0) {
						$qualifiers[Endpoint_Type::site_url] = Qualifier::Equals( Endpoint_Type::site_url, $value );
					}
					break;

			// Endpoint_Type::api_url == TEXT
				case Endpoint_Type::api_url:
					if (strlen($value) > 0) {
						$qualifiers[Endpoint_Type::api_url] = Qualifier::Equals( Endpoint_Type::api_url, $value );
					}
					break;

			// Endpoint_Type::favicon_url == TEXT
				case Endpoint_Type::favicon_url:
					if (strlen($value) > 0) {
						$qualifiers[Endpoint_Type::favicon_url] = Qualifier::Equals( Endpoint_Type::favicon_url, $value );
					}
					break;

			// Endpoint_Type::throttle_hits == INTEGER
				case Endpoint_Type::throttle_hits:
					if ( intval($value) > 0 ) {
						$qualifiers[Endpoint_Type::throttle_hits] = Qualifier::Equals( Endpoint_Type::throttle_hits, intval($value) );
					}
					break;

			// Endpoint_Type::throttle_time == INTEGER
				case Endpoint_Type::throttle_time:
					if ( intval($value) > 0 ) {
						$qualifiers[Endpoint_Type::throttle_time] = Qualifier::Equals( Endpoint_Type::throttle_time, intval($value) );
					}
					break;

				default:
					/* no type specified for Endpoint_Type::throttle_time */
					break;
				}
			}
		}
		return $qualifiers;
	}

	/**
	 *	Simple fetches
	 */
	public function objectForCode($value)
	{
		return $this->singleObjectForKeyValue(Endpoint_Type::code, $value);
	}


	public function objectForName($value)
	{
		return $this->singleObjectForKeyValue(Endpoint_Type::name, $value);
	}


	public function allForComments($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Endpoint_Type::comments, $value, null, $limit);
	}


	public function allForData_type($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Endpoint_Type::data_type, $value, null, $limit);
	}


	public function allForSite_url($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Endpoint_Type::site_url, $value, null, $limit);
	}


	public function allForApi_url($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Endpoint_Type::api_url, $value, null, $limit);
	}


	public function allForFavicon_url($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Endpoint_Type::favicon_url, $value, null, $limit);
	}


	public function allForThrottle_hits($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Endpoint_Type::throttle_hits, $value, null, $limit);
	}

	public function allForThrottle_time($value, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForKeyValue(Endpoint_Type::throttle_time, $value, null, $limit);
	}


	/**
	 * Simple relationship fetches
	 */

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "endpoint":
					return array( Endpoint_Type::code, "type_code"  );
					break;
				case "pull_list_excl":
					return array( Endpoint_Type::code, "endpoint_type_code"  );
					break;
				case "pull_list_expansion":
					return array( Endpoint_Type::code, "endpoint_type_code"  );
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
				$default_name = $this->attributeDefaultValue( null, null, Endpoint_Type::name);
				if ( is_null( $default_name ) == false ) {
					$values['name'] = $default_name;
				}
			}
			if ( isset($values['comments']) == false ) {
				$default_comments = $this->attributeDefaultValue( null, null, Endpoint_Type::comments);
				if ( is_null( $default_comments ) == false ) {
					$values['comments'] = $default_comments;
				}
			}
			if ( isset($values['data_type']) == false ) {
				$default_data_type = $this->attributeDefaultValue( null, null, Endpoint_Type::data_type);
				if ( is_null( $default_data_type ) == false ) {
					$values['data_type'] = $default_data_type;
				}
			}
			if ( isset($values['site_url']) == false ) {
				$default_site_url = $this->attributeDefaultValue( null, null, Endpoint_Type::site_url);
				if ( is_null( $default_site_url ) == false ) {
					$values['site_url'] = $default_site_url;
				}
			}
			if ( isset($values['api_url']) == false ) {
				$default_api_url = $this->attributeDefaultValue( null, null, Endpoint_Type::api_url);
				if ( is_null( $default_api_url ) == false ) {
					$values['api_url'] = $default_api_url;
				}
			}
			if ( isset($values['favicon_url']) == false ) {
				$default_favicon_url = $this->attributeDefaultValue( null, null, Endpoint_Type::favicon_url);
				if ( is_null( $default_favicon_url ) == false ) {
					$values['favicon_url'] = $default_favicon_url;
				}
			}
			if ( isset($values['throttle_hits']) == false ) {
				$default_throttle_hits = $this->attributeDefaultValue( null, null, Endpoint_Type::throttle_hits);
				if ( is_null( $default_throttle_hits ) == false ) {
					$values['throttle_hits'] = $default_throttle_hits;
				}
			}
			if ( isset($values['throttle_time']) == false ) {
				$default_throttle_time = $this->attributeDefaultValue( null, null, Endpoint_Type::throttle_time);
				if ( is_null( $default_throttle_time ) == false ) {
					$values['throttle_time'] = $default_throttle_time;
				}
			}

			// default conversion for relationships
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Endpoint_Type ) {
		}

		return parent::updateObject($object, $values);
	}

	/**
	 *	Delete functions
	 */
	public function deleteObject( DataObject $object = null)
	{
		if ( $object instanceof Endpoint_TypeDBO )
		{
			$endpoint_model = Model::Named('Endpoint');
			if ( $endpoint_model->deleteAllForKeyValue(Endpoint::type_code, $object->code) == false ) {
				return false;
			}
			$pull_list_excl_model = Model::Named('Pull_List_Excl');
			if ( $pull_list_excl_model->deleteAllForKeyValue(Pull_List_Excl::endpoint_type_code, $object->code) == false ) {
				return false;
			}
			$pull_list_expansion_model = Model::Named('Pull_List_Expansion');
			if ( $pull_list_expansion_model->deleteAllForKeyValue(Pull_List_Expansion::endpoint_type_code, $object->code) == false ) {
				return false;
			}
			return parent::deleteObject($object);
		}

		return false;
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
				Endpoint_Type::name,
				Endpoint_Type::site_url,
				Endpoint_Type::api_url
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesMap() {
		return array(
			Endpoint_Type::name => Model::TEXT_TYPE,
			Endpoint_Type::comments => Model::TEXTAREA_TYPE,
			Endpoint_Type::data_type => Model::TEXT_TYPE,
			Endpoint_Type::site_url => Model::TEXTAREA_TYPE,
			Endpoint_Type::api_url => Model::TEXTAREA_TYPE,
			Endpoint_Type::favicon_url => Model::TEXTAREA_TYPE,
			Endpoint_Type::throttle_hits => Model::INT_TYPE,
			Endpoint_Type::throttle_time => Model::INT_TYPE
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
				Endpoint_Type::name,
				"FIELD_EMPTY"
			);
		}

		// make sure Name is unique
		$existing = $this->objectForName($value);
		if ( $existing != false && ( is_null($object) || $existing->id != $object->id)) {
			return Localized::ModelValidation(
				$this->tableName(),
				Endpoint_Type::name,
				"UNIQUE_FIELD_VALUE"
			);
		}
		return null;
	}
	function validate_comments($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_data_type($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_site_url($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Endpoint_Type::site_url,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_api_url($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Endpoint_Type::api_url,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_favicon_url($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_throttle_hits($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Endpoint_Type::throttle_hits,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
	function validate_throttle_time($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		// integers
		if (filter_var($value, FILTER_VALIDATE_INT) === false) {
			return Localized::ModelValidation(
				$this->tableName(),
				Endpoint_Type::throttle_time,
				"FILTER_VALIDATE_INT"
			);
		}
		return null;
	}
}

?>
