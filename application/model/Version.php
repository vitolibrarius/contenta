<?php

namespace model;

use \DataObject as DataObject;
use model\VersionDBO as VersionDBO;
use \Model as Model;

class Version extends Model
{
	const TABLE =		'version';
	const id =			'id';
	const code =		'code';
	const major =		'major';
	const minor =		'minor';
	const patch =		'patch';
	const created =		'created';
	const hash_code =	'hash_code';

	public function tableName() { return Version::TABLE; }
	public function tablePK() { return Version::id; }
	public function sortOrder() { return array( 'desc' => array(Version::code)); }

	public function dboClassName() { return 'model\\VersionDBO'; }

	public function allColumnNames()
	{
		return array(
			Version::id, Version::code, Version::hash_code,
			Version::major, Version::minor, Version::patch,
			Version::created
		 );
	}

	function latestVersion()
	{
		$onlyOne = $this->fetchAll(Version::TABLE, $this->allColumnNames(), null, $this->sortOrder(), 1 );
		return (is_array($onlyOne) ? $onlyOne[0] : false );
	}

	function versions()
	{
		return $this->fetchAll(Version::TABLE, $this->allColumnNames(), null, $this->sortOrder() );
	}

	function versionForCode($name)
	{
		return $this->fetch(Version::TABLE, $this->allColumnNames(), array(Version::code => $name));
	}

	function versionForHash($hash)
	{
		return $this->fetch(Version::TABLE, $this->allColumnNames(), array(Version::hash_code => $hash));
	}

	function create($versionNum, $versionHash)
	{
		$obj = false;
		if ( isset($versionNum, $versionHash) ) {
			$obj = $this->versionForCode($versionNum);
			if ( is_a( $obj, "model\\VersionDBO" ) ) {
				$updates = array();
				if ($obj->hash_code != $versionHash) {
					$updates[Version::hash_code] = $versionHash;
				}

				if ( count($updates) > 0 ) {
					$this->update(Version::TABLE, $updates, array(Version::id => $obj->id) );
					$obj = $this->refreshObject($obj);
				}
			}
			else {
				$vers = explode(".", $versionNum );

				$params = array(
					Version::created => time(),
					Version::code => $versionNum,
					Version::hash_code => $versionHash,
					Version::major => (isset($vers[0]) ? intval($vers[0]) : 0),
					Version::minor => (isset($vers[1]) ? intval($vers[1]) : 0),
					Version::patch => (isset($vers[2]) ? intval($vers[2]) : 0)
				);

				$newObjId = $this->createObj(Version::TABLE, $params);
				$obj = ($newObjId != false ? $this->objectForId($newObjId) : false);
			}
		}
		return $obj;
	}
}

?>
