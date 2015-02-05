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
	public function sortOrder() { return array("desc" => array(Log_Level::code)); }

	public function dboClassName() { return 'model\\Log_LevelDBO'; }

	public function allColumnNames()
	{
		return array(Log_Level::id, Log_Level::code, Log_Level::name);
	}

	function logLevels()
	{
		return $this->fetchAll(Log_Level::TABLE, $this->allColumnNames(), null, array(Log_Level::id));
	}

	function logLevelForCode($name)
	{
		return $this->fetch(Log_Level::TABLE, $this->allColumnNames(), array(Log_Level::code => $name));
	}
}
