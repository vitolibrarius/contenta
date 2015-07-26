<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;

class Log_Level extends Model
{
	const TABLE =	'log_level';
	const id =		'id';
	const code =	'code';
	const name =	'name';

	public function tableName() { return Log_Level::TABLE; }
	public function tablePK() { return Log_Level::id; }
	public function sortOrder() { return array( array("desc" => Log_Level::code)); }

	public function allColumnNames()
	{
		return array(Log_Level::id, Log_Level::code, Log_Level::name);
	}

	function logLevelForCode($name)
	{
		return $this->singleObjectForKeyValue( Log_Level::code, $name );
	}
}
