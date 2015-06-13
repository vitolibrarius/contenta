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
