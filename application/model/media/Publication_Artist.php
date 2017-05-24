<?php

namespace model\media;

use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \Localized as Localized;

use \model\media\Publication_ArtistDBO as Publication_ArtistDBO;

/* import related objects */
use \model\media\Publication as Publication;
use \model\media\PublicationDBO as PublicationDBO;
use \model\media\Artist as Artist;
use \model\media\ArtistDBO as ArtistDBO;

class Publication_Artist extends _Publication_Artist
{
	public function searchQualifiers( array $query )
	{
		$qualifiers = parent::searchQualifiers($query);
		return $qualifiers;
	}

	/**
	 *	Create/Update functions
	 */
	public function createObject( array $values = array())
	{
		if ( isset($values) ) {
			// massage values as necessary
		}

		return parent::createObject($values);
	}

	public function updateObject(DataObject $object = null, array $values = array()) {
		if (isset($object) && $object instanceof Publication_ArtistDBO ) {
			// massage values as necessary
		}

		return parent::updateObject($object, $values);
	}

	public function attributesFor($object = null, $type = null) {
		$attrFor = array(
			Publication_Artist::publication_id,
			Publication_Artist::artist_id
		);
		return array_intersect_key($this->attributesMap(),array_flip($attrFor));
	}

	/*
	public function attributeIsEditable($object = null, $type = null, $attr)
	{
		// add customization here
		return parent::attributeIsEditable($object, $type, $attr);
	}
	*/

	/*
	public function attributeRestrictionMessage($object = null, $type = null, $attr)	{ return null; }
	public function attributePlaceholder($object = null, $type = null, $attr)	{ return null; }
	*/

	/*
	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		return parent::attributeDefaultValue($object, $type, $attr);
	}
	*/

	/*
	public function attributeEditPattern($object = null, $type = null, $attr)
	{
		return null;
	}
	*/

	public function attributeOptions($object = null, $type = null, $attr)
	{
		if ( Publication_Artist::publication_id == $attr ) {
			$model = Model::Named('Publication');
			return $model->allObjects();
		}
		if ( Publication_Artist::artist_id == $attr ) {
			$model = Model::Named('Artist');
			return $model->allObjects();
		}
		return null;
	}

	/** Validation */
/*
	function validate_publication_id($object = null, $value)
	{
		return parent::validate_publication_id($object, $value);
	}
*/

/*
	function validate_artist_id($object = null, $value)
	{
		return parent::validate_artist_id($object, $value);
	}
*/

	public function createJoin( PublicationDBO $pubObj, ArtistDBO $artistObj, $role = null)
	{
		if (is_null($role)) {
			$role = Model::Named('Artist_Role')->unknownRole();
		}
		else if ( is_string($role) ) {
			$role = Model::Named('Artist_Role')->findRoleOrCreate($role, $role );
		}

		if (isset($pubObj, $pubObj->id, $artistObj, $artistObj->id)) {
			$existing = $this->objectForPublicationArtistRole($pubObj, $artistObj, $role);

			// the role is not known, check to see if a join exists, otherwise create it
			if ( $role->isUnknown() ) {
				if ($existing == false) {
					list( $existing, $errorList ) = $this->createObject(array(
						"publication" => $pubObj,
						"artist" => $artistObj,
						"artist_role" => $role
						)
					);

					if ( is_array($errorList) ) {
						return $errorList;
					}
				}
				return $existing;
			}

			if ( $existing == false ) {
				// no existing join, check for unknown join and update to correct role
				$unknownJoin = $this->objectForPublicationArtistRole($pubObj, $artistObj, Model::Named('Artist_Role')->unknownRole());
				if ( $unknownJoin != false ) {
					list($join, $errorList) = $this->updateObject( $unknownJoin, array("artist_role" => $role));
					if ( is_array($errorList) ) {
						return $errorList;
					}
					return $join;
				}

				// no unknown join, so create one
				list( $join, $errorList ) = $this->createObject(array(
					"publication" => $pubObj,
					"artist" => $artistObj,
					"artist_role" => $role
					)
				);

				if ( is_array($errorList) ) {
					return $errorList;
				}
				return $join;
			}

			// return existing value
			return $existing;
		}

		return false;
	}
}
