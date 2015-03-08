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
	const path =			'path';
	const small_icon_name =	'small_icon_name';
	const large_icon_name =	'large_icon_name';
	const xurl =	'xurl';
	const xsource =	'xsource';
	const xid =		'xid';
	const xupdated = 'xupdated';

	public function tableName() { return Publisher::TABLE; }
	public function tablePK() { return Publisher::id; }
	public function sortOrder() { return array(Publisher::name); }

	public function dboClassName() { return "\model\PublisherDBO"; }

	public function allColumnNames()
	{
		return array(
			Publisher::id, Publisher::name, Publisher::created, Publisher::updated,
			Publisher::path, Publisher::small_icon_name, Publisher::large_icon_name,
			Publisher::xurl, Publisher::xsource, Publisher::xid, Publisher::xupdated
		);
	}

	public function UnknownPublisher() {
		return $this->findExternalOrCreate( '-= Unknown Publisher =-', '-unknown-', 'UNKNOWN', null );
	}

	public function objectForExternal($xid, $xsrc)
	{
		if ( isset($xid) )
		{
			$qual = array(Publisher::xid => $xid);
			if ( isset($xsrc) ) {
				$qual[Publisher::xsource] = $xsrc;
			}

			return $this->fetch(Publisher::TABLE, $this->allColumns(), $qual);
		}
		return false;
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
			if ( $obj == false )
			{
				$obj = $this->create($name, $xid, $xsrc, $xurl);
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
				Publisher::path => null,
				Publisher::small_icon_name => null,
				Publisher::large_icon_name => null,
				Publisher::xurl => $xurl,
				Publisher::xsource => $xsrc,
				Publisher::xid => $xid,
				Publisher::xupdated => (is_null($xid) ? null : time())
				)
			);
		}

		return ((isset($newObjId) && $newObjId != false) ? $this->objectForId($newObjId) : false);
	}

	public function createObject($values)
	{
		return parent::createObject($values);
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
		elseif (strlen($value) > 255 OR strlen($value) < 5)
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

