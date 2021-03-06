<?php

namespace model\media;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\media\Series_AliasDBO as Series_AliasDBO;

/* import related objects */
use \model\media\Series as Series;
use \model\media\SeriesDBO as SeriesDBO;

/** Generated class, do not edit.
 */
abstract class _Series_Alias extends Model
{
	const TABLE = 'series_alias';

	// attribute keys
	const id = 'id';
	const name = 'name';
	const series_id = 'series_id';

	// relationship keys
	const series = 'series';

	public function modelName()
	{
		return "Series_Alias";
	}

	public function dboName()
	{
		return '\model\media\Series_AliasDBO';
	}

	public function tableName() { return Series_Alias::TABLE; }
	public function tablePK() { return Series_Alias::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Series_Alias::name)
		);
	}

	public function allColumnNames()
	{
		return array(
			Series_Alias::id,
			Series_Alias::name,
			Series_Alias::series_id
		);
	}

	public function allAttributes()
	{
		return array(
			Series_Alias::name,
		);
	}

	public function allForeignKeys()
	{
		return array(Series_Alias::series_id);
	}

	public function allRelationshipNames()
	{
		return array(
			Series_Alias::series
		);
	}

	public function attributes()
	{
		return array(
			Series_Alias::name => array('length' => 256,'type' => 'TEXT'),
		);
	}

	public function relationships()
	{
		return array(
			Series_Alias::series => array(
				'destination' => 'Series',
				'ownsDestination' => false,
				'isMandatory' => true,
				'isToMany' => false,
				'joins' => array( 'series_id' => 'id')
			)
		);
	}

	public function searchQualifiers( array $query )
	{
		$qualifiers = array();
		if ( is_array($query) ) {
			foreach( $query as $attr => $value ) {
				switch ($attr) {
			// Series_Alias::id == INTEGER

			// Series_Alias::name == TEXT
				case Series_Alias::name:
					if (strlen($value) > 0) {
						$qualifiers[Series_Alias::name] = Qualifier::Equals( Series_Alias::name, $value );
					}
					break;

			// Series_Alias::series_id == INTEGER
				case Series_Alias::series_id:
					if ( intval($value) > 0 ) {
						$qualifiers[Series_Alias::series_id] = Qualifier::Equals( Series_Alias::series_id, intval($value) );
					}
					break;

				default:
					/* no type specified for Series_Alias::series_id */
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
		return $this->allObjectsForKeyValue(Series_Alias::name, $value, null, $limit);
	}




	/**
	 * Simple relationship fetches
	 */
	public function allForSeries($obj, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForFK(Series_Alias::series_id, $obj, $this->sortOrder(), $limit);
	}

	public function countForSeries($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Series_Alias::series_id, $obj );
		}
		return false;
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "series":
					return array( Series_Alias::series_id, "id"  );
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
				$default_name = $this->attributeDefaultValue( null, null, Series_Alias::name);
				if ( is_null( $default_name ) == false ) {
					$values['name'] = $default_name;
				}
			}

			// default conversion for relationships
			if ( isset($values['series']) ) {
				$local_series = $values['series'];
				if ( $local_series instanceof SeriesDBO) {
					$values[Series_Alias::series_id] = $local_series->id;
				}
				else if ( is_integer( $local_series) ) {
					$params[Series_Alias::series_id] = $local_series;
				}
			}
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Series_Alias ) {
			if ( isset($values['series']) ) {
				$local_series = $values['series'];
				if ( $local_series instanceof SeriesDBO) {
					$values[Series_Alias::series_id] = $local_series->id;
				}
				else if ( is_integer( $local_series) ) {
					$params[Series_Alias::series_id] = $values['series'];
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
		if ( $object instanceof Series_AliasDBO )
		{
			// does not own series Series
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
	public function objectForSeriesAndAlias(SeriesDBO $series, $name )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		$qualifiers[] = Qualifier::FK( 'series_id', $series);
		$qualifiers[] = Qualifier::Equals( 'name', $name);

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
				throw new \Exception( "objectForSeriesAndAlias expected 1 result, but fetched " . count($result) );
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
				Series_Alias::name
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesMap() {
		return array(
			Series_Alias::name => Model::TEXT_TYPE,
			Series_Alias::series_id => Model::TO_ONE_TYPE
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
				case Series_Alias::series_id:
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
	function validate_name($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Series_Alias::name,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_series_id($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Series_Alias::series_id,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
}

?>
