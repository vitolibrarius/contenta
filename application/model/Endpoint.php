<?php

namespace model;

use \Database as Database;
use \DataObject as DataObject;
use \Model as Model;

class Endpoint extends Model
{
	const TABLE =			'endpoint';
	const id =				'id';
	const type_id =			'type_id';
	const name =			'name';
	const base_url =		'base_url';
	const api_key =			'api_key';
	const username =		'username';
	const enabled =			'enabled';
	const compressed =		'compressed';


	public function __construct(Database $db)
	{
		parent::__construct($db);
	}

	public function tableName() { return Endpoint::TABLE; }
	public function tablePK() { return Endpoint::id; }
	public function sortOrder() { return array(Endpoint::type_id, Endpoint::name); }

	public function allColumnNames()
	{
		return array(
			Endpoint::id, Endpoint::type_id, Endpoint::name, Endpoint::base_url,
			Endpoint::api_key, Endpoint::username, Endpoint::enabled, Endpoint::compressed
		);
	}

	public function allForTypeCode($code = null)
	{
		if ( $code != null ) {
			$type_model = Model::Named('Endpoint_Type');
			$type = $type_model->endpointTypeForCode($code);
			if ( $type != false ) {
				return $this->allForType($type);
			}
		}
		return false;
	}

	public function allForType($obj)
	{
		return $this->fetchAll(Endpoint::TABLE, $this->allColumns(), array(Endpoint::type_id => $obj->id), array(Endpoint::name));
	}

	public function endpointForTypeAndUrl($obj, $endpointURL)
	{
		return $this->fetch(Endpoint::TABLE, $this->allColumns(), array(
			Endpoint::type_id => $obj->id,	Endpoint::base_url => $endpointURL ));
	}

	public function create($typeObj, $name, $endpointURL, $apiKey = null, $username = null, $enabled = true,  $compressed = false)
	{
		if ( isset($typeObj, $name, $endpointURL) ) {
			$params = array(
				Endpoint::name => $name,
				Endpoint::type_id => $typeObj->id,
				Endpoint::base_url => $endpointURL,
				Endpoint::api_key => $apiKey,
				Endpoint::username => $username,
				Endpoint::enabled => ($enabled)? Model::TERTIARY_TRUE : Model::TERTIARY_FALSE,
				Endpoint::compressed => ($compressed)? Model::TERTIARY_TRUE : Model::TERTIARY_FALSE
			);

			$newObjId = $this->createObj(Endpoint::TABLE, $params);
			$obj = ($newObjId != false ? $this->objectForId($newObjId) : false);
		}

		return $obj;
	}

	public function deleteRecord($obj)
	{
		if ( $obj != false )
		{
			return $this->deleteObj($obj, Endpoint::TABLE, Endpoint::id );
		}
		return false;
	}

	/* EditableModelInterface */

	public function attributesFor($object = null, $type = null ) {
		return array(
			Endpoint::name => Model::TEXT_TYPE,
			Endpoint::base_url => Model::TEXT_TYPE,
			Endpoint::api_key => Model::TEXT_TYPE,
			Endpoint::username => Model::TEXT_TYPE,
			Endpoint::enabled => Model::FLAG_TYPE,
			Endpoint::compressed => Model::FLAG_TYPE
		);
	}

	public function attributeOptions($object = null, $type = null, $attr) {
		if ( $attr == Endpoint::type_id ) {
			$type_model = Model::Named('Endpoint_Type');
			return $type_model->endpointTypes();
		}
		return null;
	}

	public function attributeIsEditable($object = null, $type = null, $attr) {
		if ( is_a($object, "model\\EndpointDBO" ) ) {
			if ( $attr == Endpoint::type_id ) {
				return false;
			}
		}

		return true;
	}

	public function attributeDefaultValue($object = null, $type = null, $attr)
	{
		if ( isset($object) == false || is_null($object) == true) {
			if ( isset($type) && is_a($type, "model\\Endpoint_TypeDBO" ) ) {
				switch ($attr) {
					case Endpoint::base_url:
						return $type->api_url;
					case Endpoint::name:
						return $type->name;
					case Endpoint::compressed:
						return Model::TERTIARY_FALSE;
					case Endpoint::enabled:
						return Model::TERTIARY_TRUE;
				}
			}
		}
		return parent::attributeDefaultValue($object, $type, $attr);
	}

	public function attributeRestrictionMessage($object = null, $type = null, $attr)
	{
		if ( $attr == Endpoint::type_id ) {
			if ( isset($type, $type->comments) && is_a($type, "model\\Endpoint_TypeDBO" ) ) {
				return $type->comments;
			}
		}

		return null;
	}

}
