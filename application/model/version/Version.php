<?php

namespace model\version;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use model\version\VersionDBO as VersionDBO;

/** Sample Creation script */
		/** VERSION
		$sql = "CREATE TABLE IF NOT EXISTS version ( "
			. model\version\Version::id . " INTEGER PRIMARY KEY, "
			. model\version\Version::code . " TEXT, "
			. model\version\Version::major . " INTEGER, "
			. model\version\Version::minor . " INTEGER, "
			. model\version\Version::patch . " INTEGER, "
			. model\version\Version::created . " INTEGER, "
			. model\version\Version::hash_code . " TEXT, "
			. ")";
		$this->sqlite_execute( "version", $sql, "Create table version" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS version_code on version (code)';
		$this->sqlite_execute( "version", $sql, "Index on version (code)" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS version_hash_code on version (hash_code)';
		$this->sqlite_execute( "version", $sql, "Index on version (hash_code)" );
		$sql = 'CREATE  INDEX IF NOT EXISTS version_majorminorpatch on version (major,minor,patch)';
		$this->sqlite_execute( "version", $sql, "Index on version (major,minor,patch)" );
*/
class Version extends Model
{
	const TABLE = 'version';
	const id = 'id';
	const code = 'code';
	const major = 'major';
	const minor = 'minor';
	const patch = 'patch';
	const created = 'created';
	const hash_code = 'hash_code';

	public function tableName() { return Version::TABLE; }
	public function tablePK() { return Version::id; }
	public function sortOrder() { return array( 'asc' => array(Version::code, )); }

	public function allColumnNames()
	{
		return array(
Version::id, Version::code, Version::major, Version::minor, Version::patch, Version::created, Version::hash_code, 		 );
	}

	/** * * * * * * * * *
		Basic search functions
	 */
	public function objectForCode($value)
	{
		return $this->singleObjectForKeyValue(Version::code, $value);
	}

	public function allLikeCode($value)
	{
		return SQL::Select( $this )
			->where( Qualifier::Like( Version::code, $value, SQL::SQL_LIKE_AFTER ))
			->orderBy( $this->sortOrder() )
			->limit( 50 )
			->fetchAll();
	}
	public function objectForHash_code($value)
	{
		return $this->singleObjectForKeyValue(Version::hash_code, $value);
	}



	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				default:
					break;
			}
		}
		return parent::joinAttributes( $joinModel );
	}

	public function create( $code, $major, $minor, $patch, $hash_code)
	{
		$obj = false;
		if ( isset($code, $hash_code) ) {
			$params = array(
				Version::code => (isset($code) ? $code : ''),
				Version::major => (isset($major) ? $major : null),
				Version::minor => (isset($minor) ? $minor : null),
				Version::patch => (isset($patch) ? $patch : null),
				Version::created => time(),
				Version::hash_code => (isset($hash_code) ? $hash_code : ''),
			);


			list( $obj, $errorList ) = $this->createObject($params);
			if ( is_array($errorList) ) {
				return $errorList;
			}
		}
		return $obj;
	}

	public function deleteObject( DataObject $object = null)
	{
		if ( $object instanceof Version )
		{
			return parent::deleteObject($object);
		}

		return false;
	}

	public function latestVersion(  )
	{
		$select = SQL::Select( $this );
		$qualifiers = array();
		$qualifiers[] = Qualifier::InSubQuery( 'code', SQL::Aggregate( 'max', Model::Named('Version'), 'code', null, null ), null);

		if ( count($qualifiers) > 0 ) {
			$select->where( Qualifier::Combine( 'AND', $qualifiers ));
		}

		$result = $select->fetchAll();
		if ( is_array($result) && count($result) > 1 ) {
			throw new \Exception( latestVersion . " expected 1 result, but fetched " . count($result) );
		}

		return (is_array($result) ? $result[0] : false );
	}

}

?>
