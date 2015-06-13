<?php

require_once SYSTEM_PATH .'application/libs/db/Qualifier.php';

use \Database as Database;
use \Localized as Localized;
use \Logger as Logger;
use \DataObject as DataObject;
use \Model as Model;

/**
 */
abstract class SQL
{
	const CMD_SELECT	= 'SELECT';
	const CMD_INSERT	= 'INSERT';
	const CMD_UPDATE	= 'UPDATE';
	const CMD_DELETE	= 'DELETE';

	const SQL_FROM		= 'FROM';

	const SQL_WHERE		= 'WHERE';

	const SQL_ORDER		= 'ORDER BY';
	const SQL_ORDER_ASC		= 'ASC';
	const SQL_ORDER_DESC	= 'DESC';

	public $qualifier;

	public static function TableAlias( $idx = 0 )
	{
		$characters = 'abcdefghijklmnopqrstuvwxyz';
		$alias = '';
		if ( intval(floor($idx / 26)) > 0 ) {
			$alias .= $characters[intval(floor($idx / 26)) - 1];
		}
		$alias .= $characters[intval($idx % 26)];
		return $alias;
	}

	public static function Select( Model $model, array $columns = array() )
	{
		return new db\SelectSQL($model, $columns);
	}

	public static function Delete( Model $model, db\Qualifier $qualifier = null )
	{
		return new db\DeleteSQL($model, $qualifier);
	}

	public static function DeleteObject( DataObject $data = null )
	{
		if ( is_null($data) ) {
			throw new \Exception( "You must specify the data to be deleted" );
		}

		return new db\DeleteSQL($data->model(), db\Qualifier::PK($data));
	}


    public function __construct()
    {
    }

	abstract public function sqlParameters();
	abstract public function sqlStatement();

	public function where( db\Qualifier $qualifier )
	{
		$this->qualifier = $qualifier;
	}
}
