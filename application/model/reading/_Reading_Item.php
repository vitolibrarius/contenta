<?php

namespace model\reading;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\reading\Reading_ItemDBO as Reading_ItemDBO;

/* import related objects */
use \model\user\Users as Users;
use \model\user\UsersDBO as UsersDBO;
use \model\media\Publication as Publication;
use \model\media\PublicationDBO as PublicationDBO;

/** Generated class, do not edit.
 */
abstract class _Reading_Item extends Model
{
	const TABLE = 'reading_item';

	// attribute keys
	const id = 'id';
	const user_id = 'user_id';
	const publication_id = 'publication_id';
	const created = 'created';
	const read_date = 'read_date';
	const mislabeled = 'mislabeled';

	// relationship keys
	const user = 'user';
	const publication = 'publication';

	public function modelName()
	{
		return "Reading_Item";
	}

	public function dboName()
	{
		return '\model\reading\Reading_ItemDBO';
	}

	public function tableName() { return Reading_Item::TABLE; }
	public function tablePK() { return Reading_Item::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Reading_Item::read_date)
		);
	}

	public function allColumnNames()
	{
		return array(
			Reading_Item::id,
			Reading_Item::user_id,
			Reading_Item::publication_id,
			Reading_Item::created,
			Reading_Item::read_date,
			Reading_Item::mislabeled
		);
	}

	public function allAttributes()
	{
		return array(
			Reading_Item::created,
			Reading_Item::read_date,
			Reading_Item::mislabeled
		);
	}

	public function allForeignKeys()
	{
		return array(Reading_Item::user_id,
			Reading_Item::publication_id);
	}

	public function allRelationshipNames()
	{
		return array(
			Reading_Item::user,
			Reading_Item::publication
		);
	}

	public function attributes()
	{
		return array(
			Reading_Item::created => array('type' => 'DATE'),
			Reading_Item::read_date => array('type' => 'DATE'),
			Reading_Item::mislabeled => array('type' => 'BOOLEAN')
		);
	}

	public function relationships()
	{
		return array(
			Reading_Item::user => array(
				'destination' => 'Users',
				'ownsDestination' => false,
				'isMandatory' => true,
				'isToMany' => false,
				'joins' => array( 'user_id' => 'id')
			),
			Reading_Item::publication => array(
				'destination' => 'Publication',
				'ownsDestination' => false,
				'isMandatory' => true,
				'isToMany' => false,
				'joins' => array( 'publication_id' => 'id')
			)
		);
	}

	public function searchQualifiers( array $query )
	{
		$qualifiers = array();
		if ( is_array($query) ) {
			foreach( $query as $attr => $value ) {
				switch ($attr) {
			// Reading_Item::id == INTEGER

			// Reading_Item::user_id == INTEGER
				case Reading_Item::user_id:
					if ( intval($value) > 0 ) {
						$qualifiers[Reading_Item::user_id] = Qualifier::Equals( Reading_Item::user_id, intval($value) );
					}
					break;

			// Reading_Item::publication_id == INTEGER
				case Reading_Item::publication_id:
					if ( intval($value) > 0 ) {
						$qualifiers[Reading_Item::publication_id] = Qualifier::Equals( Reading_Item::publication_id, intval($value) );
					}
					break;

			// Reading_Item::created == DATE

			// Reading_Item::read_date == DATE

			// Reading_Item::mislabeled == BOOLEAN
				case Reading_Item::mislabeled:
					$v = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
					if (is_null($v) == false) {
						$qualifiers[Reading_Item::mislabeled] = Qualifier::Equals( Reading_Item::mislabeled, $v );
					}
					break;

				default:
					/* no type specified for Reading_Item::mislabeled */
					break;
				}
			}
		}
		return $qualifiers;
	}

	/**
	 *	Simple fetches
	 */







	/**
	 * Simple relationship fetches
	 */
	public function allForUser($obj, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForFK(Reading_Item::user_id, $obj, $this->sortOrder(), $limit);
	}

	public function countForUser($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Reading_Item::user_id, $obj );
		}
		return false;
	}
	public function allForPublication($obj, $limit = SQL::SQL_DEFAULT_LIMIT)
	{
		return $this->allObjectsForFK(Reading_Item::publication_id, $obj, $this->sortOrder(), $limit);
	}

	public function countForPublication($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Reading_Item::publication_id, $obj );
		}
		return false;
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "users":
					return array( Reading_Item::user_id, "id"  );
					break;
				case "publication":
					return array( Reading_Item::publication_id, "id"  );
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
				$default_created = $this->attributeDefaultValue( null, null, Reading_Item::created);
				if ( is_null( $default_created ) == false ) {
					$values['created'] = $default_created;
				}
			}
			if ( isset($values['read_date']) == false ) {
				$default_read_date = $this->attributeDefaultValue( null, null, Reading_Item::read_date);
				if ( is_null( $default_read_date ) == false ) {
					$values['read_date'] = $default_read_date;
				}
			}
			if ( isset($values['mislabeled']) == false ) {
				$default_mislabeled = $this->attributeDefaultValue( null, null, Reading_Item::mislabeled);
				if ( is_null( $default_mislabeled ) == false ) {
					$values['mislabeled'] = $default_mislabeled;
				}
			}

			// default conversion for relationships
			if ( isset($values['user']) ) {
				$local_user = $values['user'];
				if ( $local_user instanceof UsersDBO) {
					$values[Reading_Item::user_id] = $local_user->id;
				}
				else if ( is_integer( $local_user) ) {
					$params[Reading_Item::user_id] = $local_user;
				}
			}
			if ( isset($values['publication']) ) {
				$local_publication = $values['publication'];
				if ( $local_publication instanceof PublicationDBO) {
					$values[Reading_Item::publication_id] = $local_publication->id;
				}
				else if ( is_integer( $local_publication) ) {
					$params[Reading_Item::publication_id] = $local_publication;
				}
			}
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Reading_Item ) {
			if ( isset($values['user']) ) {
				$local_user = $values['user'];
				if ( $local_user instanceof UsersDBO) {
					$values[Reading_Item::user_id] = $local_user->id;
				}
				else if ( is_integer( $local_user) ) {
					$params[Reading_Item::user_id] = $values['user'];
				}
			}
			if ( isset($values['publication']) ) {
				$local_publication = $values['publication'];
				if ( $local_publication instanceof PublicationDBO) {
					$values[Reading_Item::publication_id] = $local_publication->id;
				}
				else if ( is_integer( $local_publication) ) {
					$params[Reading_Item::publication_id] = $values['publication'];
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
		if ( $object instanceof Reading_ItemDBO )
		{
			// does not own user Users
			// does not own publication Publication
			return parent::deleteObject($object);
		}

		return false;
	}

	public function deleteAllForUser(UsersDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForUser($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForUser($obj);
			}
		}
		return $success;
	}
	public function deleteAllForPublication(PublicationDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForPublication($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForPublication($obj);
			}
		}
		return $success;
	}

	/**
	 * Named fetches
	 */
	public function objectForUserAndPublication(UsersDBO $user,PublicationDBO $publication )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		$qualifiers[] = Qualifier::FK( 'user_id', $user);
		$qualifiers[] = Qualifier::FK( 'publication_id', $publication);

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
				throw new \Exception( "objectForUserAndPublication expected 1 result, but fetched " . count($result) );
			}
		}

		return false;
	}


	/**
	 * Attribute editing
	 */

	public function attributesMap() {
		return array(
			Reading_Item::user_id => Model::TO_ONE_TYPE,
			Reading_Item::publication_id => Model::TO_ONE_TYPE,
			Reading_Item::created => Model::DATE_TYPE,
			Reading_Item::read_date => Model::DATE_TYPE,
			Reading_Item::mislabeled => Model::FLAG_TYPE
		);
	}

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) === false || is_null($object) == true) {
			switch ($attr) {
				case Reading_Item::mislabeled:
					return Model::TERTIARY_FALSE;
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
				case Reading_Item::user_id:
					$users_model = Model::Named('Users');
					$fkObject = $users_model->objectForId( $value );
					break;
				case Reading_Item::publication_id:
					$publication_model = Model::Named('Publication');
					$fkObject = $publication_model->objectForId( $value );
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
	function validate_user_id($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Reading_Item::user_id,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
	function validate_publication_id($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Reading_Item::publication_id,
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
				Reading_Item::created,
				"IMMUTABLE"
			);
		}
		return null;
	}
	function validate_read_date($object = null, $value)
	{
		// not mandatory field
		if (isset($value) == false || empty($value)  ) {
			return null;
		}

		return null;
	}
	function validate_mislabeled($object = null, $value)
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
				Reading_Item::mislabeled,
				"FILTER_VALIDATE_BOOLEAN"
			);
		}
		return null;
	}
}

?>
