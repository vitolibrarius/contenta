<?php

namespace model;

use \Session as Session;
use \DataObject as DataObject;
use \Model as Model;
use \Localized as Localized;

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
	const path =		'path';
	const small_icon_name =	'small_icon_name';
	const large_icon_name =	'large_icon_name';
	const xurl =		'xurl';
	const xsource =		'xsource';
	const xid =			'xid';
	const xupdated =	'xupdated';


	public function tableName() { return Character::TABLE; }
	public function tablePK() { return Character::id; }
	public function sortOrder() { return array(Character::name); }

	public function dboClassName() { return 'model\\CharacterDBO'; }

	public function allColumnNames()
	{
		return array(
			Character::id, Character::publisher_id, Character::name, Character::realname, Character::desc,
			Character::gender, Character::created, Character::popularity,
			Character::path, Character::small_icon_name, Character::large_icon_name,
			Character::xurl, Character::xsource, Character::xid, Character::xupdated
		);
	}

	public function allForPublisher($obj)
	{
		return $this->fetchAll(Series::TABLE, $this->allColumns(), array(Character::publisher_id => $obj->id), array(Character::name));
	}

	public function allForCharacter($obj)
	{
		return $this->fetchAll(Character::TABLE, $this->allColumns(), array(Character::character_id => $obj->id), array(Character::name));
	}

	public function allForName($name)
	{
		return $this->fetchAll(Character::TABLE,
			$this->allColumns(),
			array(Character::name => $name),
			array(Character::character_id));
	}

	public function forName($obj, $name)
	{
		return $this->fetch(Character::TABLE,
			$this->allColumns(),
			array(Character::character_id => $obj->id, Character::name => $name));
	}

	public function objectForExternal($xid, $xsrc)
	{
		if ( isset($xid, $xsrc) )
		{
			return $this->fetch(Character::TABLE, $this->allColumns(), array(Character::xid => $xid, Character::xsource => $xsrc ));
		}
		return false;
	}

	public function findExternalOrCreate($publishObj, $name, $realname, $gender, $desc, $aliases, $xid, $xsrc, $xurl = null )
	{
		if ( isset($name, $xid, $xsrc) && strlen($name) && strlen($xid) && strlen($xsrc)) {
			$obj = $this->objectForExternal($xid, $xsrc);
			if ( $obj == false )
			{
				$obj = $this->create($publishObj, $name, $realname, $gender, $desc, $aliases, $xid, $xsrc, $xurl);
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
			if ( isset($name) ) {
				if ( isset($realname) == false ) {
					$realname = $name;
				}

				$params = array(
					Character::created => time(),
					Character::name => $name,
					Character::realname => $realname,
					Character::desc => $desc,
					Character::gender => $gender,
					Character::popularity => 0,
					Character::path => sanitize($name),
					Character::small_icon_name => null,
					Character::large_icon_name => null,
					Character::xurl => $xurl,
					Character::xsource => $xsrc,
					Character::xid => $xid,
					Character::xupdated => null
				);

				if ( isset($publishObj)  && is_a($publishObj, '\model\PublisherDBO')) {
					$params[Character::publisher_id] = $publishObj->id;
				}

				$newObjId = $this->createObject($params);
				$obj = ($newObjId != false ? $this->objectForId($newObjId) : false);
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

	public function deleteObject($object = null)
	{
		if ( $object != false )
		{
			$alias_model = Model::Named("Character_Alias");
			if ( $alias_model->deleteAllForCharacter($object) == true ) {
				return parent::deleteObject($object);
			}
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
				$this->update(Character::TABLE, array(Character::popularity => $pop), array(Character::id => $character->id) );
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
		elseif (strlen($value) > 255 OR strlen($value) < 5)
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
