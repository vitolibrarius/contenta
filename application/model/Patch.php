<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;

class Patch extends Model
{
	const TABLE =		'patch';
	const id =			'id';
	const name =		'name';
	const created =		'created';
	const version_id =	'version_id';

	public function tableName() { return Patch::TABLE; }
	public function tablePK() { return Patch::id; }
	public function sortOrder() { return array( "desc" => array(Patch::created)); }

	public function allColumnNames()
	{
		return array(
			Patch::id, Patch::name, Patch::created, Patch::version_id
		 );
	}

	function patchesForVersion(model\VersionDBO $version)
	{
		return $this->allObjectsForFK(Patch::version_id, $version);
	}

	function patchWithName($name)
	{
		return $this->singleObjectForKeyValue(Patch::name, $name);
	}

	public function create($version, $name)
	{
		$obj = false;
		if ( isset($version, $name) ) {
			$obj = $this->patchWithName($name);
			if ( is_a($obj, "model\\PatchDBO") == false ) {
				$params = array(
					Patch::name => $name,
					Patch::version_id => $version->id,
					Patch::created => time()
				);

				list( $obj, $errorList ) = $this->createObject($params);
				if ( is_array($errorList) ) {
					return $errorList;
				}
			}
		}
		return $obj;
	}

	public function deleteObject(\DataObject $object = null)
	{
		if ( $object != false && $object instanceof model\PatchDBO )
		{
			return parent::deleteObject($object );
		}
		return false;
	}
}

?>
