<?php
namespace model;

use \DataObject as DataObject;
use \Model as Model;
use \Localized as Localized;
use \Config as Config;

class Publisher extends Model
{
	const TABLE =	'publisher';
	const id =		'id';
	const name =	'name';
	const created = 'created';
	const updated = 'updated';
	const xurl =	'xurl';
	const xsource =	'xsource';
	const xid =		'xid';
	const xupdated = 'xupdated';

	public function tableName() { return Publisher::TABLE; }
	public function tablePK() { return Publisher::id; }
	public function sortOrder() { return array(Publisher::name); }

	public function allColumnNames()
	{
		return array(
			Publisher::id, Publisher::name, Publisher::created, Publisher::updated,
			Publisher::xurl, Publisher::xsource, Publisher::xid, Publisher::xupdated
		);
	}

	public function UnknownPublisher() {
		return $this->findExternalOrCreate( '-= Unknown Publisher =-', '-unknown-', 'UNKNOWN', null );
	}

	public function findOrCreate( $name )
	{
		$obj = $this->objForName($name);
		if ( $obj == false )
		{
			$obj = $this->create($name);
		}
		return $obj;
	}

	public function findExternalOrCreate( $name, $xid, $xsrc, $xurl = null )
	{
		if ( isset($name, $xid, $xsrc) && strlen($name) && strlen($xid) && strlen($xsrc)) {
			$obj = $this->objectForExternal($xid, $xsrc);
			if ( $obj == false ) {
				$obj = $this->create($name, $xid, $xsrc, $xurl);
			}
			else {
				$updates = array();

				if (isset($name) && $name != $obj->name ) {
					$updates[Publisher::name] = $name;
				}

				if ( isset($xid) ) {
					$updates[Publisher::xupdated] = time();

					if ((isset($xurl) && strlen($xurl) > 0) && (isset($obj->xurl) == false || strlen($obj->xurl) == 0)) {
						$updates[Publisher::xurl] = $xurl;
					}
				}

				if ( count($updates) > 0 ) {
					$this->updateObject($obj, $updates );
				}
			}

			return $obj;
		}
		return false;
	}

	public function create( $name, $xid = null, $xsrc = null, $xurl = null)
	{
		if ( isset($name) && strlen($name)) {
			$newObjId = $this->createObject( array(
				Publisher::created => time(),
				Publisher::name => $name,
				Publisher::xurl => $xurl,
				Publisher::xsource => $xsrc,
				Publisher::xid => $xid,
				Publisher::xupdated => (is_null($xid) ? null : time())
				)
			);
		}

		return ((isset($newObjId) && $newObjId != false) ? $this->objectForId($newObjId) : false);
	}

	public function deleteObject($object = null)
	{
		if ( $object != false )
		{
			// delete any relationships
			return parent::deleteObject($object);
		}

		return false;
	}

	public function objForName($name = 'none')
	{
		return $this->fetch(Publisher::TABLE, $this->allColumns(), array(Publisher::name => $name));
	}

	public function updateXReference($obj, $xurl, $xsrc, $xid )
	{
		if ( $obj != false )
		{
			$this->update(Publisher::TABLE,
				array(
					Publisher::xurl => $xurl,
					Publisher::xsource => $xsrc,
					Publisher::xid => $xid,
					Publisher::xupdated => time()
				),
				array(Publisher::id => $obj->id)
			);

			return $this->refreshObject($obj);
		}
		return false;
	}
	/* EditableModelInterface */
	function validate_name($object = null, $value)
	{
		if (empty($value))
		{
			return Localized::ModelValidation($this->tableName(), Publisher::name, "FIELD_EMPTY");
		}
		elseif (strlen($value) > 256)
		{
			return Localized::ModelValidation($this->tableName(), Publisher::name, "FIELD_TOO_LONG" );
		}
		return null;
	}

	public function attributesMandatory($object = null)
	{
		if ( is_null($object) ) {
			return array(
				Publisher::name
			);
		}
		return parent::attributesMandatory($object);
	}

	public function attributesFor($object = null, $type = null ) {
		return array(
			Publisher::name => Model::TEXT_TYPE
		);
	}

	public function attributeRestrictionMessage($object = null, $type = null, $attr)
	{
		return null;
	}
}

