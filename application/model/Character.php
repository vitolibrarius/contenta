<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;
use \Localized as Localized;
use \Logger as Logger;

class Character extends Model
{
	const TABLE =		'character';
	const id =			'id';
	const publisher_id =	'publisher_id';
	const name =		'name';
	const desc =		'desc';
	const realname =	'realname';
	const popularity =	'popularity';
	const gender =		'gender';
	const created =	'created';
	const xurl =		'xurl';
	const xsource =		'xsource';
	const xid =			'xid';
	const xupdated =	'xupdated';


	public function tableName() { return Character::TABLE; }
	public function tablePK() { return Character::id; }
	public function sortOrder() { return array(Character::name); }

	public function allColumnNames()
	{
		return array(
			Character::id, Character::publisher_id, Character::name, Character::realname, Character::desc,
			Character::gender, Character::created, Character::popularity,
			Character::xurl, Character::xsource, Character::xid, Character::xupdated
		);
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "publisher":
					return array( Character::publisher_id, "id" );
					break;
				case "series_character":
					return array( Character::id, "character_id" );
					break;
				case "publication_character":
				case "story_arc_character":
				default:
					break;
			}
		}
		return parent::joinAttributes( $joinModel );
	}


	public function allForPublisher( model\PublisherDBO $obj)
	{
		return $this->allObjectsForFK(Character::publisher_id, $obj, array(Character::name));
	}

	public function allForName($name)
	{
		return $this->allObjectsForKeyValue(Character::name, $name);
	}

	public function findExternalOrCreate($publishObj, $name, $realname, $gender, $desc, $aliases, $xid, $xsrc, $xurl = null )
	{
		if ( isset($name, $xid, $xsrc) && strlen($name) && strlen($xid) && strlen($xsrc)) {
			$obj = $this->objectForExternal($xid, $xsrc);
			if ( $obj == false )
			{
				$obj = $this->create($publishObj, $name, $realname, $gender, $desc, $aliases, $xid, $xsrc, $xurl);
			}
			else {
				$updates = array();

				if ( isset($publishObj, $publishObj->id) && (isset($obj->publisher_id) == false || $publishObj->id != $obj->publisher_id) ) {
					$updates[Character::publisher_id] = $publishObj->id;
				}

				if (isset($name) && (isset($obj->name) == false || $name != $obj->name)) {
					$updates[Character::name] = $name;
				}

				if (isset($realname) && $realname != $obj->realname ) {
					$updates[Character::realname] = $realname;
				}

				if (isset($gender) && $gender != $obj->gender ) {
					$updates[Character::gender] = $gender;
				}

				if (isset($desc) && $desc != $obj->desc ) {
					$updates[Character::desc] = strip_tags($desc);
				}

				if ( isset($xid) ) {
					$updates[Character::xupdated] = time();

					if ((isset($xurl) && strlen($xurl) > 0) && (isset($obj->xurl) == false || strlen($obj->xurl) == 0)) {
						$updates[Character::xurl] = $xurl;
					}
				}

				if ( count($updates) > 0 ) {
					$this->updateObject($obj, $updates );
				}

				if ( $obj != false && is_array($aliases) ) {
					$char_model = Model::Named("Character_Alias");
					foreach ($aliases as $key => $value) {
						$char_model->create($obj, $value);
					}
				}
			}
			return $obj;
		}
		return false;
	}

	public function create($publishObj, $name, $realname, $gender, $desc, $aliases, $xid = null, $xsrc = null, $xurl = null)
	{
		$obj = $this->objectForExternal($xid, $xsrc);
		if ( $obj == false )
		{
			if ( isset($realname) == false ) {
				$realname = $name;
			}

			$params = array(
				Character::created => time(),
				Character::name => $name,
				Character::realname => $realname,
				Character::desc => strip_tags($desc),
				Character::gender => $gender,
				Character::popularity => 0,
				Character::xurl => $xurl,
				Character::xsource => $xsrc,
				Character::xid => $xid,
				Character::xupdated => (is_null($xid) ? null : time())
			);

			if ( isset($publishObj)  && is_a($publishObj, '\model\PublisherDBO')) {
				$params[Character::publisher_id] = $publishObj->id;
			}

			list( $obj, $errorList ) = $this->createObject($params);
			if ( is_array($errorList) ) {
				return $errorList;
			}
		}

		if ( $obj != false && is_array($aliases) ) {
			$char_model = Model::Named("Character_Alias");
			foreach ($aliases as $key => $value) {
				$char_model->create($obj, $value);
			}
		}

		return $obj;
	}

	public function deleteObject( \DataObject $object = null)
	{
		if ( $object != false && $object instanceof model\CharacterDBO)
		{
			$alias_model = Model::Named("Character_Alias");
			if ( $alias_model->deleteAllForCharacter($object) == false ) {
				throw new exceptions\DeleteObjectException("Failed to delete aliases " . $object, $object->id );
			}

			$pub_char_model = Model::Named("Publication_Character");
			if ( $pub_char_model->deleteAllForCharacter($object) == false ) {
				throw new exceptions\DeleteObjectException("Failed to delete publication characters " . $object, $object->id );
			}

			$story_char_model = Model::Named("Story_Arc_Character");
			if ( $story_char_model->deleteAllForCharacter($object) == false ) {
				throw new exceptions\DeleteObjectException("Failed to delete story_arc characters " . $object, $object->id );
			}

			$series_model = Model::Named("Series_Character");
			if ( $series_model->deleteAllForCharacter($object) == false ) {
				throw new exceptions\DeleteObjectException("Failed to delete series characters " . $object, $object->id );
			}

			return parent::deleteObject($object);
		}

		return false;
	}

	public function joinToSeries($character, $series) {
		$model = Model::Named('Series_Character');
		return $model->create($series, $character);
	}

	public function updatePopularity($character = null) {
		if ( is_null($character) ) {
			Model::Named('Series_Character');
			$this->updateAgregate(
				Character::TABLE, Series_Character::TABLE,
				Character::popularity, "count(*)",
				Character::id, Series_Character::character_id
			);
		}
		else {
			$characterjoin_model = Model::Named('Series_Character');
			$pop = $characterjoin_model->countForCharacter($character);
			if ($character->popularity != $pop ) {
				$this->updateObject( $character, array(Character::popularity => $pop));
			}
			return $pop;
		}
		return false;
	}

	/* EditableModelInterface */
	function validate_name($object = null, $value)
	{
		if (empty($value))
		{
			return Localized::ModelValidation($this->tableName(), Character::name, "FIELD_EMPTY");
		}
		else if (strlen($value) > 256 )
		{
			return Localized::ModelValidation($this->tableName(), Character::name, "FIELD_TOO_LONG" );
		}
		return null;
	}

	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Character::name
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesFor($object = null, $type = null ) {
		return array(
			Character::name => Model::TEXT_TYPE,
			Character::realname => Model::TEXT_TYPE,
			Character::desc => Model::TEXTAREA_TYPE,
			Character::gender => Model::TEXT_TYPE,
			Character::publisher_id => Model::TO_ONE_TYPE
		);
	}

	public function attributeOptions($object = null, $type = null, $attr) {
		switch ($attr) {
			case Character::publisher_id:
				$model = Model::Named('Publisher');
				return $model->allObjects();
			default:
				return null;
		}
		return null;
	}

	public function attributeRestrictionMessage($object = null, $type = null, $attr)
	{
		return null;
	}
}

?>
