<?php

namespace model\version;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use \model\version\PatchDBO as PatchDBO;

/* import related objects */
use \model\version\Version as Version;
use \model\version\VersionDBO as VersionDBO;

/** Sample Creation script */
		/** PATCH */
/*
		$sql = "CREATE TABLE IF NOT EXISTS patch ( "
			. Patch::id . " INTEGER PRIMARY KEY, "
			. Patch::name . " TEXT, "
			. Patch::created . " INTEGER, "
			. Patch::version_id . " INTEGER, "
			. "FOREIGN KEY (". Patch::version_id .") REFERENCES " . Version::TABLE . "(" . Version::id . ")"
		. ")";
		$this->sqlite_execute( "patch", $sql, "Create table patch" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS patch_name on patch (name)';
		$this->sqlite_execute( "patch", $sql, "Index on patch (name)" );
*/
abstract class _Patch extends Model
{
	const TABLE = 'patch';
	const id = 'id';
	const name = 'name';
	const created = 'created';
	const version_id = 'version_id';

	public function tableName() { return Patch::TABLE; }
	public function tablePK() { return Patch::id; }

	public function sortOrder()
	{
		return array(
			array( 'asc' => Patch::name)
		);
	}

	public function allColumnNames()
	{
		return array(
			Patch::id,
			Patch::name,
			Patch::created,
			Patch::version_id
		);
	}

	/** * * * * * * * * *
		Basic search functions
	 */
	public function objectForName($value)
	{
		return $this->singleObjectForKeyValue(Patch::name, $value);
	}

	public function allLikeName($value)
	{
		return SQL::Select( $this )
			->where( Qualifier::Like( Patch::name, $value, SQL::SQL_LIKE_AFTER ))
			->orderBy( $this->sortOrder() )
			->limit( 50 )
			->fetchAll();
	}

	public function allForVersion($obj)
	{
		return $this->allObjectsForFK(Patch::version_id, $obj, $this->sortOrder(), 50);
	}

	public function joinAttributes( Model $joinModel = null )
	{
		if ( is_null($joinModel) == false ) {
			switch ( $joinModel->tableName() ) {
				case "version":
					return array( Patch::version_id, "id"  );
					break;
				default:
					break;
			}
		}
		return parent::joinAttributes( $joinModel );
	}

	public function create( $version, $name)
	{
		$obj = false;
		if ( isset($version, $name) ) {
			$params = array(
				Patch::name => (isset($name) ? $name : null),
				Patch::created => time(),
			);

			if ( isset($version) ) {
				if ( $version instanceof VersionDBO) {
					$params[Patch::version_id] = $version->id;
				}
				else if (  is_integer($version) ) {
					$params[Patch::version_id] = $version;
				}
			}

			list( $obj, $errorList ) = $this->createObject($params);
			if ( is_array($errorList) ) {
				return $errorList;
			}
		}
		return $obj;
	}

	public function deleteObject( DataObject $object = null)
	{
		if ( $object instanceof Patch )
		{
			// does not own Version
			return parent::deleteObject($object);
		}

		return false;
	}

}

?>
