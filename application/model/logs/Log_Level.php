<?php

namespace model\logs;


use \DataObject as DataObject;
use \Model as Model;
use \Logger as Logger;
use \SQL as SQL;
use \db\Qualifier as Qualifier;

use model\logs\Log_LevelDBO as Log_LevelDBO;

/** Sample Creation script */
		/** LOG_LEVEL
		$sql = "CREATE TABLE IF NOT EXISTS log_level ( "
			. model\logs\Log_Level::id . " INTEGER PRIMARY KEY, "
			. model\logs\Log_Level::code . " TEXT, "
			. model\logs\Log_Level::name . " TEXT, "
			. ")";
		$this->sqlite_execute( "log_level", $sql, "Create table log_level" );

		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS log_level_code on log_level (code)';
		$this->sqlite_execute( "log_level", $sql, "Index on log_level (code)" );
		$sql = 'CREATE UNIQUE INDEX IF NOT EXISTS log_level_name on log_level (name)';
		$this->sqlite_execute( "log_level", $sql, "Index on log_level (name)" );
*/
class Log_Level extends Model
{
	const TABLE = 'log_level';
	const id = 'id';
	const code = 'code';
	const name = 'name';

	public function tableName() { return Log_Level::TABLE; }
	public function tablePK() { return Log_Level::id; }
	public function sortOrder()
	{
		return array(
			array( 'asc' => Log_Level::name)
		);
	}

	public function allColumnNames()
	{
		return array(
			Log_Level::id,
			Log_Level::code,
			Log_Level::name
		);
	}

	/** * * * * * * * * *
		Basic search functions
	 */
	public function objectForCode($value)
	{
		return $this->singleObjectForKeyValue(Log_Level::code, $value);
	}

	public function objectForName($value)
	{
		return $this->singleObjectForKeyValue(Log_Level::name, $value);
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

	public function create( $code, $name)
	{
		$obj = false;
		if ( isset($code) ) {
			$params = array(
				Log_Level::code => (isset($code) ? $code : null),
				Log_Level::name => (isset($name) ? $name : null),
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
		if ( $object instanceof Log_Level )
		{
			return parent::deleteObject($object);
		}

		return false;
	}

}

?>
