<?php

namespace model\media;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \exceptions\DeleteObjectException as DeleteObjectException;

use \model\media\Story_Arc_PublicationDBO as Story_Arc_PublicationDBO;

/* import related objects */
use \model\media\Story_Arc as Story_Arc;
use \model\media\Story_ArcDBO as Story_ArcDBO;
use \model\media\Publication as Publication;
use \model\media\PublicationDBO as PublicationDBO;

/** Sample Creation script */
		/** STORY_ARC_PUBLICATION */
/*
		$sql = "CREATE TABLE IF NOT EXISTS story_arc_publication ( "
			. Story_Arc_Publication::id . " INTEGER PRIMARY KEY, "
			. Story_Arc_Publication::story_arc_id . " INTEGER, "
			. Story_Arc_Publication::publication_id . " INTEGER, "
			. "FOREIGN KEY (". Story_Arc_Publication::story_arc_id .") REFERENCES " . Story_Arc::TABLE . "(" . Story_Arc::id . "),"
			. "FOREIGN KEY (". Story_Arc_Publication::publication_id .") REFERENCES " . Publication::TABLE . "(" . Publication::id . ")"
		. ")";
		$this->sqlite_execute( "story_arc_publication", $sql, "Create table story_arc_publication" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS story_arc_publication_story_arc_idpublication_id on story_arc_publication (story_arc_id,publication_id)';
		$this->sqlite_execute( "story_arc_publication", $sql, "Index on story_arc_publication (story_arc_id,publication_id)" );
*/
abstract class _Story_Arc_Publication extends Model
{
	const TABLE = 'story_arc_publication';

	// attribute keys
	const id = 'id';
	const story_arc_id = 'story_arc_id';
	const publication_id = 'publication_id';

	// relationship keys
	const story_arc = 'story_arc';
	const publication = 'publication';

	public function tableName() { return Story_Arc_Publication::TABLE; }
	public function tablePK() { return Story_Arc_Publication::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Story_Arc_Publication::story_arc_id)
		);
	}

	public function allColumnNames()
	{
		return array(
			Story_Arc_Publication::id,
			Story_Arc_Publication::story_arc_id,
			Story_Arc_Publication::publication_id
		);
	}

	public function allAttributes()
	{
		return array(
		);
	}

	public function allForeignKeys()
	{
		return array(Story_Arc_Publication::story_arc_id,
			Story_Arc_Publication::publication_id);
	}

	public function allRelationshipNames()
	{
		return array(
			Story_Arc_Publication::story_arc,
			Story_Arc_Publication::publication
		);
	}

	/**
	 *	Simple fetches
	 */




	/**
	 * Simple relationship fetches
	 */
	public function allForStory_arc($obj)
	{
		return $this->allObjectsForFK(Story_Arc_Publication::story_arc_id, $obj, $this->sortOrder(), 50);
	}

	public function countForStory_arc($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Story_Arc_Publication::story_arc_id, $obj );
		}
		return false;
	}
	public function allForPublication($obj)
	{
		return $this->allObjectsForFK(Story_Arc_Publication::publication_id, $obj, $this->sortOrder(), 50);
	}

	public function countForPublication($obj)
	{
		if ( is_null($obj) == false ) {
			return $this->countForFK( Story_Arc_Publication::publication_id, $obj );
		}
		return false;
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "story_arc":
					return array( Story_Arc_Publication::story_arc_id, "id"  );
					break;
				case "publication":
					return array( Story_Arc_Publication::publication_id, "id"  );
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

			// default conversion for relationships
			if ( isset($values['story_arc']) ) {
				$local_story_arc = $values['story_arc'];
				if ( $local_story_arc instanceof Story_ArcDBO) {
					$values[Story_Arc_Publication::story_arc_id] = $local_story_arc->id;
				}
				else if ( is_integer( $local_story_arc) ) {
					$params[Story_Arc_Publication::story_arc_id] = $local_story_arc;
				}
			}
			if ( isset($values['publication']) ) {
				$local_publication = $values['publication'];
				if ( $local_publication instanceof PublicationDBO) {
					$values[Story_Arc_Publication::publication_id] = $local_publication->id;
				}
				else if ( is_integer( $local_publication) ) {
					$params[Story_Arc_Publication::publication_id] = $local_publication;
				}
			}
		}
		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Story_Arc_Publication ) {
			if ( isset($values['story_arc']) ) {
				$local_story_arc = $values['story_arc'];
				if ( $local_story_arc instanceof Story_ArcDBO) {
					$values[Story_Arc_Publication::story_arc_id] = $local_story_arc->id;
				}
				else if ( is_integer( $local_story_arc) ) {
					$params[Story_Arc_Publication::story_arc_id] = $values['story_arc'];
				}
			}
			if ( isset($values['publication']) ) {
				$local_publication = $values['publication'];
				if ( $local_publication instanceof PublicationDBO) {
					$values[Story_Arc_Publication::publication_id] = $local_publication->id;
				}
				else if ( is_integer( $local_publication) ) {
					$params[Story_Arc_Publication::publication_id] = $values['publication'];
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
		if ( $object instanceof Story_Arc_PublicationDBO )
		{
			// does not own story_arc Story_Arc
			// does not own publication Publication
			return parent::deleteObject($object);
		}

		return false;
	}

	public function deleteAllForStory_arc(Story_ArcDBO $obj)
	{
		$success = true;
		if ( $obj != false ) {
			$array = $this->allForStory_arc($obj);
			while ( is_array($array) && count($array) > 0) {
				foreach ($array as $key => $value) {
					if ($this->deleteObject($value) == false) {
						$success = false;
						throw new DeleteObjectException("Failed to delete " . $value, $value->id );
					}
				}
				$array = $this->allForStory_arc($obj);
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
	public function objectForStoryArcAndPublication(Story_ArcDBO $story,PublicationDBO $pub )
	{
		$select = SQL::Select( $this );
		$select->orderBy( $this->sortOrder() );
		$qualifiers = array();
		$qualifiers[] = Qualifier::FK( 'publication_id', $pub);
		$qualifiers[] = Qualifier::FK( 'story_arc_id', $story);

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
				throw new \Exception( "objectForStoryArcAndPublication expected 1 result, but fetched " . count($result) );
			}
		}

		return false;
	}


	/**
	 * Attribute editing
	 */

	public function attributesMap() {
		return array(
			Story_Arc_Publication::story_arc_id => Model::TO_ONE_TYPE,
			Story_Arc_Publication::publication_id => Model::TO_ONE_TYPE
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
				case Story_Arc_Publication::story_arc_id:
					$story_arc_model = Model::Named('Story_Arc');
					$fkObject = $story_arc_model->objectForId( $value );
					break;
				case Story_Arc_Publication::publication_id:
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
	function validate_story_arc_id($object = null, $value)
	{
		// check for mandatory field
		if (isset($value) == false || empty($value)  ) {
			return Localized::ModelValidation(
				$this->tableName(),
				Story_Arc_Publication::story_arc_id,
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
				Story_Arc_Publication::publication_id,
				"FIELD_EMPTY"
			);
		}

		return null;
	}
}

?>
