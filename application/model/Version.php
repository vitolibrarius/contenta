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

	public function allColumnNames()
	{
		return array(
			Version::id, Version::code, Version::hash_code,
			Version::major, Version::minor, Version::patch,
			Version::created
		 );
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "patch":
					return array( Version::id, "version_id"  );
					break;
				default:
					break;
			}
		}
		return parent::joinAttributes( $joinModel );
	}

	function latestVersion()
	{
		$onlyOne = \SQL::Select( $this )->orderBy($this->sortOrder())->limit(1)->fetchAll();
		return (is_array($onlyOne) ? $onlyOne[0] : false );
	}

	function versionForCode($name)
	{
		return $this->singleObjectForKeyValue(Version::code, $name);
	}

	function versionForHash($hash)
	{
		return \SQL::Select( $this )->whereEqual( Version::hash_code, $hash )->fetch();
	}

	function create($versionNum, $versionHash)
	{
		$obj = false;
		if ( isset($versionNum, $versionHash) ) {
			$obj = $this->versionForCode($versionNum);
			if ( $obj instanceof model\VersionDBO ) {
				$updates = array();
				if ($obj->hash_code != $versionHash) {
					$updates[Version::hash_code] = $versionHash;
				}

				if ( count($updates) > 0 ) {
					$this->updateObject( $obj, $updates);
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

				list( $obj, $errorList ) = $this->createObject($params);
				if ( is_array($errorList) ) {
					return $errorList;
				}
			}
		}
		return $obj;
	}
}

?>
