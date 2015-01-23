<?php

namespace model;

use \DataObject as DataObject;
use \Model as Model;

class LogLevel extends Model
{
	const TABLE =	'log_level';
	const id =		'id';
	const code =	'code';
	const name =	'name';

	public function tableName() { return LogLevel::TABLE; }
	public function tablePK() { return LogLevel::id; }
	public function sortOrder() { return array("desc" => array(LogLevel::code)); }

	public function allColumnNames()
	{
		return array(LogLevel::id, LogLevel::code, LogLevel::name);
	}

	function logLevels()
	{
		return $this->fetchAll(LogLevel::TABLE, $this->allColumnNames(), null, array(LogLevel::id));
	}

	function logLevelForCode($name)
	{
		return $this->fetch(LogLevel::TABLE, $this->allColumnNames(), array(LogLevel::code => $name));
	}
}
